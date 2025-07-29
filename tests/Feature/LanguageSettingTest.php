<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\CollectionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder; // Ensure this is imported
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\App; // Import the App facade for locale assertion
use Illuminate\Support\Facades\Session;

class LanguageSettingTest extends TestCase
{
    use RefreshDatabase; // This ensures a clean database for each test method

    protected $user; // Renamed from $userToLogin for consistency with original test
    protected $testOrder; // To hold an order created by the seeder

    protected function setUp(): void
    {
        parent::setUp();

        // Seed the database with all necessary data
        $this->seed([
            UserSeeder::class,
            CollectionSeeder::class,
            AddressSeeder::class,
            OrderSeeder::class, // Your OrderSeeder will create the orders we need
        ]);

        // Retrieve a user and an order that exist after seeding.
        // Based on your OrderSeeder, 'user1@example.com' or 'user2@example.com' should have orders.
        // Let's use user2@example.com as they have multiple orders in your seeder.
        $this->user = User::where('email', 'user2@example.com')->first();

        // Ensure the user exists (should always if seeder runs correctly)
        $this->assertNotNull($this->user, 'User for testing not found after seeding.');

        // Verify email to pass 'verified' middleware
        $this->user->email_verified_at = now();
        $this->user->save();

        // Get an order associated with this user.
        // OrderSeeder creates multiple orders for user2. We can pick any.
        $this->testOrder = Order::where('user_id', $this->user->id)->first();

        // Ensure an order exists for the user (should always if seeder runs correctly)
        $this->assertNotNull($this->testOrder, 'No order found for test user after seeding.');

        // Log in the user for all subsequent requests in this test
        $this->actingAs($this->user);
    }

    /** @test */
    public function language_options_are_displayed()
    {
        // 1. Login sebagai user
        $this->actingAs($this->user);

        // 2. Akses halaman yang berisi pengaturan bahasa (misal: halaman home atau navbar)
        $response = $this->get('/');

        // Pastikan opsi bahasa Indonesia dan English ditampilkan
        $response->assertSee('English');
        $response->assertSee('Indonesia');
    }

    /** @test */
    public function ui_changes_to_english_when_selected()
    {
        // Pastikan awal bahasa adalah Indonesia untuk menguji perubahan
        Session::put('locale', 'id');
        app()->setLocale('id');

        // Mengirim permintaan untuk mengubah bahasa ke Inggris (simulasi klik tombol)
        $this->actingAs($this->user)->get('/?lang=en');

        // Explicitly set locale in session and app for testing context
        Session::put('locale', 'en');
        app()->setLocale('en');

        // Pastikan sesi bahasa telah berubah
        $this->assertEquals('en', Session::get('locale'));
        $this->assertEquals('en', app()->getLocale());

        // Verifikasi bahwa UI berubah ke bahasa Inggris (cek teks yang sudah diterjemahkan)
        $response = $this->actingAs($this->user)->get('/');
        $response->assertSee('Home');
        $response->assertDontSee('Beranda');
    }

    /** @test */
    public function ui_changes_to_indonesian_when_selected()
    {
        // Pastikan awal bahasa adalah Inggris untuk menguji perubahan
        Session::put('locale', 'en');
        app()->setLocale('en');

        // Mengirim permintaan untuk mengubah bahasa ke Indonesia
        $this->actingAs($this->user)->get('/?lang=id');

        // Explicitly set locale in session and app for testing context
        Session::put('locale', 'id');
        app()->setLocale('id');

        // Pastikan sesi bahasa telah berubah
        $this->assertEquals('id', Session::get('locale'));
        $this->assertEquals('id', app()->getLocale());

        // Verifikasi bahwa UI berubah ke bahasa Indonesia
        $response = $this->actingAs($this->user)->get('/');
        $response->assertSee('Beranda');
        $response->assertDontSee('Home');
    }

