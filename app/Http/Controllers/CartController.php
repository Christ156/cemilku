<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\Customize;
use App\Models\MysteryBox;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log; // Pastikan ini di-import

class CartController extends Controller
{
    public function index($id_user, $slug)
    {
        $id_user = Auth::user()->id;
        $cartQuery = Cart::where('user_id', $id_user)->where('is_active', 1);

        if ($cartQuery->count() == 0) {
            Cart::insert(['user_id' => $id_user, 'is_active' => 1, 'created_at' => now(), 'updated_at' => now()]);
        }

        $id_cart = $cartQuery->first()->id;

        $carts = CartItem::where('cart_id', $id_cart)->get();
        $address_active = Address::where('user_id', $id_user)->where('is_primary', 1)->first();
        $count_address_active = Address::where('user_id', $id_user)->where('is_primary', 1)->count();
        $address = Address::where('user_id', $id_user)->where('is_primary', 0)->get();

        // --- Calculate Total Price for the view ---
        $totalPriceFromItems = $carts->sum('total_price');
        $shippingCost = 20000; // Define your shipping cost here
        $finalCartTotal = $totalPriceFromItems + $shippingCost;

        // Format the total for display, e.g., "Rp 1.049.000"
        $formattedFinalCartTotal = 'Rp ' . number_format($finalCartTotal, 0, ',', '.');
        // --- End Calculation ---

        return view('cart', \compact(['carts', 'address_active', 'count_address_active', 'address', 'formattedFinalCartTotal']));
    }

    // Metode private create_new_order tidak lagi diperlukan karena logikanya diintegrasikan ke dalam checkout.
    // private function create_new_order($id_user, $total_price, $payment_method) { ... }

    public function checkout(Request $request, $id_user_param, $slug) // Menggunakan $id_user_param untuk parameter rute
    {
        $user = Auth::user();
        $id_user = $user->id; // Selalu gunakan ID pengguna yang terotentikasi untuk keamanan

        // 1. Validasi awal untuk metode pembayaran
        try {
            $request->validate([
                'payment_method' => 'required|in:BCA,Mandiri,Cimb Niaga,Danamon',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        // 2. Dapatkan keranjang aktif pengguna
        $cart = Cart::where('user_id', $id_user)->where('is_active', true)->first();

        if (!$cart) {
            return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Keranjang kosong atau tidak aktif.']);
        }

        // 3. Filter item-item yang *dipilih* di keranjang untuk checkout
        $selectedCartItems = $cart->cartItems()->whereIn('id', collect($request->input())->keys()->map(function($key) {
            return Str::after($key, 'item_cart_');
        })->filter(function($id) use ($request) {
            return is_numeric($id) && $request->has('item_cart_' . $id) && $request->input('item_cart_' . $id) === 'true';
        }))->get();

        if ($selectedCartItems->isEmpty()) {
            return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Tidak ada item yang dipilih untuk checkout.']);
        }

        // 4. Dapatkan alamat pengiriman utama yang aktif
        $address = Address::where('user_id', $id_user)->where('is_primary', true)->first();

        if (!$address) {
            return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Harap atur alamat pengiriman utama sebelum checkout.']);
        }

        // 5. --- VALIDASI KETERSEDIAAN STOK (KUNCI UNTUK MEMBUAT TES LULUS & APLIKASI AMAN) ---
        $unavailableItems = [];
        foreach ($selectedCartItems as $item) {
            $product = null;
            if ($item->collection_id) {
                $product = Collection::find($item->collection_id);
            } elseif ($item->customize_id) {
                $product = Customize::find($item->customize_id);
            } elseif ($item->mysterybox_id) {
                $product = MysteryBox::find($item->mysterybox_id);
            }

            if (!$product || !isset($product->stock) || $item->quantity > $product->stock) {
                $name = $product ? $product->name : 'Produk Tidak Ditemukan';
                $stock = $product && isset($product->stock) ? $product->stock : 0;
                $unavailableItems[] = "{$name} (Stok tersedia: {$stock}, Diminta: {$item->quantity})";
            }
        }

        if (!empty($unavailableItems)) {
            // Jika ada item yang tidak tersedia, **redirect kembali ke halaman keranjang dengan error**
            return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug($user->name)])
                             ->withErrors(['stock_error' => 'Beberapa item tidak tersedia dalam jumlah yang diminta: ' . implode(', ', $unavailableItems)]);
        }

        // 6. --- MULAI TRANSAKSI DATABASE (PENTING UNTUK KONSISTENSI DATA) ---
        DB::beginTransaction();
        try {
            // Hitung total harga dari item yang dipilih (plus biaya pengiriman)
            $totalPriceFromSelectedItems = $selectedCartItems->sum('total_price');
            $shippingCost = 20000; // Pastikan ini konsisten dengan logika Anda
            $finalTotalPrice = $totalPriceFromSelectedItems + $shippingCost;

            // 7. Buat order baru (INI DILAKUKAN SETELAH SEMUA VALIDASI LOLOS)
            $order = Order::create([
                'user_id' => $id_user,
                'total_price' => $finalTotalPrice,
                'payment_method' => $request->input('payment_method'),
                'address_id' => $address->id,
                'order_date' => now(),
                'invoice_id' => 'INV-' . Str::upper(Str::random(8)), // Generate invoice ID
                'status' => 'pending', // Status awal order
                'delivery_date' => null,
                'delivery_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // 8. Buat order details dan kurangi stok untuk setiap item yang dipilih
            foreach ($selectedCartItems as $cartItem) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'collection_id' => $cartItem->collection_id,
                    'customize_id' => $cartItem->customize_id,
                    'mysterybox_id' => $cartItem->mysterybox_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->total_price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Kurangi stok dari produk yang relevan
                if ($cartItem->collection_id) {
                    Collection::find($cartItem->collection_id)->decrement('stock', $cartItem->quantity);
                } elseif ($cartItem->customize_id) {
                    Customize::find($cartItem->customize_id)->decrement('stock', $cartItem->quantity);
                } elseif ($cartItem->mysterybox_id) {
                    MysteryBox::find($cartItem->mysterybox_id)->decrement('stock', $cartItem->quantity);
                }

                // Hapus item dari keranjang setelah berhasil diproses ke order_detail
                $cartItem->delete();
            }

            // 9. Nonaktifkan keranjang jika semua itemnya sudah diproses
            if ($cart->cartItems()->count() == 0) {
                 $cart->is_active = false;
                 $cart->save();
            }

            DB::commit(); // Konfirmasi semua perubahan database

            // 10. Redirect ke halaman detail order/sukses setelah semua proses selesai
            return redirect()->route('checkout.index', ['order_id' => $order->id])->with('success', 'Checkout berhasil!');

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan semua perubahan jika ada error
            Log::error("Checkout failed for user {$id_user}: " . $e->getMessage() . "\n" . $e->getTraceAsString()); // Log detail error
            return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug($user->name)])
                             ->withErrors(['checkout_error' => 'Terjadi kesalahan saat checkout. Silakan coba lagi.']);
        }
    }

