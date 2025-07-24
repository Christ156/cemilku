<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    public function index()
    {
        if (!Auth::check()) {
            return redirect('/login')->with('error', 'Please log in to view your cart.');
        }

        $user = Auth::user();
        // dd($user);

        $cart = Cart::with('cartItems.collection')
                    ->where('user_id', $user->id)
                    ->where('is_active', 1)
                    ->first();

        // dd($cart->cartItems);
        return view('keranjang', compact('cart'));
    }
}
