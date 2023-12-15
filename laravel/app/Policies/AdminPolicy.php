<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Admin;

class AdminPolicy
{


    public function create(User $user)
    {
        // Permite que o usuário crie a entidade admin
        return $user->user_type == 'A';
    }

    public function view(User $user, Admin $admin)
    {
        // Permite que o usuário veja a entidade admin
        return $user->user_type == 'A';
    }

    public function update(User $user, Admin $admin)
    {
        // Permite que o usuário edite a entidade admin
        return $user->user_type == 'A';
    }
    public function delete(User $user, Admin $admin)
    {
        // Permite que o usuário delete a entidade admin
        return $user->user_type == 'A';
    }
}