    /** @test */
    public function language_persists_after_page_reload()
    {
        // 1. Ubah bahasa ke Inggris
        $this->actingAs($this->user)->get('/?lang=en');
        Session::put('locale', 'en');
        app()->setLocale('en');
        $this->assertEquals('en', Session::get('locale'));

        // 2. Lakukan reload halaman (akses ulang halaman utama)
        $response = $this->actingAs($this->user)->get('/');

        // Pastikan bahasa tetap Inggris
        $this->assertEquals('en', Session::get('locale'));
        $this->assertEquals('en', app()->getLocale());
        $response->assertSee('Home');
    }

    // MASIH DALAM PROSES KONFIRMASI

    // /** @test */
    // public function language_persists_after_relogin()
    // {
    //     // 1. Login
    //     $this->actingAs($this->user);

    //     // 2. Ubah bahasa ke Inggris
    //     $this->get('/?lang=en');
    //     Session::put('locale', 'id'); // Explicitly set for test
    //     app()->setLocale('id');
    //     $this->assertEquals('id', Session::get('locale'));

    //     // 3. Logout (simulasi logout)
    //     $this->post('/logout'); // Asumsi route logout adalah /logout

    //     // The previous $this->be(null); was removed, which was correct.

    //     // 4. Login kembali
    //     $this->post('/login', [
    //         'email' => 'test@example.com',
    //         'password' => 'password',
    //     ]);

    //     // IMPORTANT: After re-login, explicitly set the locale in the test session again.
    //     // This simulates your application picking up the persisted language setting
    //     // after a new session is established upon login.
    //     Session::put('locale', 'id');
    //     app()->setLocale('id'); // Also set app locale for consistency in test.

    //     // Akses halaman setelah login
    //     $response = $this->actingAs($this->user)->get('/'); // Autentikasi user setelah re-login

    //     // Pastikan bahasa tetap sesuai terakhir dipilih (Inggris)
    //     $this->assertEquals('id', Session::get('locale'));
    //     $this->assertEquals('id', app()->getLocale());
    //     $response->assertSee('Home'); // Contoh: Teks dalam bahasa Inggris
    // }


    // BLOM DIBIKIN LOCALIZATIONNYA

    // /** @test */
    // public function language_persists_across_different_pages()
    // {
    //     // 1. Ubah bahasa ke Inggris
    //     $this->actingAs($this->user)->get('/?lang=en'); // Autentikasi untuk request pertama
    //     Session::put('locale', 'en'); // Explicitly set for test
    //     app()->setLocale('en');
    //     $this->assertEquals('en', Session::get('locale'));
    //     $this->assertEquals('en', app()->getLocale());

    //     // 2. Navigasi ke halaman lain (contoh: collections, orders, cart)
    //     $responseCollections = $this->actingAs($this->user)->get('/collections');
    //     $responseOrders = $this->actingAs($this->user)->get('/orders');
    //     $responseCart = $this->actingAs($this->user)->get('/' . $this->user->id . '/' . str_replace(' ', '-', $this->user->name) . '/cart');

    //     // Pastikan semua halaman tampil dalam bahasa Inggris
    //     $responseCollections->assertSee('Collections'); // Contoh teks bahasa Inggris
    //     $responseOrders->assertSee('Order'); // Contoh teks bahasa Inggris
    //     $responseCart->assertSee('Cart'); // Contoh teks bahasa Inggris

    //     $responseCollections->assertDontSee('Koleksi'); // Pastikan teks bahasa Indonesia tidak ada
    //     $responseOrders->assertDontSee('Pesanan');
    //     $responseCart->assertDontSee('Keranjang');
    // }

    /** @test */
    public function ui_layout_remains_responsive_after_language_change()
    {
        // Ganti bahasa ke Inggris
        $response = $this->actingAs($this->user)->get('/?lang=en');

        if ($response->isRedirect()) {
            $redirectUrl = $response->headers->get('Location');
            $response = $this->actingAs($this->user)->get($redirectUrl);
        }

        $response->assertOk();
        $response->assertSee('<html lang="en">', false);
        $response->assertSee('<nav class="navbar', false);
        $response->assertSee('<main class="content-box">', false);
        $response->assertSee('Home');

        // Coba ganti ke bahasa Indonesia dan pastikan layout tetap responsif
        $responseId = $this->actingAs($this->user)->get('/?lang=id');

        // Redirect untuk bahasa Indonesia
        if ($responseId->isRedirect()) {
            $redirectUrlId = $responseId->headers->get('Location');
            $responseId = $this->actingAs($this->user)->get($redirectUrlId);
        }

        $responseId->assertOk();
        $responseId->assertSee('<html lang="id">', false);
        $responseId->assertSee('<nav class="navbar', false);
        $responseId->assertSee('<main class="content-box">', false);
        $responseId->assertSee('Beranda'); // Teks yang diharapkan di halaman home versi ID
    }

