<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\OrderItem;
use Akaunting\Money\Money;
use Illuminate\Http\Request;
use Akaunting\Money\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use App\Models\Payment;
use Illuminate\Support\Facades\Validator;
use App\Services\PaymentService;

class OrderController extends Controller
{
    protected $paymentService;
    public function __construct(PaymentService $paymentService)
    {
         $this->paymentService = $paymentService;
    }

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
      $validator = Validator::make($request->all(), [
        'items' => 'required|array',
        'items.*.product_id' => 'required|',
        'items.*.quantity' => 'required|integer|min:1'
      ]);

    // Check if the validation failed
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        //     var_dump($request->items[0]['product_id']);
        // exit;
    DB::transaction(function () use ($request) {
        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'total_price' => 0 // We'll calculate the total price later
        ]);
        $totalPrice = 0;

        // Iterate over each item in the request
        foreach ($request->items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            // Create an order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $itemData['quantity'],
                'price' => $product->price
            ]);

            // Update the total price
            $totalPrice += $product->price * $itemData['quantity'];
            // Decrement the product stock
            $product->decrement('stock', $itemData['quantity']);
        }

        // Update the order's total price
        $order->update(['total_price' => $totalPrice]);

        // Eager load user and items relation
        $order->load('user', 'items');
    });
    // Retrieve the last order
    $order = Order::with('items')->latest()->first();

    // Format the total price and each product price to IDR
    $order->total_price = 'Rp.'. number_format($order->total_price, 0,'','.');
    foreach ($order->items as $item) {
        $item->price = 'Rp.'. number_format($item->price, 0,'','.');
    }

    $invoice = $this->paymentService->createInvoice($order->total_price);
      return response()->json([
        'message' => 'Order placed successfully',
        'data' => $order,
        'invoice' => $invoice
    ]);
    }
}
