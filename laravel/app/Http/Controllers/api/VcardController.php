<?php

namespace App\Http\Controllers\api;

use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\VcardResource;
use App\Http\Requests\UpdateVcardRequest;
use App\Http\Requests\UpdateVcardPasswordRequest;
use App\Services\Base64Services;


class VcardController extends Controller
{
    public function show($phone_number)
    {
        $Vcard = Vcard::find($phone_number);
        return new VcardResource($Vcard);
    }

    private function storeBase64AsFile($phone_number, String $base64String)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $phone_number . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function update(UpdateVcardRequest $request, Vcard $v, $phone_number)
    {
        $Vcard = Vcard::find($phone_number);

        $dataToSave = $request->only(['name', 'email', 'photo_url', 'base64ImagePhoto', 'deletePhotoOnServer']);

        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) ?
            $dataToSave["deletePhotoOnServer"] : ($dataToSave["deletePhotoOnServer"] ?? null);

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        if ($base64ImagePhoto) {
            $dataToSave['photo_url'] = $this->storeBase64AsFile($phone_number, $base64ImagePhoto);
        } 

        if($deletePhotoOnServer){
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
        $Vcard = Vcard::find($phone_number);
        
        $dataToSave = $request->only(['password', 'confirmation_code', 'new_password', 'new_confirmation_code']);
        
        
        if(!empty($dataToSave['password'])){
            
            if (!password_verify($dataToSave['password'], $Vcard['password'])) {
                return response()->json(['error' => 'Senha atual incorreta'], 401);
            } else {
                $dataToSave['password'] = bcrypt($dataToSave['new_password']);
            }
        } else {
            unset($dataToSave['password']);
        }

        if(!empty($dataToSave['confirmation_code'])){
            
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

}
