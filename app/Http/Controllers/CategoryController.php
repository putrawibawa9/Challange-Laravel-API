<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Support\Str;
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
           if (!$user) {
        return response()->json(['message' => 'Token not provided or invalid'], 401);
    }
        // check if the token is valid
           if (!$user->tokenCan('admin') && !$user->tokenCan('user')) {
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
        if (!Auth::user()->tokenCan('admin')) {
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
    public function show($slug)
    {
        // check if the token is valid
        if (!Auth::user()->tokenCan('admin') && Auth::user()->tokenCan('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // Retrieve a single category with its products
        $category = Category::with('products')->where('slug', $slug)->first();
        if (!$category) {
          abort(404, 'Category not found');
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
    public function update(Request $request, string $slug)
    {
        // Check if the user has the ability to update
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Find the category by slug
        $category = Category::where('slug', $slug)->firstOrFail();

        // Define validation rules
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Update category details
        $category->fill($request->all());

        // Check if the name (or relevant field) has changed
        if ($request->has('name') && $category->isDirty('name')) {
            $category->slug = $this->generateUniqueSlug($request->input('name'));
        }

        $category->save();

        return new CategoryResource('Category updated successfully', $category);
    }

    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $i = 1;

        // Ensure uniqueness
        while (Category::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        //check if user has ability to delete
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // delete category
        $category = Category::where('slug', $slug)->first();
        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
