<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CategoryResource;

class CategoryPublicController extends Controller
{
    public function index()
    {
        // Retrieve all categories with their products
        $categories = Category::paginate(5);

        return new  CategoryResource('Categories retrieved successfully', $categories);

    }

      public function show($slug)
    {
        // Retrieve a single category with its products
        $category = Category::with('products')->where('slug', $slug)->first();
        if (!$category) {
          abort(404, 'Category not found');
        }
        return new CategoryResource('Category retrieved successfully', $category);
    }
}
