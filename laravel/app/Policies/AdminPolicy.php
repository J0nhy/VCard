<?php

namespace App\Policies;

use App\Models\User;

class AdminPolicy
{

    public function view(User $user)
    {

        return $user->user_type == 'A';
    }

    public function update(User $user)
    {
        return $user->user_type == 'A';
    }
}
