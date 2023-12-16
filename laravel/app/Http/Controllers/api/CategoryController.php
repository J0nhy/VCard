<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\UserResource;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /*  public function index(Request $request)
    {
        $userId = optional(Auth::user())->id;

        $categories = Category::where('vcard', $userId)->paginate(10);

        return CategoryResource::collection($categories);
    }*/
    public function index(User $user, Request $request)
    {
        $query = Category::where('vcard', $user->id);

        // Check if the 'search' parameter is present in the request
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like',  $searchTerm . '%');
        }

        $paginator = $request->input('disablePaginator'); // Adjusted to match the axios payload key


        if(!$paginator)
            $categories = $query->paginate(10);
        else
            $categories = $query->get();
        return CategoryResource::collection($categories);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(User $user, Request $request)
    {
        //
        $name = $request->input('name');
        $type = $request->input('type');
        $newCategory = new Category();
        $newCategory->vcard = $user->id;
        $newCategory->name = $name;
        $newCategory->type = $type;
        $newCategory->save();
        return new CategoryResource($newCategory);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, $search)
    {
        $userId = optional(Auth::user())->id;

        $categories = Category::where("vcard", $userId)
            ->where('name', 'like', '%' . $search . '%')
            ->paginate(10);

        return CategoryResource::collection($categories);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, Category $category)
    {
        $category->delete();
        return new CategoryResource($category);
    }
}
