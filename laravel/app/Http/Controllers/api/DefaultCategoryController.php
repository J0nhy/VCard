<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DefaultCategoryResource;
use App\Models\DefaultCategory;
use Illuminate\Http\Request;

class DefaultCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = DefaultCategory::query(); // Initialize the query builder
    
        // Check if the 'search' parameter is present in the request
        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where('name', 'like', $searchTerm . '%');
        }
    
        $categories = $query->paginate(10);
    
        return DefaultCategoryResource::collection($categories);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($search)
    {
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
    public function destroy(DefaultCategory $category)
    {
        $category->delete();
        return new DefaultCategoryResource($category);

    }
}
