<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;


class TransactionController extends Controller
{

    public function show($phoneNumber)
    {
        $Transaction = Transaction::where('vcard', $phoneNumber)->get();
        return new TransactionResource($Transaction);
    }
    public function show_specific($id)
    {
        $Transaction = Transaction::find($id);
        return new TransactionResource($Transaction);
    }

    public function update(UpdateTransactionRequest $request, Transaction $v, $id)
    {
        $dataToSave = $request->validated();
        $Transaction = Transaction::find($id);

        $Transaction->category_id = array_key_exists('category_id', $dataToSave) ? $dataToSave['category_id'] : null;
        $Transaction->description = array_key_exists('description', $dataToSave) ? $dataToSave['description'] : null;

        $Transaction->update($dataToSave);
        return new TransactionResource($Transaction);
    }

    public function store(StoreTransactionRequest $request)
    {
        $dataToSave = $request->validated();
        $sender = Auth::id();
        $sender = Vcard::find($sender);
        $reciever = Vcard::where('phone_number', $dataToSave['payment_reference'])->first();

        if ($dataToSave['payment_type'] != "VCARD") {
            // API validation for non vcard operations
            if ($dataToSave['type'] = 'D') {
                $response = Http::post(
                    'https://dad-202324-payments-api.vercel.app/api/credit',
                    [
                        'type' => $dataToSave['payment_type'],
                        'reference' => $dataToSave['payment_reference'],
                        'value' => (float) $dataToSave['value'],
                    ]
                );
            } else {
                $response = Http::post(
                    'https://dad-202324-payments-api.vercel.app/api/debit',
                    [
                        'type' => $dataToSave['payment_type'],
                        'reference' => $dataToSave['payment_reference'],
                        'value' => (float) $dataToSave['value'],
                    ]
                );
            }

            // Check the response status
            if (!$response->successful()) {
                $responseData = $response->json();
                $message = isset($responseData['message']) ? $responseData['message'] : 'Error: API transaction validation failed';
                return response()->json(['message' => $message], $response->status());
            }
        }

        DB::beginTransaction();

        try {
            $transactionSender = new Transaction();

            $transactionSender->vcard = $sender->phone_number;
            $transactionSender->date = date('Y-m-d');
            $transactionSender->datetime = date('Y-m-d H:i:s');
            $transactionSender->type = $dataToSave['type'];
            $transactionSender->value = $dataToSave['value'];
            $transactionSender->old_balance = $sender->balance;
            $transactionSender->new_balance = $sender->balance - floatval($dataToSave['value']);
            $transactionSender->payment_type = $dataToSave['payment_type'];
            $transactionSender->payment_reference = $dataToSave['payment_reference'];

            if ($dataToSave['payment_type'] == "VCARD") {
                $transactionReciever = new Transaction();

                $transactionReciever->vcard = $dataToSave['payment_reference'];
                $transactionReciever->date = date('Y-m-d');
                $transactionReciever->datetime = date('Y-m-d H:i:s');
                $transactionReciever->type = 'C';
                $transactionReciever->value = $dataToSave['value'];
                $transactionReciever->old_balance = $reciever->balance;
                $transactionReciever->new_balance = $reciever->balance + floatval($dataToSave['value']);
                $transactionReciever->payment_type = $dataToSave['payment_type'];
                $transactionReciever->payment_reference = $sender->phone_number;

                $transactionSender->pair_vcard = $dataToSave['payment_reference'];
                $transactionReciever->pair_vcard = $sender->phone_number;
                $transactionSender->pair_transaction = $transactionReciever->id;
                $transactionReciever->pair_transaction = $transactionSender->id;
            }

            $transactionSender->category_id = array_key_exists('category_id', $dataToSave) ? $dataToSave['category_id'] : null;
            $transactionSender->description = array_key_exists('description', $dataToSave) ? $dataToSave['description'] : null;

            if ($dataToSave['payment_type'] == "VCARD") {
                $transactionReciever->category_id = array_key_exists('category_id', $dataToSave) ? $dataToSave['category_id'] : null;
                $transactionReciever->description = array_key_exists('description', $dataToSave) ? $dataToSave['description'] : null;
                $transactionReciever->save();
            }

            $transactionSender->save();

            if ($dataToSave['payment_type'] == "VCARD") {
                $reciever->balance = $reciever->balance + floatval($dataToSave['value']);
                $reciever->save();
            }

            $sender->balance = $sender->balance - floatval($dataToSave['value']);
            $sender->save();

            DB::commit();
        } catch (\Exception $e) {
            // Rollback the transactionSender in case of an exception
            DB::rollBack();
            // Handle or log the exception
            return response()->json(['error' => 'Transaction failed'], 500);
        }
        return new TransactionResource($transactionSender);
    }

    /*public function destroy($phone_number)
    {
        $Transaction = Transaction::find($phone_number);
        $Transaction->delete();
        return response()->json(null, 204);
    }

    public function show_me(Request $request)
    {
        print_r($request . 'teste');
        return new TransactionResource($request->user());
    }*/
}
