<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Mail\SampleEmail;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           $user = Auth::user();
        // check if the token is valid
           if (!$user->tokenCan('admin') && !$user->tokenCan('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
       $products = Product::orderBy('created_at', 'desc')->paginate(5); // 10 is the number of items per page
        return new ProductResource('Products retrieved successfully', $products);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
          // check if user has ability to create


        //define validation rules
        $validator = Validator::make($request->all(), [
        'name'       => 'required|string|max:255',
        'description'     => 'required|string',
        'price'      => 'required|numeric',
        'stock' => 'required|numeric',
        'category_id' => 'required|numeric',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //create Post
       $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price ,
             'stock' => $request->stock,
             'category_id' => $request->category_id,
        ]);

        //return success response
        return response()->json([
            'code' => '200',
            'message' => 'Product created successfully',
            'data' => $product,
        ]);
 
    }

    /**
     * Display the specified resource.
     */
    public function show( $slug)
    {
        // check if the token is valid
        if (!Auth::user()->tokenCan('admin') && Auth::user()->tokenCan('user')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $product = Product::where('slug', $slug)->first();
        // return if there is no such data
        if (!$product) {
           abort(404, 'Product not found');
        }
        return new ProductResource('Product retrieved successfully', $product);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $slug)
    {
        // Find the product by slug
        $product = Product::where('slug', $slug)->firstOrFail();

        // Check if the token is valid
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Update product details
        $product->fill($request->all());

        // Check if the name (or relevant field) has changed
        if ($request->has('name') && $product->isDirty('name')) {
            $product->slug = $this->generateUniqueSlug($request->input('name'));
        }

        $product->save();

        return response()->json(['message' => 'Product updated successfully', 'product' => new ProductResource('Product has changed',$product)]);
    }

    protected function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $i = 1;

        // Ensure uniqueness
        while (Product::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $i;
            $i++;
        }

        return $slug;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
      

        // check if the token is valid
        if (!Auth::user()->tokenCan('admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
          // search the data on database
        $product = Product::where('slug', $slug)->first();

        // return if there is no such data
        if (!$product) {
            abort(404, 'Product not found');
        }
        // delete the data
        $product->delete();
        return response()->json([
            'code' => '200',
            'message' => 'Product deleted successfully',
        ]);
    }
}
