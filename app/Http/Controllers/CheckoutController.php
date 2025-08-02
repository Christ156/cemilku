<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Tambahkan ini untuk debugging

class CheckoutController extends Controller
{
    public function index($order_id){
        Log::info("DEBUG: CheckoutController@index called with order_id: {$order_id}");
        $order = Order::find($order_id);

        if (!$order) {
            Log::warning("DEBUG: Order with ID {$order_id} not found in CheckoutController@index. Redirecting to cart.");
            // Redirect ke halaman error atau kembali ke keranjang jika order tidak ditemukan
            // Pastikan Auth::user() tersedia di sini.
            // Anda mungkin perlu menangani kasus di mana pengguna belum login, tetapi asumsi ini adalah rute terotentikasi.
            $userSlug = Auth::check() ? Str::slug(Auth::user()->name) : 'guest';
            $userId = Auth::check() ? Auth::user()->id : 0; // Ganti 0 dengan ID user default jika diperlukan
            return redirect()->route('cart.index', ['id_user' => $userId, 'slug' => $userSlug])->withErrors(['error' => 'Order tidak ditemukan atau sudah tidak valid.']);
        }

        return \view('checkout', \compact('order'));
    }

    /*
    // --------------- PENTING: METHOD STORE INI DIKOMENTARI / DIHAPUS ---------------
    // Logika checkout sudah dipindahkan ke CartController@checkout
    // Mengaktifkan kembali method ini akan menyebabkan duplikasi dan masalah alur.
    // Jika Anda ingin menggunakan ini untuk alur checkout lain (misalnya pembayaran langsung tanpa keranjang),
    // pastikan alur panggilannya berbeda dan tidak tumpang tindih dengan CartController.

    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'payment_method' => 'required|in:BCA,CimbNiaga,Danamon,Mandiri',
        ]);

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
    */
}
