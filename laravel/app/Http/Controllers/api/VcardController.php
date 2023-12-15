<?php

namespace App\Http\Controllers\api;

use App\Models\Vcard;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\VcardResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\TransactionResource;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\StoreVcardRequest;
use App\Http\Requests\UpdateVcardPasswordRequest;
use App\Services\Base64Services;
use App\Models\DefaultCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\DeviatesCastableAttributes;

use function Psy\debug;

class VcardController extends Controller
{

    public function index(Request $request)
    {
        $query = Vcard::withTrashed();

        // Check if the 'search' parameter is present in the request
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', $searchTerm . '%')
                    ->orWhere('phone_number', 'like', $searchTerm . '%');
        }

        $users = $query->paginate(10);

        return VcardResource::collection($users);
    }

    public function show(Vcard $vcard)
    {
        return new VcardResource($vcard);
    }

    public function search($name)
    {
        $vcards = Vcard::withTrashed()
            ->where(function ($query) use ($name) {
                $query->where('name', 'like', $name . '%')
                    ->orWhere('phone_number', 'like', $name . '%');
            })
            ->paginate(10);



        return VcardResource::collection($vcards);
    }

    private function storeBase64AsFile($phone_number, String $base64String)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $phone_number . "_" . rand(1000, 9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }
    public function updateByAdmin(Request $request,$phoneNumber)
    {
        $newMaxDebit = $request->input('newMaxDebit'); 
        $block = $request->input('block');
        $Vcard = Vcard::find($phoneNumber);

        if($newMaxDebit){
            //se for menor n muda
            if ($newMaxDebit < $Vcard->balance) return new VcardResource($Vcard);
            $Vcard->max_debit = $newMaxDebit;
            $Vcard->save();
        }
        else{
            if ($Vcard->blocked == 0) {

                $Vcard->blocked = 1;
            } else {
                $Vcard->blocked = 0;
            }
            $Vcard->save();
        }
        return new VcardResource($Vcard);
    }


    public function update(UpdateVcardRequest $request, Vcard $v, $phone_number)
    {
        $phone_number = $request->get('phone_number');

        $Vcard = Vcard::find($request->get('phone_number'));

        $dataToSave = $request->only(['name', 'email', 'photo_url', 'base64ImagePhoto', 'deletePhotoOnServer']);

        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) ?
            $dataToSave["deletePhotoOnServer"] : ($dataToSave["deletePhotoOnServer"] ?? null);

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        if ($base64ImagePhoto) {
            $dataToSave['photo_url'] = $this->storeBase64AsFile($phone_number, $base64ImagePhoto);
        }

        if ($deletePhotoOnServer) {
            $dataToSave['photo_url'] = "";
        }

        $Vcard->update($dataToSave);
        return new VcardResource($Vcard);
    }

    public function showPassword($phone_number)
    {
        $Vcard = new Vcard();
        $Vcard->phone_number = $phone_number;
        return new VcardResource($Vcard);
    }

    public function updatePassword(UpdateVcardPasswordRequest $request, Vcard $v, $phone_number)
    {
        $Vcard = Vcard::find($request->get('phone_number'));

        if (!$Vcard) {
            return response()->json(['error' => 'Usuário não encontrado'], 401);
        }

        $dataToSave = $request->only(['password', 'confirmation_code', 'new_password', 'new_confirmation_code']);


        if (!empty($dataToSave['password'])) {

            if (!password_verify($dataToSave['password'], $Vcard['password'])) {
                return response()->json(['error' => 'Senha atual incorreta'], 401);
            } else {
                $dataToSave['password'] = bcrypt($dataToSave['new_password']);
            }
        } else {
            unset($dataToSave['password']);
        }

        if (!empty($dataToSave['confirmation_code'])) {

            if (!password_verify($dataToSave['confirmation_code'], $Vcard['confirmation_code'])) {
                return response()->json(['error' => 'Código de confirmação incorreto'], 401);
            } else {
                $dataToSave['confirmation_code'] = bcrypt($dataToSave['new_confirmation_code']);
            }
        } else {
            unset($dataToSave['confirmation_code']);
        }

        $Vcard->update($dataToSave);
        $Vcard = new Vcard();
        $Vcard->phone_number = $phone_number;
        return new VcardResource($Vcard);
    }

    public function store(StoreVcardRequest $request)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        $vcard = new Vcard();
        $vcard->phone_number = $dataToSave['phone_number'];
        $vcard->name = $dataToSave['name'];
        $vcard->email = $dataToSave['email'];


        $vcard->confirmation_code = bcrypt($dataToSave['confirmation_code']);
        $vcard->password = bcrypt($dataToSave['password']);
        $vcard->blocked = 0;

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $vcard->photo_url = $this->storeBase64AsFile($vcard, $base64ImagePhoto);
        }
        $vcard->save();

        // Associar categorias padrão ao vcard com type 'D'
        $defaultCategories = DefaultCategory::all();

        // Mapear apenas os nomes das categorias
        $categoryNames = $defaultCategories->pluck('name')->toArray();

        // Adicionar o valor 'D' para a coluna type
        $categoryData = array_fill_keys($categoryNames, ['type' => 'D']);

        $vcard->categories()->sync($categoryData);

        return new VcardResource($vcard);
    }

    public function destroy($phone_number)
    {


        $Vcard = Vcard::find($phone_number);

        $vcardUser = $Vcard;
        if (!$Vcard) {
            return response()->json(['error' => 'Usuário não encontrado'], 404);
        }

        if ($Vcard->balance > 0) {
            return response()->json(['error' => 'Usuário com saldo positivo'], 401);
        }

        if ($Vcard->transactions()->exists()) {
            // Soft delete se houver transações associadas
            $Vcard->delete();
        } else {
            // Delete normal se não houver transações
            $Vcard->forceDelete();
        }


        return new VcardResource($vcardUser);
    }

    public function show_me(Request $request)
    {
        return new VcardResource($request->user());
    }

    public function ActivatePiggy(Request $request, Vcard $vcard)
    {
        $vcard->custom_data = 0.00;
        $vcard->save();

        return new VcardResource($vcard);
    }

    public function add(Request $request, Vcard $vcard)
    {
        $value = $request->input('valor');

        if ($vcard->balance < floatval($value)) {
            return response()->json(['error' => 'Saldo insuficiente'], 401);
        }

        $vcard->update([
            'custom_data' => floatval($vcard->custom_data) + floatval($value),
            'balance' => floatval($vcard->balance) - floatval($value),
        ]);

        return new VcardResource($vcard);
    }

    public function remove(Request $request, Vcard $vcard)
    {
        $value = $request->input('valor');

        if (floatval($value) > floatval($vcard->custom_data)) {
            return response()->json(['error' => 'Saldo insuficiente'], 401);
        }

        $vcard->update([
            'custom_data' => floatval($vcard->custom_data) - floatval($value),
            'balance' => floatval($vcard->balance) + floatval($value),
        ]);

        return new VcardResource($vcard);
    }

    public function getVcardTransactions(Vcard $vcard)
    {
        $transactions = Transaction::where('vcard', $vcard->phone_number)->get();
        return TransactionResource::collection($transactions);
    }
}
