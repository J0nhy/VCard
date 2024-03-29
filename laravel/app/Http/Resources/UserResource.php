<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $this->load('vcard');

        return [
            'id' => $this->id,
            'user_type' => $this->user_type,
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'photo_url' => $this->photo_url,
            'balance' => $this->vcard ? $this->vcard->balance : null,
            'custom_data' => $this->vcard ? $this->vcard->custom_data : null,
        ];
    }
}
