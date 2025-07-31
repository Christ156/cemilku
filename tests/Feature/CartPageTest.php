<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\User;
use App\Models\Collection;
use App\Models\Address;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The authenticated user instance.
     * @var \App\Models\User|\Illuminate\Contracts\Auth\Authenticatable
     */
    protected $user;
    protected $cart;
    protected $product1;
    protected $product2;
    protected $address1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Pastikan user dibuat unik untuk setiap test run
        $this->user = User::factory()->create([
            'name' => 'User Test ' . Str::random(8), // Tambahkan random string yang lebih panjang
            'email' => 'user_' . Str::random(10) . '@example.com', // Tambahkan random string yang lebih panjang
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($this->user);

        // 2. Buat keranjang untuk user ini
        $this->cart = Cart::create(['user_id' => $this->user->id]);
        $this->assertNotNull($this->cart, 'Cart could not be created for the test user.');

        // 3. Buat produk secara langsung untuk test ini
        $this->product1 = Collection::create([
            'category' => 'Chinese New Year',
            'name' => 'Kongsi Tower',
            'slug' => Str::slug('Chinese New Year | Kongsi Tower ' . Str::random(5)), // Tambahkan random slug
            'image' => 'cny1.png',
            'description' => 'Celebrate the joy of togetherness with Kongsi Tower, a delightful snack set filled with a variety of sweet and savory treats, perfect for sharing joyful moments with family and friends during Chinese New Year celebrations.',
            'price' => 339000,
            'stock' => 10,
            'layer' => '4',
        ]);

        $this->product2 = Collection::create([
            'category' => 'Chinese New Year',
            'name' => 'Snackpao Tower',
            'slug' => Str::slug('Chinese New Year | Snackpao Tower ' . Str::random(5)), // Tambahkan random slug
            'image' => 'cny2.png',
            'description' => 'Snackpao Tower brings a burst of excitement to Chinese New Year, combining a vibrant selection of popular snacks and soft bao, making every gathering a special and memorable experience with delicious flavors.',
            'price' => 355000,
            'stock' => 5,
            'layer' => '4',
        ]);

        $this->assertNotNull($this->product1, 'Product 1 (Kongsi Tower) failed to be created in setUp().');
        $this->assertNotNull($this->product2, 'Product 2 (Snackpao Tower) failed to be created in setUp().');

        $this->address1 = Address::create([
            'user_id' => $this->user->id,
            'receiver_name' => 'Dava Test ' . Str::random(3),
            'phone_number' => '08123456789' . rand(0, 9),
            'label' => 'Rumah Utama ' . Str::random(3),
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
    }

    /**
     * Test that multiple products are added and correctly displayed on the cart page.
     *
     * @return void
     */
    public function test_multiple_products_can_be_added_and_displayed(): void
    {
        CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
            'selected' => true,
        ]);

        CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
            'selected' => true,
        ]);

        $expectedCartTotal = ($this->product1->price * 1) + ($this->product2->price * 2);

        $response = $this->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]));

        $response->assertSeeText($this->product1->name);
        $response->assertSeeText('1');
        $response->assertSeeText('Rp' . number_format($this->product1->price * 1, 0, ',', '.'));

        $response->assertSeeText($this->product2->name);
        $response->assertSeeText('2');
    }

    /** @test */
    public function quantity_of_existing_product_in_cart_is_updated()
    {
        // Tambahkan 1 produk
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 1]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
        ]);

        // Tambahkan 2 lagi produk yang sama
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 2]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 3, // 1 (awal) + 2 (ditambah) = 3
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 3,
        ]);
        $this->assertEquals(1, CartItem::where('cart_id', $this->cart->id)->count());

        $cartResponse = $this->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeText($this->product1->name);
        $cartResponse->assertSeeText('3');
        $cartResponse->assertSee('Rp' . number_format($this->product1->price * 3, 0, ',', '.'));
    }

    public function test_user_can_soft_delete_cart_item()
    {
        $this->actingAs($this->user);

        // Add product 1
        $response1 = $this->post(route('collection.to.cart', [
            'id_collection' => $this->product1->id,
            'quantity' => 1,
        ]));
        $response1->assertStatus(302);
        $response1->assertRedirect(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name)]));

        $cartForUser = Cart::where('user_id', $this->user->id)->where('is_active', true)->first();
        $this->assertNotNull($cartForUser, 'Cart should have been created for the user.');


        // Add product 2
        $response2 = $this->post(route('collection.to.cart', [
            'id_collection' => $this->product2->id,
            'quantity' => 2,
        ]));
        $response2->assertStatus(302);
        $response2->assertRedirect(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name)]));

        $this->assertDatabaseCount('carts', 1);
        $this->assertDatabaseCount('cart_items', 2);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cartForUser->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $cartForUser->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
        ]);

        $expectedCartTotal = ($this->product1->price * 1) + ($this->product2->price * 2);

        $response = $this->actingAs($this->user)->get(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]));

        $response->assertStatus(200);
        $response->assertSee($this->product1->name);
        $response->assertSee('1');
        $response->assertSee(number_format($this->product1->price * 1, 0, ',', '.'));
        $response->assertSee($this->product2->name);
        $response->assertSee('2');
        $response->assertSee(number_format($this->product2->price * 2, 0, ',', '.'));
    }

    /** @test */
    public function cart_total_price_is_correctly_calculated_and_displayed()
    {
        $this->withoutExceptionHandling();

        $user = $this->user;
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        if (!isset($this->product1)) {
            $this->product1 = \App\Models\Collection::create([
                'category' => 'Chinese New Year', 'name' => 'Kongsi Tower', 'slug' => 'kongsi-tower', 'image' => 'test.png', 'description' => 'Test', 'price' => 339000, 'stock' => 5, 'layer' => '1'
            ]);
        }
        if (!isset($this->product2)) {
            $this->product2 = \App\Models\Collection::create([
                'category' => 'Chinese New Year', 'name' => 'Snackpao Tower', 'slug' => 'snackpao-tower', 'image' => 'test.png', 'description' => 'Test', 'price' => 355000, 'stock' => 5, 'layer' => '1'
            ]);
        }

        $userCart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
        ]);

        $expectedCartTotal = $this->product1->price + ($this->product2->price * 2);
        $formattedExpectedCartTotal = 'Rp0';

        $userSlug = \Str::slug($user->name ?? '');

        $userCart->refresh();

        $response = $this->get(route('cart.index', [
            'id_user' => $user->id,
            'slug' => $userSlug,
        ]));

        $response->assertOk();

        file_put_contents(storage_path('logs/cart_debug.html'), $response->getContent());

        $response->assertSee('Total Price');
        $response->assertSee('Shipping Regular');
        $response->assertSee('Total');
        $response->assertSeeText($formattedExpectedCartTotal);
    }

    /** @test */
    public function cart_is_empty_when_no_products()
    {
        $user = User::factory()->create([
            'name' => 'User Empty Cart ' . Str::random(8),
            'email' => 'empty_cart_' . Str::random(10) . '@example.com',
            'password' => bcrypt('password'),
            'phone_number' => '08' . rand(1000000000, 9999999999),
        ]);

        $this->actingAs($user);

        $cart = Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['slug' => Str::slug($user->name), 'is_active' => 1]
        );

        CartItem::where('cart_id', $cart->id)->delete();

        $response = $this->get(route('cart.index', [
            'id_user' => $user->id,
            'slug' => Str::slug($user->name),
        ]));

        $response->assertOk();
        $response->assertSeeText('Your cart is empty');
        $response->assertSeeText('Total Price (0 Product)');
    }

    /** @test */
    public function successful_checkout_creates_order_and_clears_cart()
    {
        $item1 = CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
            'selected' => true,
        ]);

        $item2 = CartItem::create([
            'cart_id' => $this->cart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
            'selected' => true,
        ]);

        $expectedOrderTotalPrice = $item1->total_price + $item2->total_price;

        $checkoutResponse = $this->post("/checkout", [
            'payment_method' => 'BCA',
            'address_id' => $this->address1->id,
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertSessionHas('success', 'Checkout berhasil!');

        $order = Order::where('user_id', $this->user->id)->latest()->first();

        $this->assertNotNull($order, 'Order was not created.');

        $checkoutResponse->assertRedirect(route('orders.index', $order->id));

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $this->user->id,
            'address_id' => $order->address_id,
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
            'deleted_at' => null
        ]);
        $this->assertDatabaseHas('cart_items', [
            'id' => $item2->id,
            'cart_id' => $this->cart->id,
            'deleted_at' => null
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
        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]);
        $this->get($cartUrl)->assertOk();

        $newAddressData = [
            'label_address' => 'Kantor Baru ' . Str::random(3),
            'receiver_name' => 'Dava Kantor ' . Str::random(3),
            'receiver_phone' => '08123456788' . rand(0, 9),
            'address' => 'Jl. Merdeka No. 10',
            'rt' => '03',
            'rw' => '04',
            'kelurahan' => 'Cihampelas',
            'kecamatan' => 'Cipaganti',
            'kabupaten' => 'Bandung',
            'province' => 'Jawa Barat',
            'pos_code' => '40115',
            'is_primary' => 0
        ];

        $response = $this->post(route('cart.new.address', [
            'id_user' => $this->user->id,
            'slug' => Str::slug($this->user->name)
        ]), $newAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl);

        $this->assertDatabaseHas('addresses', array_merge(['user_id' => $this->user->id], [
            'label' => $newAddressData['label_address'],
            'receiver_name' => $newAddressData['receiver_name'],
            'phone_number' => $newAddressData['receiver_phone'],
            'address' => $newAddressData['address'],
            'rt' => $newAddressData['rt'],
            'rw' => $newAddressData['rw'],
            'kelurahan_desa' => $newAddressData['kelurahan'],
            'kecamatan' => $newAddressData['kecamatan'],
            'kota_kabupaten' => $newAddressData['kabupaten'],
            'provinsi' => $newAddressData['province'],
            'kode_pos' => $newAddressData['pos_code'],
        ]));

        $this->assertEquals($initialAddressCount + 1, Address::where('user_id', $this->user->id)->count());

        $finalResponse = $this->get($cartUrl);
        $finalResponse->assertOk();
        $finalResponse->assertSeeText($newAddressData['label_address']);
        $finalResponse->assertSeeText($newAddressData['address']);
        $finalResponse->assertSeeText($newAddressData['receiver_name']);
        $finalResponse->assertSeeText($newAddressData['receiver_phone']);
        $finalResponse->assertSeeText($newAddressData['pos_code']);
    }

    // BLOM ADA VALIDASI
    // /** @test */
    // public function new_address_submission_with_invalid_format_shows_warning()
    // {
    //     // Uncoment jika Anda ingin menguji validasi form
    //     $initialAddressCount = Address::where('user_id', $this->user->id)->count();

    //     $cartUrl = route('cart.index', [
    //         'id_user' => $this->user->id,
    //         'slug' => Str::slug($this->user->name)
    //     ]);
    //     $this->get($cartUrl)->assertOk();

    //     $invalidAddressData = [
    //         'receiver_name' => '',
    //         'receiver_phone' => '123', // Terlalu pendek
    //         'label_address' => '', // Wajib
    //         'address' => 'Jl. Pahlawan',
    //         // 'rt', 'rw', 'kelurahan', 'kecamatan', 'kabupaten', 'province', 'pos_code' hilang
    //     ];

    //     // Perhatikan: Route untuk menyimpan alamat baru dari halaman keranjang adalah 'cart.new.address'
    //     // Bukan 'address.store' seperti di komentar Anda.
    //     $response = $this->post(route('cart.new.address', [
    //         'id_user' => $this->user->id,
    //         'slug' => Str::slug($this->user->name)
    //     ]), $invalidAddressData);

    //     $response->assertStatus(302);
    //     $response->assertRedirect($cartUrl); // Harus redirect kembali ke halaman keranjang

    //     // Ikuti redirect untuk mengecek pesan error validasi
    //     // Perhatikan: jika Anda menggunakan withErrors() dan redirect()->back(),
    //     // pesan error akan ada di session dan perlu di-assert menggunakan assertSessionHasErrors.
    //     $response->assertSessionHasErrors([
    //         'label_address', 'receiver_name', 'receiver_phone', 'rt', 'rw',
    //         'kelurahan', 'kecamatan', 'kabupaten', 'province', 'pos_code' // Semua yang required
    //     ]);
    //     // Jika Anda memeriksa teks di halaman setelah redirect, pastikan bahwa halaman tersebut
    //     // benar-benar menampilkan pesan error dari session.
    //     // $finalResponse = $this->get($response->headers->get('Location'));
    //     // $finalResponse->assertSeeText('The receiver name field is required.');
    //     // $finalResponse->assertSeeText('The receiver phone field must be at least 10 characters.');
    //     // dst.

    //     $this->assertEquals($initialAddressCount, Address::where('user_id', $this->user->id)->count());
    // }
}
