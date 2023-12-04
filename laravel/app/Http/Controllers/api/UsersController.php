<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Services\Base64Services;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\ViewAuthUsersResource;
use App\Models\ViewAuthUsers;
use App\Http\Requests\UpdateUserPasswordRequest;


class UsersController extends Controller
{
    private function storeBase64AsFile(User $user, String $base64String)
    {
        $targetDir = storage_path('app/public/fotos');
        $newfilename = $user->id . "_" . rand(1000,9999);
        $base64Service = new Base64Services();
        return $base64Service->saveFile($base64String, $targetDir, $newfilename);
    }

    public function index()
    {
        return UserResource::collection(User::all())
        ;
    }

    public function show($id)
    {
        $user = User::find($id);
        $view = ViewAuthUsers::where('email', $user["email"])->first();
        return new ViewAuthUsersResource($view);
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
        $user->type = 'M';
        $user->gender = $dataToSave['gender'];
        $user->password = bcrypt($dataToSave['password']);

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $user->photo_url = $this->storeBase64AsFile($user, $base64ImagePhoto);
        }
        $user->save();
        return new UserResource($user);
    }


    public function update(UpdateUserRequest $request, User $user)
    {

        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        $deletePhotoOnServer = array_key_exists("deletePhotoOnServer", $dataToSave) && $dataToSave["deletePhotoOnServer"];
        unset($dataToSave["base64ImagePhoto"]);
        unset($dataToSave["deletePhotoOnServer"]);

        $user->setTable('users');
        $user->fill($dataToSave);
        

        // Delete previous photo file if a new file is uploaded or the photo is to be deleted
        if ($user->photo_url && ($deletePhotoOnServer || $base64ImagePhoto)) {
            if (Storage::exists('public/fotos/' . $user->photo_url)) {
                Storage::delete('public/fotos/' . $user->photo_url);
            }
            $user->photo_url = null;
        }

        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $user->photo_url = $this->storeBase64AsFile($user, $base64ImagePhoto);
        }
        $user->save();
        $user->setTable('view_auth_users');
        return new UserResource($user);
    }

    public function update_password(UpdateUserPasswordRequest $request, User $user)
    {
        $user->password = bcrypt($request->validated()['password']);
        $user->save();
        return new UserResource($user);
    }
}