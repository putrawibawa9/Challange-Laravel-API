<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RatingController extends Controller
{
    public function store(Request $request)
    {

          $validator = Validator::make($request->all(), [
        'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        //check if validation fails
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        Rating::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Rating submitted successfully']);
    }

    public function index($productId)
    {
        $ratings = Rating::where('product_id', $productId)->get();

        return response()->json(['message' => 'Ratings retrieved successfully', 'data' => $ratings]);
    }

    public function averageRating($productId)
    {
        $average = Rating::where('product_id', $productId)->avg('rating');

        // Round the ratings to 1 decimal place
        $average = round($average, 1);
        return response()->json(['average_rating' => $average]);
    }
}