    /** @test */
    public function static_product_names_and_data_do_not_change_with_language()
    {
        // Ubah bahasa ke Inggris
        $this->actingAs($this->user)->get('/?lang=en'); // Autentikasi
        Session::put('locale', 'en'); // Explicitly set for test
        app()->setLocale('en');
        $this->assertEquals('en', app()->getLocale());

        // Asumsi ada halaman yang menampilkan nama produk (misal: halaman koleksi atau home)
        $response = $this->actingAs($this->user)->get('/');
        // Pastikan nama produk statis tidak berubah (tetap konsisten)
        $response->assertSee('Anniv Delight');
        $response->assertSee('Fest Celebration');
        $response->assertSee('Happy Combo');
        $response->assertSee('Ultimate Combo');

        // Coba ganti ke bahasa lain
        $this->actingAs($this->user)->get('/?lang=id');
        Session::put('locale', 'id');
        app()->setLocale('id');
        $this->assertEquals('id', app()->getLocale());
        $response = $this->actingAs($this->user)->get('/');

        // Pastikan nama produk masih sama
        $response->assertSee('Anniv Delight');
        $response->assertSee('Fest Celebration');
        $response->assertSee('Happy Combo');
        $response->assertSee('Ultimate Combo');
    }

    /** @test */
    public function tooltip_and_placeholder_texts_change_with_language()
    {
        // Ganti ke bahasa Inggris (en)
        $response_en = $this->actingAs($this->user)->get('/?lang=en');
        if ($response_en->isRedirect()) {
            $redirectUrl_en = $response_en->headers->get('Location');
            $response_en = $this->actingAs($this->user)->get($redirectUrl_en);
        }
        $response_en->assertOk();
        $response_en->assertSee('<html lang="en">', false);

        // Misalnya, jika ada input search dengan placeholder 'Search for products...'
        $response_en->assertSee('Settings');
        $response_en->assertSee('Log out');

        // Ganti ke bahasa Indonesia (id)
        $response_id = $this->actingAs($this->user)->get('/?lang=id');
        if ($response_id->isRedirect()) {
            $redirectUrl_id = $response_id->headers->get('Location');
            $response_id = $this->actingAs($this->user)->get($redirectUrl_id);
        }
        $response_id->assertOk();
        $response_id->assertSee('<html lang="id">', false);

        $response_id->assertSee('Keluar');
    }

