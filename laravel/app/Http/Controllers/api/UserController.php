<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Models\ViewAuthUsers;
use App\Http\Requests\UpdateUserRequest;



class UserController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        return new UserResource($user);
    }

    public function update(UpdateUserRequest $request, User $u, $id)
    {
        $user = User::find($id);
        $dataToSave = $request->only(['name', 'email']);

        $user->update($dataToSave);

        return new UserResource($user);
    }

}
