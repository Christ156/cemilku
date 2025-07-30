<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Collection;
use App\Models\Address;
use Database\Seeders\UserSeeder; // Import UserSeeder
use Database\Seeders\AddressSeeder; // Import AddressSeeder
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker; // Mungkin tidak perlu jika tidak pakai faker
use Tests\TestCase;
use Illuminate\Support\Str;
use Database\Seeders\CollectionSeeder;
use Illuminate\Support\Facades\Hash; // Mungkin tidak perlu di test jika hanya pakai seeder
use Illuminate\Support\Facades\DB; // Mungkin tidak perlu di test jika hanya pakai seeder

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    protected $user; // User untuk tes ini (akan menjadi User Dua dari seeder)
    protected $product1;
    protected $product2;
    protected $address1;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Jalankan UserSeeder terlebih dahulu
        // Ini akan mengisi tabel `users` dengan user ID 1, 2, dan 3.
        $this->seed(UserSeeder::class);

        // 2. Jalankan CollectionSeeder (ini membuat produk seperti Kongsi Tower)
        $this->seed(CollectionSeeder::class);

        // 3. Jalankan AddressSeeder
        // Ini akan mengisi tabel `addresses` dengan alamat yang terkait dengan user ID 1 dan 2.
        $this->seed(AddressSeeder::class);

        // --- Perubahan PENTING di sini ---
        // Alih-alih membuat user baru, ambil user yang sudah ada dari seeder.
        // Asumsi user test utama Anda adalah user dengan ID 2 ('User Dua') dari UserSeeder.
        $this->user = User::find(2); // Ambil user dengan ID 2

        // Ambil alamat yang terkait dengan user test utama (user ID 2)
        $this->address1 = Address::where('user_id', $this->user->id)->first();

        // Ambil koleksi/produk yang ada dari seeder Collection
        $this->product1 = Collection::where('name', 'Kongsi Tower')->first();
        $this->product2 = Collection::where('name', 'Snackpao Tower')->first();

        // Assertions untuk memastikan data setup sudah benar
        $this->assertNotNull($this->user, 'Test user (ID 2) should exist from UserSeeder.');
        $this->assertNotNull($this->product1, 'Kongsi Tower should exist in the database from seeder.');
        $this->assertNotNull($this->product2, 'Snackpao Tower should exist in the database from seeder.');
        $this->assertNotNull($this->address1, 'Address for test user (ID 2) should exist from AddressSeeder.');
    }

    // --- (Sisa kode test Anda yang lain tetap sama) ---

    /** @test */
    public function multiple_products_can_be_added_and_displayed()
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
        $response->assertSee('Quantity: 1');
        $response->assertSee(number_format($this->product1->price * 1, 0, ',', '.'));
        $response->assertSee($this->product2->name);
        $response->assertSee('Quantity: 2');
        $response->assertSee(number_format($this->product2->price * 2, 0, ',', '.'));
        $response->assertSee(number_format($expectedCartTotal, 0, ',', '.'));
    }

    /** @test */
    public function quantity_of_existing_product_in_cart_is_updated()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 1]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
        ]);

        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 2]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 3,
            'total_price' => $this->product1->price * 3,
        ]);
        $this->assertEquals(1, CartItem::where('cart_id', $userCart->id)->count());

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSee($this->product1->name);
        $cartResponse->assertSeeText('3 pcs');
    }

    /** @test */
    public function product_can_be_removed_from_cart()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        $cartItem = CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price,
        ]);

        $this->assertDatabaseHas('cart_items', ['id' => $cartItem->id, 'collection_id' => $this->product1->id]);

        $response = $this->post(route('cart.delete', [
            'id_cart_item' => $cartItem->id,
        ]));

        $response->assertStatus(302);
        $response->assertRedirectContains(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]));

        $this->assertDatabaseMissing('cart_items', ['id' => $cartItem->id, 'collection_id' => $this->product1->id]);
        $this->assertEquals(0, CartItem::where('cart_id', $userCart->id)->count());

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));
        $cartResponse->assertOk();
        $cartResponse->assertDontSee($this->product1->name);
    }

    /** @test */
    public function cart_total_price_is_correctly_calculated_and_displayed()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 1,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'price' => $this->product2->price,
            'total_price' => $this->product2->price * 2,
        ]);

        $expectedCartTotal = ($this->product1->price * 1) + ($this->product2->price * 2);
        $formattedExpectedCartTotal = 'Rp' . number_format($expectedCartTotal, 0, ',', '.');

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeText('Total Harga (2 Produk)');
        $cartResponse->assertSeeText($formattedExpectedCartTotal);
        $cartResponse->assertSeeText('Shipping Regular');
        $cartResponse->assertSeeText('Total');
    }

    /** @test */
    public function cart_is_empty_when_no_products()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
        CartItem::where('cart_id', $userCart->id)->delete();

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeText('Keranjang Anda kosong');
        $cartResponse->assertDontSee($this->product1->name);
        $cartResponse->assertDontSee($this->product2->name);
        $cartResponse->assertSeeText('Total Harga (0 Produk)');
    }

    /** @test */
    public function successful_checkout_creates_order_and_clears_cart()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price,
        ]);

        $initialCartItemCount = CartItem::where('cart_id', $userCart->id)->count();
        $this->assertEquals(1, $initialCartItemCount);

        $expectedOrderTotal = $this->product1->price + 20000;

        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertRedirectContains(route('order.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]));

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_price' => $expectedOrderTotal,
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
        ]);
        $this->assertEquals(0, CartItem::where('cart_id', $userCart->id)->count());
    }

    /** @test */
    public function checkout_with_no_products_redirects_back_with_error()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
        CartItem::where('cart_id', $userCart->id)->delete();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);

        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertRedirect($cartUrl);
        $finalResponse = $this->get($checkoutResponse->headers->get('Location'));
        $finalResponse->assertSeeText('Keranjang Anda kosong');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    /** @test */
    public function checkout_without_selected_address_redirects_back_with_error()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price,
        ]);

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);

        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertRedirect($cartUrl);
        $finalResponse = $this->get($checkoutResponse->headers->get('Location'));
        $finalResponse->assertSeeText('The address id field is required.');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    /** @test */
    public function checkout_with_unavailable_products_shows_message()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 5,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price * 5,
        ]);

        $this->product1->stock = 2;
        $this->product1->save();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);

        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertRedirect($cartUrl);
        $finalResponse = $this->get($checkoutResponse->headers->get('Location'));
        $finalResponse->assertSeeText('Beberapa item tidak tersedia');
        $this->assertDatabaseMissing('orders', ['user_id' => $this->user->id]);
    }

    /** @test */
    public function shipping_estimation_displays_if_address_exists()
    {
        $this->actingAs($this->user);

        $userCart = Cart::firstOrCreate([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        CartItem::create([
            'cart_id' => $userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'price' => $this->product1->price,
            'total_price' => $this->product1->price,
        ]);

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $response = $this->get($cartUrl);
        $response->assertOk();

        $response->assertSeeText('Ongkos kirim');
        $response->assertSeeText('Rp');
        $response->assertSeeText('20.000');
    }

    /** @test */
    public function new_address_is_added_with_valid_data_from_cart_page()
    {
        $this->actingAs($this->user);

        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $this->get($cartUrl)->assertOk();

        $newAddressData = [
            'receiver_name' => 'John Doe',
            'receiver_phone' => '081234567890',
            'label' => 'Rumah Baru',
            'address' => 'Jl. Contoh Baru No. 456',
            'rt' => '01',
            'rw' => '01',
            'kelurahan_desa' => 'Kelurahan Baru',
            'kecamatan' => 'Kecamatan Baru',
            'kota_kabupaten' => 'Kota Baru',
            'provinsi' => 'Provinsi Baru',
            'kode_pos' => '54321',
        ];

        $response = $this->post(route('address.store', ['id_user' => $this->user->id]), $newAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl);

        $this->assertDatabaseHas('addresses', array_merge(['user_id' => $this->user->id], $newAddressData));
        $this->assertEquals($initialAddressCount + 1, Address::where('user_id', $this->user->id)->count());

        $finalResponse = $this->get($cartUrl);
        $finalResponse->assertOk();
        $finalResponse->assertSeeText($newAddressData['label']);
        $finalResponse->assertSeeText($newAddressData['address']);
    }

    /** @test */
    public function new_address_submission_with_invalid_format_shows_warning()
    {
        $this->actingAs($this->user);

        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $this->get($cartUrl)->assertOk();

        $invalidAddressData = [
            'receiver_name' => '',
            'receiver_phone' => '123',
            'label' => '',
            'address' => 'Jl. Pahlawan',
            'kecamatan' => 'Some Suburb',
            'kota_kabupaten' => 'Some City',
            'provinsi' => 'Some Province',
            'kode_pos' => '123',
            'kelurahan_desa' => '',
        ];

        $response = $this->post(route('address.store', ['id_user' => $this->user->id]), $invalidAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl);

        $finalResponse = $this->get($response->headers->get('Location'));
        $finalResponse->assertSeeText('The receiver name field is required.');
        $finalResponse->assertSeeText('The receiver phone field must be at least 10 characters.');
        $finalResponse->assertSeeText('The label field is required.');
        $finalResponse->assertSeeText('The kelurahan desa field is required.');
        $finalResponse->assertSeeText('The kode pos field must be at least 5 characters.');

        $this->assertEquals($initialAddressCount, Address::where('user_id', $this->user->id)->count());
    }
}
