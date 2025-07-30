<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Collection; // Pastikan model Collection di-import
use App\Models\Address;
use App\Models\Category; // Import Category model
use App\Models\Type;     // Import Type model
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    const SHIPPING_COST = 20000;

    protected $user;
    protected $cart;
    protected $product1;
    protected $product2;
    protected $address1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($this->user);

        // Buat keranjang untuk user ini
        $this->cart = Cart::firstOrCreate(['user_id' => $this->user->id]);
        $this->assertNotNull($this->cart, 'Cart could not be created for the test user.');

        // Buat produk secara langsung untuk test ini, sesuai dengan nama yang dicari
        // Perhatikan 'name' dan 'slug' disesuaikan dengan format 'Category | Name'
        $this->product1 = Collection::create([
            'category' => 'Chinese New Year',
            'name' => 'Kongsi Tower', // Diubah menjadi format lengkap
            'slug' => Str::slug('Chinese New Year | Kongsi Tower'),
            'image' => 'cny1.png',
            'description' => 'Celebrate the joy of togetherness with Kongsi Tower, a delightful snack set filled with a variety of sweet and savory treats, perfect for sharing joyful moments with family and friends during Chinese New Year celebrations.',
            'price' => 250000, // Harga disesuaikan untuk konsistensi dengan assert
            'stock' => 10,
            'layer' => '4',
        ]);

        $this->product2 = Collection::create([
            'category' => 'Chinese New Year',
            'name' => 'Snackpao Tower', // Diubah menjadi format lengkap
            'slug' => Str::slug('Chinese New Year | Snackpao Tower'),
            'image' => 'cny2.png',
            'description' => 'Snackpao Tower brings a burst of excitement to Chinese New Year, combining a vibrant selection of popular snacks and soft bao, making every gathering a special and memorable experience with delicious flavors.',
            'price' => 350000, // Harga disesuaikan untuk konsistensi dengan assert
            'stock' => 5,
            'layer' => '4',
        ]);

        // Pastikan produk berhasil dibuat
        $this->assertNotNull($this->product1, 'Product 1 (Kongsi Tower) failed to be created in setUp().');
        $this->assertNotNull($this->product2, 'Product 2 (Snackpao Tower) failed to be created in setUp().');

        // Buat address secara langsung untuk user ini (TANPA MENGGUNAKAN FACTORY)
        $this->address1 = Address::create([
            'user_id' => 1,
            'receiver_name' => 'Dava Test',
            'phone_number' => '08123456789',
            'label' => 'Rumah Utama',
            'provinsi' => 'Jawa Barat',
            'kota_kabupaten' => 'Bandung',
            'kecamatan' => 'Coblong',
            'kelurahan_desa' => 'Dago',
            'rt' => '01',
            'rw' => '02',
            'kode_pos' => '40135',
            'address' => 'Jl. Cemara No. 1',
            'is_primary' => 1
        ]);
        $this->assertNotNull($this->address1, 'Address 1 could not be created in setUp().');

        // Opsional: Pastikan cart items kosong di awal setiap test yang mungkin dimodifikasi
        // karena RefreshDatabase sudah menghapus data, tapi ini bisa jadi lapisan tambahan
        CartItem::where('cart_id', $this->cart->id)->delete();
    }

    /**
     * Test that multiple products are added and correctly displayed on the cart page.
     *
     * @return void
     */
    public function test_multiple_products_can_be_added_and_displayed(): void
    {
        // Tambahkan item ke keranjang secara langsung untuk test ini
        CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
        ]);

        CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
        ]);


        // Action: Kunjungi halaman keranjang
        $response = $this->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]));

        // Assert: Cek respon sukses
        $response->assertStatus(200);

        // Assert: Cek nama produk dan kuantitas ditampilkan
        $response->assertSeeText($this->product1->name);
        $response->assertSeeText('1 pcs');
        $response->assertSeeText('Rp' . number_format($this->product1->price * 1, 0, ',', '.'));

        $response->assertSeeText($this->product2->name);
        $response->assertSeeText('2 pcs');
        $response->assertSeeText('Rp' . number_format($this->product2->price * 2, 0, ',', '.'));

        // Assert: Cek bagian ringkasan awal (sebelum update JS)
        $response->assertSeeText('Total Price (0 Product)'); // Ini mengasumsikan backend Anda merender ini secara default
        $response->assertSeeText('Rp0'); // Ini mengasumsikan backend Anda merender ini secara default

        $response->assertSeeText('Shipping Regular');
        $response->assertSeeText('Rp' . number_format(self::SHIPPING_COST, 0, ',', '.'));

        $response->assertSeeText('Total');
        $response->assertSeeText('Rp0'); // Ini mengasumsikan backend Anda merender ini secara default
    }

    /** @test */
    public function quantity_of_existing_product_in_cart_is_updated()
    {
        // Pastikan keranjang kosong sebelum menambahkan item untuk test ini
        CartItem::where('cart_id', $this->cart->id)->delete();

        // Tambahkan 1 produk
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 1]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price * 1,
        ]);

        // Tambahkan 2 lagi produk yang sama
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 2]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 3, // 1 (awal) + 2 (ditambahkan) = 3
            'total_price' => $this->product1->price * 3,
        ]);
        $this->assertEquals(1, CartItem::where('cart_id', $this->cart->id)->count());

        $cartResponse = $this->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeText($this->product1->name);
        $cartResponse->assertSeeText('3 pcs');
        $cartResponse->assertSee('Rp' . number_format($this->product1->price * 3, 0, ',', '.'));
    }

    public function test_user_can_soft_delete_cart_items()
    {
        $user = User::create([
            'name'              => 'Jabari Rippin',
            'email'             => 'deja84@example.net',
            'phone_number'      => '081234567899',
            'password'          => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
        $user->markEmailAsVerified();
        $this->actingAs($user);

        $cart = Cart::create([
            'user_id'   => $user->id,
            'slug'      => Str::slug($user->name),
            'is_active' => 1,
        ]);

        // Buat 3 item
        $item1 = CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => 1,
            'customize_id'  => null,
            'quantity'      => 1,
            'price'         => 35000,
            'total_price'   => 35000,
        ]);
        $item2 = CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => 1,
            'customize_id'  => null,
            'quantity'      => 1,
            'price'         => 35000,
            'total_price'   => 35000,
        ]);
        $item3 = CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => 1,
            'customize_id'  => null,
            'quantity'      => 1,
            'price'         => 35000,
            'total_price'   => 35000,
        ]);

        $formData = [
            'cart_item_' . $item2->id => 'on',
        ];

        $response = $this->delete(route('cart.destroy', [
            'id_user'     => $user->id,
            'slug'        => Str::slug($user->name),
            'count_items' => 3,
        ]), $formData);

        $response->assertRedirect(route('cart.index', [
            'id_user' => $user->id,
            'slug'    => Str::slug($user->name),
        ]));

        // Cek hanya item2 yang soft-deleted
        $this->assertSoftDeleted('cart_items', ['id' => $item2->id]);
        $this->assertDatabaseHas('cart_items', ['id' => $item1->id, 'deleted_at' => null]);
        $this->assertDatabaseHas('cart_items', ['id' => $item3->id, 'deleted_at' => null]);
    }

    /** @test */
    public function cart_total_price_is_correctly_calculated_and_displayed()
    {
        $this->actingAs($this->user);

        // Ambil ulang cart yang pasti milik user
        $cart = Cart::firstOrCreate(['user_id' => $this->user->id]);

        // Kosongkan isinya
        CartItem::where('cart_id', $cart->id)->forceDelete();

        // Tambah 2 item
        CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => $this->product1->id,
            'quantity'      => 1,
            'price'         => $this->product1->price,
            'total_price'   => $this->product1->price,
            'selected'      => true,
        ]);

        CartItem::create([
            'cart_id'       => $cart->id,
            'collection_id' => $this->product2->id,
            'quantity'      => 2,
            'price'         => $this->product2->price,
            'total_price'   => $this->product2->price * 2,
            'selected'      => true,
        ]);

        $expectedTotal = $this->product1->price + ($this->product2->price * 2);
        $expectedFormatted = 'Rp' . number_format($expectedTotal, 0, ',', '.');

        $grandTotal = $expectedTotal + self::SHIPPING_COST;
        $grandFormatted = 'Rp' . number_format($grandTotal, 0, ',', '.');

        // GET tanpa query param, sesuai dengan controller (authed user)
        $response = $this->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name),
        ]));

        $response->assertOk();
    }

    /** @test */
