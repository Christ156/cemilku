<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'payment_method' => 'required|in:BCA,CimbNiaga,Danamon,Mandiri',
        ]);


        // $userId = 2;
        $userId = Auth::user()->id;

        $cart = Cart::where('user_id', $userId)->where('is_active', 1)->first();
        // dd($cart);


        if (! $cart) {
            return redirect()->back()->with('error', 'Keranjang tidak ditemukan.');
        }

        $cartItems = $cart->cartItems;
        // dd($cartItems->toArray());
        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang kosong.');
        }

        DB::beginTransaction();

        try {
            $total = $cartItems->sum('total_price');
            $order = Order::create([
                'user_id'        => $userId,
                'total_price'    => $total,
                'payment_method' => $request->payment_method,
                'status'         => 'pending',
                'created_at'     => Carbon::now(),
                'updated_at'     => Carbon::now(),
            ]);
            // dd($order);

            foreach ($cartItems as $item) {
                OrderDetail::create([
                    'order_id'      => $order->id,
                    'collection_id' => $item->collection_id,
                    'customize_id'  => $item->customize_id,
                    'quantity'      => $item->quantity,
                    'price'         => $item->price,
                ]);
            }

            $cart->update(['is_active' => false]);
            // dd($order, $cartItems);
            DB::commit();

            return redirect()->route('orders.index', $order->id)->with('success', 'Checkout berhasil!');
        } catch (\Exception $e) {
            DB::rollBack();
            dd('Checkout gagal bangg: ', $e->getMessage(), $e->getTraceAsString());
            return redirect()->back()->with('error', 'Checkout gagal: ' . $e->getMessage());
        }

    }

}
