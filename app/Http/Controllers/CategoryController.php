<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    // check the user's token ability
         $user = Auth::user();
        // check if the token is valid
           if (!$user->tokenCan('read')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Retrieve all categories with their products
        $categories = Category::paginate(5);

        return new  CategoryResource('Categories retrieved successfully', $categories);

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

            // check if user has ability to create
        if (!Auth::user()->tokenCan('create')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        //define validation rules
        $validator = Validator::make($request->all(), [
        'name'       => 'required|string|max:255',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        // add new category
        $category = Category::create([
            'name' => $request->name,
        ]);

        return new CategoryResource('Category created successfully', $category);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // check if the token is valid
        if (!Auth::user()->tokenCan('read')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Retrieve a single category with its products
        $category = Category::with('products')->find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        return new CategoryResource('Category retrieved successfully', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // check if user has ability to update
        if (!Auth::user()->tokenCan('update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // update category
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        //define validation rules
        $validator = Validator::make($request->all(), [
            'name'       => 'required|string|max:255',
        ]);
        // check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // update category
        $category->update([
            'name' => $request->name,
        ]);
        return new CategoryResource('Category updated successfully', $category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //check if user has ability to delete
        if (!Auth::user()->tokenCan('delete')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // delete category
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
