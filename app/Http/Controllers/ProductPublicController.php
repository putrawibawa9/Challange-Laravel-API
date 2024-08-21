<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;

class ProductPublicController extends Controller
{
     public function index(){
       $products = Product::orderBy('created_at', 'desc')->paginate(5); // 10 is the number of items per page
        return new ProductResource('Products retrieved successfully', $products);
    }

     public function show( $slug)
    {
        $product = Product::where('slug', $slug)->first();
        // return if there is no such data
        if (!$product) {
           abort(404, 'Product not found');
        }
        return new ProductResource('Product retrieved successfully', $product);
    }
}