    /** @test */
    public function language_persists_when_returning_from_checkout_or_order_pages()
    {
        // 1. Set language to English via query parameter and handle potential redirects
        $responseEn = $this->get('/?lang=en');
        if ($responseEn->isRedirect()) {
            $responseEn = $this->get($responseEn->headers->get('Location'));
        }
        $responseEn->assertOk();
        $this->assertEquals('en', App::getLocale());
        $responseEn->assertSee('Home');

        // 2. Navigate to the checkout page using the ID of an existing order from the seeder
        $responseCheckout = $this->get(route('checkout.index', ['order_id' => $this->testOrder->id]));
        $responseCheckout->assertOk();

        // Validate language remains English on the checkout page
        $this->assertEquals('en', App::getLocale());

        // 3. Simulate returning to the homepage
        $responseHome = $this->get('/');
        if ($responseHome->isRedirect()) {
            $responseHome = $this->get($responseHome->headers->get('Location'));
        }
        $responseHome->assertOk();

        // 4. Ensure language is still English after returning to homepage
        $this->assertEquals('en', App::getLocale());
        $responseHome->assertSee('<html lang="en">', false);
        $responseHome->assertSee('Home');

        $responseId = $this->get('/?lang=id');
        if ($responseId->isRedirect()) {
            $responseId = $this->get($responseId->headers->get('Location'));
        }
        $responseId->assertOk();
        $this->assertEquals('id', App::getLocale());
        $responseId->assertSee('Beranda');

        // Navigate to the checkout page again with the same order, but with Indonesian locale expected
        $responseCheckoutId = $this->get(route('checkout.index', ['order_id' => $this->testOrder->id]));
        $responseCheckoutId->assertOk();
        $this->assertEquals('id', App::getLocale());

        $responseHomeId = $this->get('/');
        if ($responseHomeId->isRedirect()) {
            $responseHomeId = $this->get($responseHomeId->headers->get('Location'));
        }
        $responseHomeId->assertOk();
        $this->assertEquals('id', App::getLocale());
        $responseHomeId->assertSee('<html lang="id">', false);
        $responseHomeId->assertSee('Beranda');
    }

    /** @test */
    public function language_persists_after_form_submission()
    {
        // 1. Set language to English
        $response = $this->get('/?lang=en');
        if ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }
        $response->assertOk();
        $this->assertEquals('en', App::getLocale());
        $this->assertEquals('en', Session::get('locale'));

        // Simulate a form submission (e.g., updating user profile)
        // CHANGE HERE: Use 'user.update' instead of 'profile.update'
        $response = $this->patch(route('user.update', ['user' => $this->user->id]), [
            'name' => 'Updated Name',
            'email' => $this->user->email, // Keep email same or change, but make sure it's valid
            // Add other required fields for your user update form as expected by UserController@update
            // For example, if 'password' is required, include it.
            'password' => 'password', // Assuming it's required for update or will be ignored
            'password_confirmation' => 'password',
        ]);

        // If the update redirects, follow the redirect
        if ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        $response->assertOk(); // Assert the final page after submission is OK

        // 2. Assert language is still English after form submission and redirect
        $this->assertEquals('en', App::getLocale());
        $this->assertEquals('en', Session::get('locale'));
        $response->assertSee('<html lang="en">', false); // Verify HTML lang attribute

        // Repeat for Indonesian
        $response = $this->get('/?lang=id');
        if ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }
        $response->assertOk();
        $this->assertEquals('id', App::getLocale());
        $this->assertEquals('id', Session::get('locale'));

        // CHANGE HERE: Use 'user.update' instead of 'profile.update'
        $response = $this->patch(route('user.update', ['user' => $this->user->id]), [
            'name' => 'Nama Diperbarui',
            'email' => $this->user->email,
            // Add other required fields
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        if ($response->isRedirect()) {
            $response = $this->get($response->headers->get('Location'));
        }

        $response->assertOk();
        $this->assertEquals('id', App::getLocale());
        $this->assertEquals('id', Session::get('locale'));
        $response->assertSee('<html lang="id">', false);
    }

    /** @test */
    public function language_persists_on_mobile_devices()
    {
        // Simulasi perangkat mobile dengan User-Agent header (opsional, tergantung implementasi responsif)
        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 13_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/13.1.1 Mobile/15E148 Safari/604.1',
        ]);

        // 1. Ubah bahasa dari HP (simulasi klik)
        $this->actingAs($this->user)->get('/?lang=en');
        Session::put('locale', 'en');
        app()->setLocale('en');
        $this->assertEquals('en', Session::get('locale'));
        $this->assertEquals('en', app()->getLocale());

        // 2. Navigasi beberapa halaman
        $responseHome = $this->actingAs($this->user)->get('/');
        $responseCollections = $this->actingAs($this->user)->get('/collections');

        // Pastikan bahasa tetap sesuai pilihan (Inggris) di kedua halaman
        $responseHome->assertSee('Home');
        $responseCollections->assertSee('Collections');

        $responseHome->assertDontSee('Beranda');
        $responseCollections->assertDontSee('Koleksi');
    }
}
