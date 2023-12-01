<?php

namespace App\Http\Controllers\api;

use App\Models\Vcard;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\VcardResource;
use App\Http\Requests\UpdateVcardRequest;


class VcardController extends Controller
{
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

}
