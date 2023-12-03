<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VcardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'phone_number' => $this->phone_number,
            'name' => $this->name,
            'email' => $this->email,
            'photo_url' => $this->photo_url,
            'balance' => $this->balance,
            'max_debit' => $this->max_debit,
            'password' => $this->password,
            'confirmation_code' => $this->confirmation_code,
        ];
    }
}
