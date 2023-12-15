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

    public function index(Request $request)
    {
        $query = User::query();  
        // Check if the 'search' parameter is present in the request
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', $searchTerm . '%')
                    ->orWhere('phone_number', 'like', $searchTerm . '%');
        }
        $users = $query->paginate(10);
        return UserResource::collection($users);
    }
    public function show($id)
    {
        $user = User::find($id);
        $view = ViewAuthUsers::where('email', $user["email"])->first();
        return new ViewAuthUsersResource($view);
    }
    public function show_me(Request $request)
    {
        return new UserResource($request->user());
    }

}
