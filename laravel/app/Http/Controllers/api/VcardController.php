<?php

namespace App\Http\Controllers\api;

use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\VcardResource;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\StoreVcardRequest;
use App\Services\Base64Services;


class VcardController extends Controller
{

    private function storeBase64AsFile(Vcard $user, String $base64String)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $user->id . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function show($phoneNumber)
    {
        $Vcard = Vcard::find($phoneNumber);
        return new VcardResource($Vcard);
    }

    public function update(UpdateVcardRequest $request, Vcard $u, $phoneNumber)
    {
        $Vcard = Vcard::find($phoneNumber);
        $dataToSave = $request->only(['name', 'email', 'photo_url', 'balance', 'max_debit']); //mudar

        $Vcard->update($dataToSave);

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

        return new VcardResource($vcard);
    }

}