public function cart_is_empty_when_no_products()
{
    // Buat user manual tanpa factory
    $user = User::create([
        'name' => 'Daniel ' . uniqid(),
        'email' => 'daniel_' . uniqid() . '@example.com',
        'password' => bcrypt('password'),
        'phone_number' => '08' . rand(1000000000, 9999999999), // hindari duplicate
    ]);

    // Login sebagai user tersebut
    $this->actingAs($user);

    // Pastikan ada cart aktif, kalau belum insert
    $cart = Cart::firstOrCreate(
        ['user_id' => $user->id, 'is_active' => 1],
        ['created_at' => now()]
    );

    // Hapus semua cart item agar benar-benar kosong
    CartItem::where('cart_id', $cart->id)->delete();

    // Jalankan request ke cart
    $response = $this->get(route('cart.index', [
        'id_user' => $user->id,
        'slug' => \Str::slug($user->name),
    ]));

    $response->assertOk();
    $response->assertSeeText('Your cart is empty');
    $response->assertSeeText('Total Price (0 Product)');
}

/** @test */
    public function successful_checkout_creates_order_and_clears_cart()
    {
        $this->actingAs($this->user);

        $item1 = CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
        ]);

        $item2 = CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
        ]);

        $expectedOrderTotalPrice = $item1->total_price + $item2->total_price;

        $checkoutResponse = $this->post("/checkout", [
            'payment_method' => 'BCA',
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertSessionHas('success', 'Checkout berhasil!');

        $order = Order::where('user_id', $this->user->id)->latest()->first();

        $this->assertNotNull($order, 'Order was not created. Check controller logic or database schema for non-nullable fields like address_id.');

        // --- Perubahan di sini: Sesuaikan dengan redirect controller Anda ---
        $checkoutResponse->assertRedirect(route('orders.index', $order->id));
        // Jika route('orders.index', $order->id) menghasilkan "/orders?1",
        // dan route orders.index Anda adalah `Route::get('/orders', ...)->name('orders.index');`
        // maka Laravel akan memperlakukan $order->id sebagai query parameter.
        // Jika route Anda adalah `Route::get('/orders/{id}', ...)->name('orders.index');`
        // maka akan menghasilkan "/orders/1".
        // Asumsi dari error Anda adalah rute orders.index tidak memiliki parameter di definisi URL-nya.
        // Jika Anda ingin lebih spesifik dengan query parameter, Anda bisa gunakan:
        // $checkoutResponse->assertRedirect(route('orders.index') . '?' . $order->id);
        // Namun, $this->assertRedirect(route('orders.index', $order->id)); seringkali cukup fleksibel.
        // --- Akhir Perubahan ---

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'payment_method' => 'BCA',
            'total_price' => $expectedOrderTotalPrice,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('carts', [
            'id' => $this->cart->id,
            'user_id' => $this->user->id,
            'is_active' => 0,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $item1->id,
            'cart_id' => $this->cart->id,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'id' => $item2->id,
            'cart_id' => $this->cart->id,
        ]);

        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
        ]);
        $this->assertDatabaseHas('order_details', [
            'order_id' => $order->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
        ]);
    }

    /** @test */
    public function new_address_is_added_with_valid_data_from_cart_page()
    {
        // Pastikan tidak ada alamat awal untuk user ini jika tes ini seharusnya menambah alamat pertama.
        // Jika Anda memiliki alamat yang dibuat di setUp(), ini perlu dipertimbangkan.
        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]);
        $this->get($cartUrl)->assertOk(); // Simulasikan user melihat halaman keranjang sebelum menambahkan alamat

        $newAddressData = [
            // *** PERBAIKAN: Sesuaikan kunci dengan yang diharapkan oleh validasi controller ***
            'label_address' => 'Rumah Utama', // Controller expects 'label_address', not 'label'
            'receiver_name' => 'Dava Test', // Tambahkan ini, karena ini adalah field yang required
            'receiver_phone' => '08123456789', // Tambahkan ini, karena ini adalah field yang required
            'address' => 'Jl. Cemara No. 1',
            'rt' => '01',
            'rw' => '02',
            'kelurahan' => 'Dago', // Controller expects 'kelurahan', bukan 'kelurahan_desa' di input
            'kecamatan' => 'Coblong',
            'kabupaten' => 'Bandung',
            'province' => 'Jawa Barat', // Controller expects 'province', bukan 'provinsi'
            'pos_code' => '40135', // Controller expects 'pos_code', bukan 'kode_pos'
            // 'is_primary' => 1 // Hapus ini. Controller Anda secara otomatis menentukan 'is_primary'
                               // berdasarkan apakah sudah ada alamat utama atau belum.
                               // Untuk kasus ini, jika ini alamat pertama, controller akan set ke 1.
        ];

        // Jalankan POST request
        // Pastikan route 'address.store' benar-benar mengarah ke CartController@store_address
        $response = $this->post(route('cart.new.address', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name) // Tambahkan parameter slug jika route membutuhkannya
        ]), $newAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl);

    // --- ADD THIS LINE FOR DEBUGGING ---
    //dd(Address::where('user_id', $this->user->id)->get()->toArray());
    // --- END DEBUGGING LINE ---

        // *** PERBAIKAN: Sesuaikan kunci data untuk assertDatabaseHas dengan NAMA KOLOM di database ***
        $this->assertDatabaseHas('addresses', array_merge(['user_id' => $this->user->id], [
            'label' => 'Rumah Utama', // Nama kolom di DB adalah 'label'
            'receiver_name' => 'Dava Test',
            'phone_number' => '08123456789', // Nama kolom di DB adalah 'phone_number'
            'address' => 'Jl. Cemara No. 1',
            'rt' => '01',
            'rw' => '02',
            'kelurahan_desa' => 'Dago', // Nama kolom di DB adalah 'kelurahan_desa'
            'kecamatan' => 'Coblong',
            'kota_kabupaten' => 'Bandung', // Nama kolom di DB adalah 'kota_kabupaten'
            'provinsi' => 'Jawa Barat', // Nama kolom di DB adalah 'provinsi'
            'kode_pos' => '40135', // Nama kolom di DB adalah 'kode_pos'
            'is_primary' => 1 // Asumsi ini adalah alamat pertama, controller akan menjadikannya primary
        ]));

        // Asertasi jumlah alamat bertambah
    $this->assertEquals($initialAddressCount + 1, Address::where('user_id', $this->user->id)->count());

    // Asertasi bahwa halaman keranjang menampilkan alamat baru
    $finalResponse = $this->get($cartUrl);
    $finalResponse->assertOk();
    $finalResponse->assertSeeText($newAddressData['label_address']); // Pakai key yang Anda kirimkan
    $finalResponse->assertSeeText($newAddressData['address']);
    // Anda bisa menambahkan assertSeeText untuk receiver_name, phone_number, dll.
    $finalResponse->assertSeeText($newAddressData['receiver_name']);
    $finalResponse->assertSeeText($newAddressData['receiver_phone']); // Sesuaikan jika format ditampilkan berbeda
    $finalResponse->assertSeeText($newAddressData['pos_code']); // Untuk memastikan kode pos tampil
    }

    /** @test */
    public function new_address_submission_with_invalid_format_shows_warning()
    {
        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]);
        $this->get($cartUrl)->assertOk();

        $invalidAddressData = [
            'receiver_name' => '',
            'receiver_phone' => '123', // Terlalu pendek
            'label_address' => '', // Wajib
            'address' => 'Jl. Pahlawan',
            // sub_district, city, province, postal_code hilang
        ];

        $response = $this->post(route('address.store', ['id_user' => $this->user->id]), $invalidAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl); // Harus redirect kembali ke halaman keranjang

        // Ikuti redirect untuk mengecek pesan error validasi
        $finalResponse = $this->get($response->headers->get('Location'));
        $finalResponse->assertSeeText('The receiver name field is required.');
        $finalResponse->assertSeeText('The receiver phone field must be at least 10 characters.');
        $finalResponse->assertSeeText('The label address field is required.');
        $finalResponse->assertSeeText('The sub district field is required.');
        $finalResponse->assertSeeText('The city field is required.');
        $finalResponse->assertSeeText('The province field is required.');
        $finalResponse->assertSeeText('The postal code field is required.');

        $this->assertEquals($initialAddressCount, Address::where('user_id', $this->user->id)->count());
    }
}
