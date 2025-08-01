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
    /**
     * Menampilkan daftar item di keranjang belanja pengguna.
     *
     * @param int $id_user Parameter ID pengguna dari URL (akan divalidasi dengan Auth::user()->id).
     * @param string $slug Parameter slug dari URL.
     * @return \Illuminate\View\View
     */
    public function index($id_user, $slug)
    {
        // Selalu gunakan ID pengguna yang sedang login untuk keamanan
        $authenticatedUserId = Auth::user()->id;

        // Temukan keranjang aktif atau buat yang baru jika tidak ada
        $cart = Cart::firstOrCreate(
            ['user_id' => $authenticatedUserId, 'is_active' => 1],
            ['created_at' => now(), 'updated_at' => now()]
        );
        $id_cart = $cart->id;

        \Log::info('DEBUG: Current active cart ID determined by controller: ' . $id_cart);

        $carts = CartItem::where('cart_id', $id_cart)
                         ->with(['collection', 'customize', 'mysteryBox'])
                         ->get();

        \Log::info('DEBUG: Cart items retrieved: ' . $carts->pluck('id')->implode(', '));

        foreach ($carts as $item) {
            \Log::info('Cart Item ID: ' . $item->id);
            \Log::info('Collection ID: ' . $item->collection_id);
            \Log::info('Customize ID: ' . $item->customize_id);
            \Log::info('MysteryBox ID: ' . $item->mysterybox_id);
            if ($item->mysteryBox) {
                \Log::info('MysteryBox Mood: ' . $item->mysteryBox->mood);
                \Log::info('MysteryBox Name: ' . $item->mysteryBox->name);
            } else {
                \Log::info('MysteryBox relationship is NULL');
            }
        }

        $address_active = Address::where('user_id', $authenticatedUserId)->where('is_primary', 1)->first();
        $count_address_active = Address::where('user_id', $authenticatedUserId)->where('is_primary', 1)->count();
        $address = Address::where('user_id', $authenticatedUserId)->where('is_primary', 0)->get();

        // Calculate total price from items on display (not necessarily selected)
        $totalPriceFromItems = $carts->sum('total_price');
        $shippingCost = 20000;
        $finalCartTotal = $totalPriceFromItems + $shippingCost; // This is the total if ALL items were selected + shipping

        $formattedFinalCartTotal = 'Rp ' . number_format($finalCartTotal, 0, ',', '.');

        return view('cart', \compact(['carts', 'address_active', 'count_address_active', 'address', 'formattedFinalCartTotal']));
    }

    /**
     * Memproses checkout item di keranjang.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_user Parameter ID pengguna dari URL.
     * @param string $slug Parameter slug dari URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function checkout(Request $request, $id_user, $slug)
    {
        $user = Auth::user();
        $authenticatedUserId = $user->id; // Gunakan ini untuk operasi database

        Log::info('DEBUG: CartController@checkout called.');
        Log::info('DEBUG: Request Payload: ' . json_encode($request->all()));

        try {
            $request->validate([
                'payment_method' => 'required|in:BCA,Mandiri,Cimb Niaga,Danamon',
                'total_price' => 'required|numeric|min:0', // Pastikan total_price divalidasi
            ]);
        } catch (ValidationException $e) {
            Log::warning('DEBUG: Validation failed: ' . json_encode($e->errors()));
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $cart = Cart::where('user_id', $authenticatedUserId)->where('is_active', true)->first();

        if (!$cart) {
            Log::warning('DEBUG: Cart not found or not active for user: ' . $authenticatedUserId);
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Keranjang kosong atau tidak aktif.']);
        }

        $selectedCartItems = [];
        foreach ($request->all() as $key => $value) {
            if (Str::startsWith($key, 'item_cart_') && $value === 'true') {
                $cartItemId = (int) Str::after($key, 'item_cart_');
                $item = $cart->cartItems()->find($cartItemId);
                if ($item) {
                    $selectedCartItems[] = $item;
                }
            }
        }
        $selectedCartItems = collect($selectedCartItems);

        Log::info('DEBUG: Selected Cart Items count: ' . $selectedCartItems->count());
        Log::info('DEBUG: Selected Cart Items IDs: ' . $selectedCartItems->pluck('id')->implode(', '));


        if ($selectedCartItems->isEmpty()) {
            Log::warning('DEBUG: No items selected for checkout.');
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Tidak ada item yang dipilih untuk checkout.']);
        }

        $address = Address::where('user_id', $authenticatedUserId)->where('is_primary', true)->first();

        if (!$address) {
            Log::warning('DEBUG: No primary address found for user: ' . $authenticatedUserId);
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug($user->name)])
                             ->withErrors(['error' => 'Harap atur alamat pengiriman utama sebelum checkout.']);
        }

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

            if (!$product || (property_exists($product, 'stock') && $item->quantity > $product->stock)) {
                $name = $product ? (property_exists($product, 'name') ? $product->name : 'Unknown Product Name') : 'Produk Tidak Ditemukan';
                $stock = $product && property_exists($product, 'stock') ? $product->stock : 0;
                $unavailableItems[] = "{$name} (Stok tersedia: {$stock}, Diminta: {$item->quantity})";
            }
        }

        if (!empty($unavailableItems)) {
            Log::warning('DEBUG: Some items are unavailable: ' . implode(', ', $unavailableItems));
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug($user->name)])
                             ->withErrors(['stock_error' => 'Beberapa item tidak tersedia dalam jumlah yang diminta: ' . implode(', ', $unavailableItems)]);
        }

        DB::beginTransaction();
        try {
            // Gunakan total_price dari request form, yang seharusnya sudah dihitung oleh JS.
            // Ini akan mencakup total_price_cart dari item yang dipilih.
            $shippingCost = 20000; // Pastikan ini konsisten dengan JS
            $finalTotalPrice = $request->input('total_price') + $shippingCost; // Ambil total dari form dan tambahkan biaya kirim
            Log::info('DEBUG: Final Total Price for Order (from form + shipping): ' . $finalTotalPrice);

            $order = Order::create([
                'user_id' => $authenticatedUserId,
                'total_price' => $finalTotalPrice, // Gunakan nilai yang dihitung dari JS + shipping
                'payment_method' => $request->input('payment_method'),
                'address_id' => $address->id,
                'order_date' => now(),
                'invoice_id' => 'INV-' . Str::upper(Str::random(8)),
                'status' => 'pending',
                'delivery_date' => null,
                'delivery_status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            Log::info('DEBUG: Order created with ID: ' . $order->id);


            foreach ($selectedCartItems as $cartItem) {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'collection_id' => $cartItem->collection_id,
                    'customize_id' => $cartItem->customize_id,
                    'mysterybox_id' => $cartItem->mysterybox_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->total_price, // Ini adalah harga total per item (quantity * price_per_unit)
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                Log::info('DEBUG: OrderDetail created for CartItem ID: ' . $cartItem->id);


                // Decrement stock based on product type
                if ($cartItem->collection_id) {
                    Collection::find($cartItem->collection_id)->decrement('stock', $cartItem->quantity);
                    Log::info('DEBUG: Decremented stock for Collection ID: ' . $cartItem->collection_id);
                } elseif ($cartItem->customize_id) {
                    Customize::find($cartItem->customize_id)->decrement('stock', $cartItem->quantity);
                    Log::info('DEBUG: Decremented stock for Customize ID: ' . $cartItem->customize_id);
                } elseif ($cartItem->mysterybox_id) {
                    MysteryBox::find($cartItem->mysterybox_id)->decrement('stock', $cartItem->quantity);
                    Log::info('DEBUG: Decremented stock for MysteryBox ID: ' . $cartItem->mysterybox_id);
                }

                $cartItem->delete(); // Remove item from cart after successful order creation
                Log::info('DEBUG: CartItem ID: ' . $cartItem->id . ' deleted from cart.');
            }

            // Check if cart is now empty and deactivate it
            if ($cart->cartItems()->count() == 0) {
                $cart->is_active = false;
                $cart->save();
                Log::info('DEBUG: Cart ID: ' . $cart->id . ' deactivated as it is now empty.');
            }

            DB::commit();
            Log::info('DEBUG: Database transaction committed successfully. Redirecting to checkout.index...');

            // Successful checkout, redirect to payment page
            return redirect()->route('checkout.index', ['order_id' => $order->id])->with('success', 'Checkout berhasil!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("DEBUG: Checkout failed for user {$authenticatedUserId}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug($user->name)])
                             ->withErrors(['checkout_error' => 'Terjadi kesalahan saat checkout. Silakan coba lagi.']);
        }
    }

    /**
     * Menghapus item dari keranjang.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_user Parameter ID pengguna dari URL.
     * @param string $slug Parameter slug dari URL.
     * @param int $count_items Parameter jumlah item (tidak digunakan secara langsung dalam logika penghapusan).
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request, $id_user, $slug, $count_items)
    {
        $authenticatedUserId = Auth::user()->id; // Selalu gunakan ID pengguna yang sedang login
        $cart = Cart::where('user_id', $authenticatedUserId)->where('is_active', 1)->first();

        if (!$cart) {
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug(Auth::user()->name)])
                             ->withErrors(['error' => 'Keranjang tidak ditemukan atau tidak aktif.']);
        }

        $id_cart = $cart->id;
        $carts = CartItem::where('cart_id', $id_cart)->get();

        $deletedCount = 0;
        foreach ($carts as $cartItem) {
            if ($request->has('cart_item_' . $cartItem->id) && $request->input('cart_item_' . $cartItem->id) === 'true') {
                $cartItem->delete();
                $deletedCount++;
            }
        }

        if ($deletedCount === 0) {
            return redirect()->back()->withErrors(['error' => 'Tidak ada item yang dipilih untuk dihapus.']);
        }

        if (CartItem::where('cart_id', $id_cart)->count() == 0) {
            $cart->is_active = 0;
            $cart->save();
        }

        return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug(Auth::user()->name)]);
    }

    /**
     * Memeriksa apakah pengguna memiliki alamat utama.
     *
     * @param int $id_user ID pengguna.
     * @return bool
     */
    private function checkPrimaryAddress($id_user)
    {
        $count = Address::where('user_id', $id_user)->where('is_primary', 1)->count();
        return $count > 0;
    }

    /**
     * Menyimpan alamat baru untuk pengguna.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_user Parameter ID pengguna dari URL.
     * @param string $slug Parameter slug dari URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store_address(Request $request, $id_user, $slug)
    {
        $authenticatedUserId = Auth::user()->id; // Selalu gunakan ID pengguna yang sedang login

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
        $address->user_id = $authenticatedUserId;
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
        $address->is_primary = $this->checkPrimaryAddress($authenticatedUserId) ? 0 : 1;
        $address->created_at = now();
        $address->updated_at = now();
        $address->save();

        return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => $slug]);
    }

    /**
     * Mengatur alamat sebagai alamat utama.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_user Parameter ID pengguna dari URL.
     * @param string $slug Parameter slug dari URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function set_primary_address(Request $request, $id_user, $slug)
    {
        $authenticatedUserId = Auth::user()->id; // Selalu gunakan ID pengguna yang sedang login

        DB::beginTransaction();
        try {
            $old_primary = Address::where('user_id', $authenticatedUserId)->where('is_primary', 1)->first();
            if ($old_primary) {
                $old_primary->is_primary = 0;
                $old_primary->updated_at = now();
                $old_primary->save();
            }

            $id_new_primary = $request->input('set-primary-address');
            $new_primary = Address::where('user_id', $authenticatedUserId)->findOrFail($id_new_primary);
            $new_primary->is_primary = 1;
            $new_primary->updated_at = now();
            $new_primary->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to set primary address for user {$authenticatedUserId}: " . $e->getMessage());
            return redirect()->back()->withErrors(['address_error' => 'Gagal mengatur alamat utama.']);
        }
        return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => $slug]);
    }

    /**
     * Memperbarui kuantitas item di keranjang.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id_user Parameter ID pengguna dari URL.
     * @param string $slug Parameter slug dari URL.
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_quantity_item(Request $request, $id_user, $slug) // Menggunakan $id_user, $slug untuk konsistensi dengan rute
    {
        // === START LOGGING TAMBAHAN UNTUK DEBUGGING ===
        \Log::info('Method update_quantity_item called.');
        \Log::info('Request payload: ' . json_encode($request->all()));
        \Log::info("Parameters from URL - id_user: {$id_user}, slug: {$slug}");
        // === END LOGGING TAMBAHAN ===

        // Selalu gunakan ID pengguna yang sedang login untuk operasi database yang aman dan validasi kepemilikan.
        $authenticatedUserId = Auth::user()->id;
        $new_quantity = $request->input('quantity_item');
        $cart_item_id = $request->input('cart_item_id');

        // === LOGGING: Nilai yang diterima dari request ===
        \Log::info("Processing with Auth::user()->id: {$authenticatedUserId}, new_quantity: {$new_quantity}, cart_item_id: {$cart_item_id}");

        // Validasi input
        if (!is_numeric($new_quantity) || $new_quantity < 1) {
            \Log::warning("Invalid new_quantity received: {$new_quantity}");
            return redirect()->back()->withErrors(['quantity_error' => 'Kuantitas harus angka positif.']);
        }
        if (!is_numeric($cart_item_id)) {
            \Log::warning("Invalid cart_item_id received: {$cart_item_id}");
            return redirect()->back()->withErrors(['quantity_error' => 'ID item keranjang tidak valid.']);
        }

        try {
            // Pastikan item keranjang benar-benar milik pengguna yang sedang login
            $cart_item = CartItem::where('id', $cart_item_id)
                                 ->whereHas('cart', function($query) use ($authenticatedUserId) {
                                     $query->where('user_id', $authenticatedUserId);
                                 })->firstOrFail();

            \Log::info("CartItem found: ID {$cart_item->id}, current quantity: {$cart_item->quantity}");

            $product = null;
            if ($cart_item->collection_id) {
                $product = Collection::find($cart_item->collection_id);
                \Log::info("Product type: Collection, ID: {$cart_item->collection_id}");
            } elseif ($cart_item->customize_id) {
                $product = Customize::find($cart_item->customize_id);
                \Log::info("Product type: Customize, ID: {$cart_item->customize_id}");
            } elseif ($cart_item->mysterybox_id) {
                $product = MysteryBox::find($cart_item->mysterybox_id);
                \Log::info("Product type: MysteryBox, ID: {$cart_item->mysterybox_id}");
            }

            if (!$product) {
                \Log::error("Product (Collection, Customize, or MysteryBox) not found for CartItem ID: {$cart_item->id}");
                return redirect()->back()->withErrors(['quantity_error' => 'Produk terkait tidak ditemukan.']);
            }

            $product_stock = property_exists($product, 'stock') ? $product->stock : null;

            if (is_null($product_stock)) {
                \Log::warning("Stock property not found on product of type: " . get_class($product) . ". Assuming unlimited stock for this item type.");
                // Jika produk tidak punya stok, anggap stok tidak terbatas atau tangani sesuai logika bisnis Anda.
                // Untuk validasi ini, kita bisa mengabaikan pengecekan stok jika properti 'stock' tidak ada.
            } else {
                \Log::info("Product stock: {$product_stock}, Requested new quantity: {$new_quantity}");
                if ($new_quantity > $product_stock) {
                    \Log::warning("Quantity exceeds available stock for product. Requested: {$new_quantity}, Available: {$product_stock}");
                    return redirect()->back()->withErrors(['quantity_error' => 'Kuantitas melebihi stok yang tersedia (' . $product_stock . ').']);
                }
            }

            $cart_item->quantity = $new_quantity;
            // Asumsi 'price' ada di model CartItem, yang menyimpan harga per unit.
            $cart_item->total_price = $cart_item->price * $new_quantity;
            $cart_item->updated_at = now();
            $cart_item->save();

            \Log::info("CartItem ID {$cart_item->id} updated successfully. New quantity: {$cart_item->quantity}, New total price: {$cart_item->total_price}");

            // Redirect ke halaman keranjang setelah berhasil memperbarui
            return redirect()->route('cart.index', ['id_user' => $authenticatedUserId, 'slug' => Str::slug(Auth::user()->name)]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error("CartItem with ID {$cart_item_id} not found or does not belong to user {$authenticatedUserId}. Error: " . $e->getMessage());
            return redirect()->back()->withErrors(['quantity_error' => 'Item keranjang tidak ditemukan atau bukan milik Anda.']);
        } catch (\Exception $e) {
            \Log::error("Error updating quantity for CartItem ID {$cart_item_id}: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->withErrors(['quantity_error' => 'Terjadi kesalahan saat memperbarui kuantitas. Silakan coba lagi.']);
        }
    }
}
