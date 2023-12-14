<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vcard;

class VcardPolicy
{

    public function view(User $user, Vcard $vcard)
    {
        return $vcard->phone_number == $user->id;
    }

}