    public function destroy(Request $request, $id_user, $slug, $count_items)
    {
        $id_user = Auth::user()->id;
        $id_cart = Cart::where('user_id', $id_user)->where('is_active', 1)->first()->id;
        $carts = CartItem::where('cart_id', $id_cart)->get();

        foreach ($carts as $cartItem) {
            if ($request->input('cart_item_' . $cartItem->id)) {
                $cartItem->delete();
            }
        }

        if (CartItem::where('cart_id', $id_cart)->count() == 0) {
            $cart = Cart::find($id_cart);
            if ($cart) {
                $cart->is_active = 0;
                $cart->save();
            }
        }

        return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => Str::slug(Auth::user()->name)]);
    }

    private function checkPrimaryAddress($id_user)
    {
        $count = Address::where('user_id', $id_user)->where('is_primary', 1)->count();
        return $count > 0;
    }

    public function store_address(Request $request, $id_user, $slug)
    {
        $id_user = Auth::user()->id;

        $request->validate([
            'label_address' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'rt' => 'required|string|max:5',
            'rw' => 'required|string|max:5',
            'kelurahan' => 'required|string|max:255',
            'kecamatan' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'pos_code' => 'required|string|max:10',
        ]);

        $address = new Address();
        $address->user_id = $id_user;
        $address->receiver_name = $request->input('receiver_name');
        $address->phone_number = $request->input('receiver_phone');
        $address->label = $request->input('label_address');
        $address->address = $request->input('address');
        $address->rt = $request->input('rt');
        $address->rw = $request->input('rw');
        $address->kelurahan_desa = $request->input('kelurahan');
        $address->kecamatan = $request->input('kecamatan');
        $address->kota_kabupaten = $request->input('kabupaten');
        $address->provinsi = $request->input('province');
        $address->kode_pos = $request->input('pos_code');
        $address->is_primary = $this->checkPrimaryAddress($id_user) ? 0 : 1;
        $address->created_at = now();
        $address->updated_at = now();
        $address->save();

        return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => $slug]);
    }

    public function set_primary_address(Request $request, $id_user, $slug)
    {
        $id_user = Auth::user()->id;

        DB::beginTransaction();
        try {
            $old_primary = Address::where('user_id', $id_user)->where('is_primary', 1)->first();
            if ($old_primary) {
                $old_primary->is_primary = 0;
                $old_primary->updated_at = now();
                $old_primary->save();
            }

            $id_new_primary = $request->input('set-primary-address');
            $new_primary = Address::where('user_id', $id_user)->findOrFail($id_new_primary);
            $new_primary->is_primary = 1;
            $new_primary->updated_at = now();
            $new_primary->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to set primary address for user {$id_user}: " . $e->getMessage());
            return redirect()->back()->withErrors(['address_error' => 'Gagal mengatur alamat utama.']);
        }
        return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => $slug]);
    }

    public function update_quantity_item(Request $request, $id_user, $slug)
    {
        $id_user = Auth::user()->id;
        $new_quantity = $request->input('quantity_item');
        $cart_item_id = $request->input('cart_item_id');

        $cart_item = CartItem::where('id', $cart_item_id)
                             ->whereHas('cart', function($query) use ($id_user) {
                                 $query->where('user_id', $id_user);
                             })->firstOrFail();

        // Validasi stok saat update quantity
        $product = null;
        if ($cart_item->collection_id) {
            $product = Collection::find($cart_item->collection_id);
        } elseif ($cart_item->customize_id) {
            $product = Customize::find($cart_item->customize_id);
        } elseif ($cart_item->mysterybox_id) {
            $product = MysteryBox::find($cart_item->mysterybox_id);
        }

        if ($product && isset($product->stock) && $new_quantity > $product->stock) {
            return redirect()->back()->withErrors(['quantity_error' => 'Kuantitas melebihi stok yang tersedia (' . $product->stock . ').']);
        }

        $cart_item->quantity = $new_quantity;
        $cart_item->total_price = $cart_item->price * $new_quantity;
        $cart_item->updated_at = now();
        $cart_item->save();

        return redirect()->route('cart.index', ['id_user' => $id_user, 'slug' => $slug]);
    }
}
