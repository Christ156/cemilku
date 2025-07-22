<?php

namespace App\Http\Controllers;


use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $collection = Collection::find($validated['collection_id']);

        if (!$collection) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Validasi stok awal saat menambahkan item baru
        if ($collection->stock < $validated['quantity']) {
            return response()->json(['message' => 'Requested quantity exceeds available stock.'], 400);
        }

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id, 'is_active' => true],
            ['user_id' => $user->id, 'is_active' => true]
        );

        $existingItem = $cart->cartItems()->where('collection_id', $collection->id)->first();

        if ($existingItem) {
            $newQuantity = $existingItem->quantity + $validated['quantity'];

            // Validasi stok saat memperbarui kuantitas item yang sudah ada
            if ($collection->stock < $newQuantity) {
                return response()->json(['message' => 'Adding this quantity would exceed available stock.'], 400);
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'total_price' => $collection->price * $newQuantity,
            ]);
        } else {
            $cart->cartItems()->create([
                'collection_id' => $collection->id,
                'quantity' => $validated['quantity'],
                'price' => $collection->price,
                'total_price' => $collection->price * $validated['quantity'],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Item added to cart successfully!',
            'cart_item' => $existingItem ?? $cart->cartItems()->where('collection_id', $collection->id)->first(),
        ], 200);
    }

    public function updateQuantity(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'collection_id' => 'required|exists:collections,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $cart = Cart::where('user_id', $user->id)->where('is_active', true)->first();

        if (!$cart) {
            return response()->json(['message' => 'Active cart not found for user.'], 404);
        }

        $cartItem = $cart->cartItems()->where('collection_id', $validated['collection_id'])->first();

        if (!$cartItem) {
            return response()->json(['message' => 'Cart item not found.'], 404);
        }

        $collection = Collection::find($validated['collection_id']);
        if (!$collection) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        // Validasi stok saat memperbarui kuantitas item
        if ($collection->stock < $validated['quantity']) {
            return response()->json(['message' => 'Requested quantity exceeds available stock.'], 400);
        }

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'total_price' => $collection->price * $validated['quantity'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Quantity updated successfully!',
            'cart_item' => $cartItem,
        ]);
    }

    public function removeItems(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['message' => 'Unauthorized. Please log in.'], 401);
        }

        $user = Auth::user();

        $validated = $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'exists:collections,id',
        ]);

        $cart = Cart::where('user_id', $user->id)->where('is_active', true)->first();

        if (!$cart) {
            return response()->json(['message' => 'Active cart not found for user.'], 404);
        }

        $deletedCount = $cart->cartItems()->whereIn('collection_id', $validated['item_ids'])->delete();

        return response()->json([
            'status' => 'success',
            'message' => "{$deletedCount} items removed from cart successfully!",
        ]);
    }

    // Menampilkan cart yang disimpan di database
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please log in to view your cart.');
        }

        $user = Auth::user();

        $cart = Cart::with('cartItems.collection')
                    ->where('user_id', $user->id)
                    ->where('is_active', true)
                    ->first();

        // Memproses cartItems di controller untuk menyertakan 'type' dan 'stock'
        if ($cart) {
            $cartItems = $cart->cartItems->map(function($item) {
                $collection = $item->collection;
                return [
                    'id' => $item->collection_id,
                    'name' => $collection->name ?? 'Unknown Product',
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'total_price' => $item->total_price,
                    'image' => asset('assets/collections/' . ($collection->image ?? 'placeholder.png')),
                    'description' => $collection->description ?? '',
                    'type' => $collection->type ?? '',
                    'stock' => $collection->stock ?? 0, // <--- BARIS INI DITAMBAHKAN
                    'selected' => true
                ];
            })->toArray();
        } else {
            $cartItems = [];
        }

        return view('cart', compact('cartItems'));
    }
}
