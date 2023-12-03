<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\UserResource;
use App\Models\ViewAuthUsers;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;



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

    // REGISTRATION - CREATES A NEW USER
    public function store(StoreUserRequest $request)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        $user = new User();
        $user->name = $dataToSave['name'];
        $user->email = $dataToSave['email'];

        $user->password = bcrypt($dataToSave['password']);

        $user->setTable('users');


        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $user->photo_url = $this->storeBase64AsFile($user, $base64ImagePhoto);
        }
        $user->save();
        $user->setTable('view_auth_users');

        return new UserResource($user);
    }

}
