<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Collection;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\CollectionSeeder;
use Database\Seeders\UserSeeder;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class CartPageTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $userCart;
    protected $product1;
    protected $product2;
    protected $address1;

    protected function setUp(): void
    {
        parent::setUp();

        // Run the seeders to populate the database
        $this->seed([
            UserSeeder::class,
            CollectionSeeder::class,
            AddressSeeder::class,
        ]);

        // Load specific user from seeder
        $this->user = User::where('email', 'user@example.com')->first();
        if ($this->user) {
            $this->user->email_verified_at = now();
            $this->user->save();
            $this->actingAs($this->user);
        } else {
            // Fallback if user@example.com isn't in seeder
            $this->user = User::create([
                'name' => 'Test User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
            $this->actingAs($this->user);
        }

        // Load products from seeder based on their ACTUAL names in your CollectionSeeder
        $this->product1 = Collection::where('name', 'Kongsi Tower')->first();
        $this->product2 = Collection::where('name', 'Congrats Tower')->first();

        $this->assertNotNull($this->product1, 'Product "Kongsi Tower" not found in seeded data. Please check your CollectionSeeder.');
        $this->assertNotNull($this->product2, 'Product "Congrats Tower" not found in seeded data. Please check your CollectionSeeder.');

        // Load address from seeder, assuming one is linked to this user
        $this->address1 = Address::where('user_id', $this->user->id)
                                  ->where('label', 'Rumah Utama')
                                  ->first();
        if (!$this->address1) {
             // Fallback: if seeder doesn't guarantee an address for this user, create one.
             $this->address1 = Address::create([
                'user_id' => $this->user->id,
                'label' => 'Rumah Utama',
                'receiver_name' => $this->user->name,
                'receiver_phone' => '081234567890',
                'address' => 'Jl. Cemara No. 1, Kel.Dago',
                'rt' => '01',
                'rw' => '02',
                'kelurahan_desa' => 'Dago',
                'kecamatan' => 'Coblong',
                'kota_kabupaten' => 'Bandung', // <--- CHANGE THIS LINE: 'kabupaten' to 'kota_kabupaten'
                'provinsi' => 'Jawa Barat',
                'kode_pos' => '40135',
            ]);
        }
        $this->assertNotNull($this->address1, 'Address for user not found in seeded data or could not be created.');

        // Create a fresh cart for the user for each test
        $this->userCart = Cart::create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);
    }

    // --- All your test methods below remain largely the same, but now they rely on seeded data ---

    /** @test */
    public function product_is_added_to_cart_and_displayed()
    {
        // 1. Tambah produk ke keranjang via POST request
        $response = $this->post(route('collection.to.cart', [
            'id_collection' => $this->product1->id,
            'quantity' => 1
        ]));

        $response->assertStatus(302);
        $response->assertRedirectContains(route('home'));

        // Pastikan produk ada di database CartItem
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price * 1,
        ]);
        $this->assertEquals(1, CartItem::where('cart_id', $this->userCart->id)->count());

        // 2. Akses halaman cart
        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSee($this->product1->name);
        $formattedPrice = 'Rp' . number_format($this->product1->price, 0, ',', '.');
        $cartResponse->assertSeeText($formattedPrice);
    }

    /** @test */
    public function multiple_products_can_be_added_and_displayed()
    {
        // Add product 1
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 1]));

        // Add product 2
        $this->post(route('collection.to.cart', ['id_collection' => $this->product2->id, 'quantity' => 2]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
        ]);
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
        ]);

        $this->assertEquals(2, CartItem::where('cart_id', $this->userCart->id)->count());

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSee($this->product1->name);
        $cartResponse->assertSee($this->product2->name);
        $cartResponse->assertSeeText('1 pcs');
        $cartResponse->assertSeeText('2 pcs');

        $formattedPrice1 = 'Rp' . number_format($this->product1->price, 0, ',', '.');
        $formattedPrice2 = 'Rp' . number_format($this->product2->price, 0, ',', '.');
        $cartResponse->assertSeeText($formattedPrice1);
        $cartResponse->assertSeeText($formattedPrice2);

        $expectedTotalPrice = ($this->product1->price * 1) + ($this->product2->price * 2);
        $formattedTotalPrice = 'Rp' . number_format($expectedTotalPrice, 0, ',', '.');
        $cartResponse->assertSeeTextInOrder(['Total', $formattedTotalPrice]);
    }

    /** @test */
    public function quantity_of_existing_product_in_cart_is_updated()
    {
        // Add product 1 once
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 1]));

        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
        ]);

        // Add product 1 again with more quantity (e.g., 2 more, total should be 3)
        $this->post(route('collection.to.cart', ['id_collection' => $this->product1->id, 'quantity' => 2]));

        // Assert the quantity is updated in the database
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 3, // 1 (initial) + 2 (added) = 3
            'total_price' => $this->product1->price * 3,
        ]);
        $this->assertEquals(1, CartItem::where('cart_id', $this->userCart->id)->count());

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSee($this->product1->name);
        $cartResponse->assertSeeText('3 pcs');
    }

    /** @test */
    public function product_can_be_removed_from_cart()
    {
        // Add a product to the cart first
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price,
        ]);

        $this->assertDatabaseHas('cart_items', ['collection_id' => $this->product1->id]);

        // Delete the product from cart via POST request
        $response = $this->post(route('cart.delete', [
            'id_cart_item' => CartItem::first()->id,
        ]));

        $response->assertStatus(302);
        $response->assertRedirectContains(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]));

        $this->assertDatabaseMissing('cart_items', ['collection_id' => $this->product1->id]);
        $this->assertEquals(0, CartItem::where('cart_id', $this->userCart->id)->count());

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));
        $cartResponse->assertOk();
        $cartResponse->assertDontSee($this->product1->name);
    }

    /** @test */
    public function cart_total_price_is_correctly_calculated_and_displayed()
    {
        // Add product 1 (1 pc)
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price * 1,
        ]);

        // Add product 2 (2 pcs)
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product2->id,
            'quantity' => 2,
            'total_price' => $this->product2->price * 2,
        ]);

        $expectedCartTotal = ($this->product1->price * 1) + ($this->product2->price * 2);
        $formattedExpectedCartTotal = 'Rp' . number_format($expectedCartTotal, 0, ',', '.');

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeTextInOrder(['Total Harga (2 Produk)', $formattedExpectedCartTotal]);
        $cartResponse->assertSeeText('Shipping Regular');
        $cartResponse->assertSeeText('Total');
    }

    /** @test */
    public function cart_is_empty_when_no_products()
    {
        // Ensure the cart is empty for this test
        CartItem::where('cart_id', $this->userCart->id)->delete();

        $userSlug = Str::slug($this->user->name ?? '');
        $cartResponse = $this->get(route('cart.index', ['id_user' => $this->user->id, 'slug' => $userSlug]));

        $cartResponse->assertOk();
        $cartResponse->assertSeeText('Keranjang Anda kosong');
        $cartResponse->assertDontSee($this->product1->name);
        $cartResponse->assertDontSee($this->product2->name);
        $cartResponse->assertSeeText('Total Harga (0 Produk)');
    }

    // --- TEST CASES: Checkout Flow ---

    /** @test */
    public function successful_checkout_creates_order_and_clears_cart()
    {
        // Add a product to the cart first
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price,
        ]);

        $initialCartItemCount = CartItem::where('cart_id', $this->userCart->id)->count();
        $this->assertEquals(1, $initialCartItemCount);

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);

        // Perform checkout
        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        $checkoutResponse->assertStatus(302);
        $checkoutResponse->assertRedirectContains(route('order.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]));

        // Assert order is created in database
        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total_price' => $this->product1->price + 20000, // Assuming Rp20.000 default shipping cost
            'payment_method' => 'Bank Transfer',
            'address_id' => $this->address1->id,
        ]);

        // Assert cart is empty after checkout
        $this->assertDatabaseMissing('cart_items', [
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
        ]);
        $this->assertEquals(0, CartItem::where('cart_id', $this->userCart->id)->count());
    }

    /** @test */
    public function checkout_with_no_products_redirects_back_with_error()
    {
        // Ensure the cart is empty
        CartItem::where('cart_id', $this->userCart->id)->delete();

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
        // Add a product to the cart
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price,
        ]);

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);

        // Attempt checkout without address_id
        $checkoutResponse = $this->post(route('checkout', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]), [
            'payment_method' => 'Bank Transfer',
            // 'address_id' is missing
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
        // Add a product to the cart
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 5, // Requesting more than available stock (initial stock 10)
            'total_price' => $this->product1->price * 5,
        ]);

        // Temporarily reduce product stock to make it "unavailable"
        $this->product1->stock = 2; // Set stock lower than cart quantity
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
        // Ensure there's a product in cart and an address exists
        CartItem::create([
            'cart_id' => $this->userCart->id,
            'collection_id' => $this->product1->id,
            'quantity' => 1,
            'total_price' => $this->product1->price,
        ]);
        // The address is created in setUp, so it exists

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $response = $this->get($cartUrl);
        $response->assertOk();

        $response->assertSeeText('Ongkos kirim');
        $response->assertSeeText('Rp');
        $response->assertSeeText('20.000');
    }

    // --- TEST CASES: Manajemen Alamat (dari halaman Cart) ---

    /** @test */
    public function new_address_is_added_with_valid_data_from_cart_page()
    {
        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $this->get($cartUrl)->assertOk();

        $newAddressData = [
            'receiver_name' => $this->faker->name,
            'receiver_phone' => '08' . $this->faker->numerify('##########'),
            'label' => 'Rumah Baru',
            'address' => $this->faker->streetAddress,
            'rt' => '01',
            'rw' => '01',
            'kelurahan_desa' => $this->faker->city,
            'kecamatan' => $this->faker->citySuffix,
            'kota_kabupaten' => $this->faker->state, // <--- CHANGE THIS LINE: 'kabupaten' to 'kota_kabupaten'
            'provinsi' => $this->faker->state,
            'post_code' => $this->faker->postcode,
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
        $initialAddressCount = Address::where('user_id', $this->user->id)->count();

        $cartUrl = route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name ?? '')]);
        $this->get($cartUrl)->assertOk();

        // Invalid data: Missing 'kelurahan_desa' which is required
        $invalidAddressData = [
            'receiver_name' => 'Invalid User',
            'receiver_phone' => '123',
            'label' => '',
            'address' => 'Jl. Pahlawan',
            'kecamatan' => 'Some Suburb',
            'kota_kabupaten' => 'Some City', // <--- CHANGE THIS LINE: 'kabupaten' to 'kota_kabupaten'
            'provinsi' => 'Some Province',
            'post_code' => '123',
        ];

        $response = $this->post(route('address.store', ['id_user' => $this->user->id]), $invalidAddressData);

        $response->assertStatus(302);
        $response->assertRedirect($cartUrl);

        $finalResponse = $this->get($response->headers->get('Location'));
        $finalResponse->assertSeeText('The receiver name field is required.');
        $finalResponse->assertSeeText('The receiver phone field must be at least 10 characters.');
        $finalResponse->assertSeeText('The label field is required.');
        $finalResponse->assertSeeText('The kelurahan desa field is required.');

        $this->assertEquals($initialAddressCount, Address::where('user_id', $this->user->id)->count());
    }
}
