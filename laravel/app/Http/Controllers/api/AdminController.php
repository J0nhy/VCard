<?php

namespace App\Http\Controllers\api;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\AdminResource;
use App\Models\ViewAuthAdmins;
use App\Http\Requests\UpdateAdminRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminPasswordRequest;
use App\Models\User;

class AdminController extends Controller
{
    public function show($id)
    {
        $admin = Admin::find($id);
        return new AdminResource($admin);
    }

    public function update(UpdateAdminRequest $request, Admin $u, $id)
    {
        $admin = Admin::find($id);
        $dataToSave = $request->only(['name', 'email']);

        $admin->update($dataToSave);

        return new AdminResource($admin);
    }

    // REGISTRATION - CREATES A NEW USER
    public function store(StoreAdminRequest $request)
    {
        $dataToSave = $request->validated();

        $base64ImagePhoto = array_key_exists("base64ImagePhoto", $dataToSave) ?
            $dataToSave["base64ImagePhoto"] : ($dataToSave["base64ImagePhoto"] ?? null);
        unset($dataToSave["base64ImagePhoto"]);

        $admin = new Admin();
        $admin->name = $dataToSave['name'];
        $admin->email = $dataToSave['email'];

        $admin->password = bcrypt($dataToSave['password']);




        // Create a new photo file from base64 content
        if ($base64ImagePhoto) {
            $admin->photo_url = $this->storeBase64AsFile($admin, $base64ImagePhoto);
        }
        $admin->save();

        return new AdminResource($admin);
    }
    public function showPassword($id)
    {
        $Admin = new Admin();
        $Admin->id = $id;
        return new AdminResource($Admin);
    }

    public function updatePassword(UpdateAdminPasswordRequest $request, Admin $v, $id)
    {
        $Admin = Admin::find($id);

        $dataToSave = $request->only(['password', 'new_password']);


        if (!empty($dataToSave['password'])) {

            if (!password_verify($dataToSave['password'], $Admin['password'])) {
                return response()->json(['error' => 'Senha atual incorreta'], 401);
            } else {
                $dataToSave['password'] = bcrypt($dataToSave['new_password']);
            }
        } else {
            unset($dataToSave['password']);
        }

        $Admin->update($dataToSave);
        $Admin = new Admin();
        $Admin->id = $id;
        return new AdminResource($Admin);
    }

    public function show_me(Request $request)
    {
        print_r($request . 'teste');
        return new AdminResource($request->user());
    }
}
