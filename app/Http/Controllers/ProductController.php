<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ProductResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\SampleEmail;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
           $user = Auth::user();
        // check if the token is valid
           if (!$user->tokenCan('read')) {
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
        if (!Auth::user()->tokenCan('create')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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
    public function show(Product $product)
    {
        // check if the token is valid
        if (!Auth::user()->tokenCan('read')) {
            return response()->json(['message' => 'Unauthorized'], 403);
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
    public function update(Request $request, Product $product)
    {
        // check if the token is valid
        if (!Auth::user()->tokenCan('update')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

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

        //update Post
        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);

        //return success response
        return response()->json([
            'code' => '200',
            'message' => 'Product updated successfully',
            'data' => $product,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // search the data on database
        $product = Product::find($id);

        // check if the token is valid
        if (!Auth::user()->tokenCan('delete')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        // delete the data
        $product->delete();
        return response()->json([
            'code' => '200',
            'message' => 'Product deleted successfully',
        ]);
    }
}
