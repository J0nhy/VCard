<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vcard;

class CategoryPolicy
{
    /**
     * Create a new policy instance.
     */

     public function view(User $user, Vcard $vcard)
    {
        return $vcard->phone_number == $user->id;
    }

    public function index(User $user, Vcard $vcard)
    {
        return $vcard->phone_number == $user->id;
    }
}
