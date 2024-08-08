<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use Akaunting\Money\Currency;
use Akaunting\Money\Money;

class OrderController extends Controller
{
    public function index(){
        // Get all orders
     $orders = Order::latest()->get();

        // Format the total price to IDR
        foreach ($orders as $order) {
            $order->total_price = 'Rp.'. number_format($order->total_price, 0,'','.');
        }
        // Return a collection of $orders
        return new OrderResource('Orders retrieved successfully', $orders);

    }

    public function show($id){
        // Find an order by id
       $order = Order::with('items')->find($id);


        // Check if the order exists
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        // Format the total price to IDR
        $order->total_price = 'Rp.'. number_format($order->total_price, 0,'','.');

        // Return a single order
        return new OrderResource('Order retrieved successfully', [$order]);
    }

    public function buy(Request $request)
    {
        // Validate the request
        $request->validate([
            'id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Find the product
        $product = Product::find($request->id);

        // Check if there's enough stock
        if ($product->stock < $request->quantitiy) {
            return response()->json(['error' => 'Not enough stock available'], 400);
        }

        // Use a database transaction to ensure data integrity
        DB::transaction(function () use ($request, $product) {
            // Create an order
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $product->price * $request->quantity,
            ]);

            // Create an order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);

            // Decrement the product stock
            $product->decrement('stock', $request->quantity);
        });

        $lastOrder = Order::latest()->first();

        // format to USD
        $lastOrder->total_price = 'Rp.'. number_format($lastOrder->total_price, 0,'','.');

        return new OrderResource('Order placed successfully', [$lastOrder]);

        // Return a success response
    }
}
