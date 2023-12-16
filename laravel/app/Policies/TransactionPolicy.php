<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vcard;
use App\Models\Transaction;

class TransactionPolicy
{
    public function view(User $user, Vcard $vcard, Transaction $transaction)
    {
        return $transaction->vcard == $vcard->phone_number;
    }

    public function index(User $user, Vcard $vcard, Transaction $transaction)
    {
        return $transaction->vcard == $vcard->phone_number == $user->id;
    }
}
