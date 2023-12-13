<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CreditTransactionResource extends JsonResource
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
                'id' => $this->id,
                'vcard' => $this->vcard,
                'date' => $this->date,
                'datetime' => $this->datetime,
                'type' => $this->type,
                'value' => $this->value,
                'old_balance' => $this->old_balance,
                'new_balance' => $this->new_balance,
                'payment_type' => $this->payment_type,
                'payment_reference' => $this->payment_reference,
                'category_id' => $this->category_id,
                'description' => $this->description,
        ];
    }
}
