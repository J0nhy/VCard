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
use App\Http\Requests\UpdateUserPasswordRequest;



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
    public function showPassword($id)
    {
        $User = new User();
        $User->id = $id;
        return new UserResource($User);
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $v, $id)
    {
        $User = User::find($id);

        $dataToSave = $request->only(['password', 'new_password']);


        if (!empty($dataToSave['password'])) {

            if (!password_verify($dataToSave['password'], $User['password'])) {
                return response()->json(['error' => 'Senha atual incorreta'], 401);
            } else {
                $dataToSave['password'] = bcrypt($dataToSave['new_password']);
            }
        } else {
            unset($dataToSave['password']);
        }

        $User->update($dataToSave);
        $User = new User();
        $User->id = $id;
        return new UserResource($User);
    }
}
