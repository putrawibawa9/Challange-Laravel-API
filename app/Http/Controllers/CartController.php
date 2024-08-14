<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Display a listing of the cart items.
     */
    public function index()
    {
        // Get the authenticated user
        $user = Auth::user();

        // Retrieve the user's cart and associated items
        $cart = Cart::with('cartItems.product')->where('user_id', $user->id)->first();

        // Return the cart items
        return response()->json($cart);
    }

    /**
     * Store a newly created product in the cart (wish-list).
     */
   public function store(Request $request)
{
   
    // Validate the request
    $request->validate([
        'items' => 'required|array',
        'items.*.product_id' => 'required',
        'items.*.quantity' => 'required|integer|min:1',
    ]);

    // if validation fails

    // Get the authenticated user
    $user = Auth::user();

    // Find or create a cart for the user
    $cart = Cart::firstOrCreate(['user_id' => $user->id]);

    // Iterate over the products array
    foreach ($request->input('items') as $product) {
        $productId = $product['product_id'];
        $quantity = $product['quantity'];

        // Check if the product is already in the cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Update the quantity if the product is already in the cart
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            // Add the product to the cart if it's not already there
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);
        }
    }

    return response()->json(['message' => 'Products added to cart successfully!'], 200);
}

    /**
     * Update the specified cart item.
     */

  public function update(Request $request, $cartItemId)
{

    // Validate the request
    $request->validate([
        'quantity' => 'required|integer|min:1',
    ]);

    // Get the authenticated user
    $user = Auth::user();

    // Find the cart item
    $cartItem = CartItem::where('id', $cartItemId)
        ->whereHas('cart', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->firstOrFail();

       

    // Update the quantity
    $cartItem->quantity = $request->input('quantity');
    $cartItem->save();

    return response()->json(['message' => 'Cart item updated successfully!', 'cartItem' => $cartItem], 200);
}

    /**
     * Remove the specified cart item from the cart.
     */
    public function destroy($id)
    {
        // Check if the cart item belong to the authenticated user
        $user = Auth::user();
        $cartItem = CartItem::where('id', $id)
            ->whereHas('cart', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->firstOrFail();
        // Delete the cart item
        $cartItem->delete();

        return response()->json(['message' => 'Cart item removed successfully!'], 200);
    }
}
