<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Collection;
use App\Models\Snack;
use App\Models\Decoration;
use App\Models\OrderDetail;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
    }

    /**
     * Verifikasi Fungsionalitas Kolom 'Search' (Pencarian Valid)
     * Memastikan pencarian dengan kata kunci valid menampilkan hasil yang relevan.
     */
    public function test_can_search_order_by_valid_keyword()
    {
        $this->loginAsAdmin();

        // Buat data order untuk diuji
        $user1 = User::factory()->create(['name' => 'Rahmat Hidayat']);
        $user2 = User::factory()->create(['name' => 'Budi Santoso']);

        Order::create([
            'user_id' => $user1->id,
            'total_price' => 50000,
            'payment_method' => 'BCA',
            'status' => 'completed',

        ]);

        Order::create([
            'user_id' => $user2->id,
            'total_price' => 25000,
            'payment_method' => 'Mandiri',
            'status' => 'pending',

        ]);

        // Skenario 1: Pencarian berdasarkan username
        $response = $this->get('/admin/order?search=Rahmat');
        $response->assertStatus(200);
        $response->assertSee('Rahmat Hidayat');
        $response->assertDontSee('Budi Santoso');

        // Skenario 2: Pencarian berdasarkan payment method
        $response = $this->get('/admin/order?search=BCA');
        $response->assertStatus(200);
        $response->assertSee('BCA');
        $response->assertDontSee('Mandiri');
    }

    /**
     * Verifikasi Fungsionalitas Kolom 'Search' (Pencarian Tidak Valid)
     * Memastikan pencarian dengan kata kunci yang tidak ada menampilkan pesan 'No results found' atau tabel kosong.
     */
    public function test_search_order_no_results()
    {
        $this->loginAsAdmin();

        // Buat beberapa data order agar tabel tidak benar-benar kosong awalnya
        User::factory()->create(['name' => 'Adi']);
        Order::create([
            'user_id' => User::factory()->create(['name' => 'Joko'])->id,
            'total_price' => 10000,
            'payment_method' => 'BCA',
            'status' => 'completed',
        ]);

        $response = $this->get('/admin/order?search=KeywordTidakAda');
        $response->assertStatus(200);

        // Pastikan tidak ada hasil yang sesuai
        $response->assertDontSee('Joko');
        $response->assertDontSee('BCA');

        // Cek apakah tabel kosong
        $response->assertSee('<tbody></tbody>', false);
    }

    /**
     * Verifikasi Tombol 'X' (Clear) pada Kolom Search
     * Memastikan kolom search kosong dan semua item kembali ditampilkan setelah clear.
     */
    public function test_clear_search_button_resets_results()
    {
        $this->loginAsAdmin();

        // Buat beberapa data order
        $user1 = User::factory()->create(['name' => 'Customer A']);
        $user2 = User::factory()->create(['name' => 'Customer B']);

        Order::create([
            'user_id' => $user1->id,
            'total_price' => 10000,
            'payment_method' => 'BCA',
            'status' => 'pending',
        ]);

        Order::create([
            'user_id' => $user2->id,
            'total_price' => 20000,
            'payment_method' => 'Mandiri',
            'status' => 'completed',
        ]);

        // Simulasikan pencarian terlebih dahulu
        $this->get('/admin/order?search=Customer A')
            ->assertSee('Customer A')
            ->assertDontSee('Customer B');

        // Skenario: Klik tombol 'X' (Clear).
        // Dalam feature test, ini biasanya disimulasikan dengan request GET ke URL tanpa parameter search.
        // Asumsi: Tombol 'X' akan mengarahkan ulang atau me-load ulang halaman tanpa parameter 'search'.
        $response = $this->get('/admin/order'); // Mengakses halaman tanpa parameter search

        $response->assertStatus(200);
        // Pastikan semua data muncul kembali setelah "clear"
        $response->assertSee('Customer A');
        $response->assertSee('Customer B');
    }

    /**
     * TC1: Verifikasi Tampilan Data Order di Tabel Entri
     * Memastikan semua kolom yang diharapkan muncul dan data relevan ditampilkan.
     */
    public function test_can_see_order_list_and_data_in_table()
    {
        $this->loginAsAdmin();

        // --- Data untuk Order 1 (sesuai gambar) ---
        $user1 = User::factory()->create(['name' => 'User Satu']);
        $order1 = Order::create([
            'user_id' => $user1->id,
            'total_price' => 678000, // Rp678.000
            'payment_method' => 'BCA',
            'status' => 'paid', // Status di UI
        ]);

        // Buat Collection untuk produk Kongsi Tower
        $collection1 = Collection::create([
            'name' => 'Kongsi Tower',
            'price' => 339000, // Harga per item
            'description' => 'Deskripsi Kongsi Tower',
            'image' => 'kongsi_tower_collection.png',
        ]);

        // Buat OrderDetail dan pastikan collection_id diisi dengan benar
        OrderDetail::create([
            'order_id' => $order1->id,
            'collection_id' => $collection1->id, // Menyambungkan dengan Collection
            'quantity' => 2,
            'price' => $collection1->price,
            'product_type' => 'App\\Models\\Collection', // Menunjukkan jenis produk
        ]);

        // --- Data untuk Order 2 (sesuai gambar) ---
        $user2 = User::factory()->create(['name' => 'User Dua']);
        $order2 = Order::create([
            'user_id' => $user2->id,
            'total_price' => 623000, // Rp623.000
            'payment_method' => 'Mandiri',
            'status' => 'completed',
        ]);

        // Buat Collection untuk produk Kongkow Bouquet
        $collection2 = Collection::create([
            'name' => 'Kongkow Bouquet',
            'price' => 400000,
            'description' => 'Deskripsi Kongkow Bouquet',
            'image' => 'kongkow_bouquet_collection.png',
        ]);

        // Buat Collection untuk produk Kongsi Tower (jika berbeda instance atau harga)
        $collection3 = Collection::create([
            'name' => 'Kongsi Tower',
            'price' => 223000,
            'description' => 'Deskripsi Kongsi Tower 2',
            'image' => 'kongsi_tower_collection_2.png',
        ]);

        // OrderDetail untuk Kongkow Bouquet
        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $collection2->id, // Menyambungkan dengan Collection Kongkow Bouquet
            'quantity' => 1,
            'price' => $collection2->price,
            'product_type' => 'App\\Models\\Collection',
        ]);

        // OrderDetail untuk Kongsi Tower
        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $collection3->id, // Menyambungkan dengan Collection Kongsi Tower
            'quantity' => 1,
            'price' => $collection3->price,
            'product_type' => 'App\\Models\\Collection',
        ]);

        // Mengakses halaman dan memastikan status 200
        $response = $this->get('/admin/order');
        $response->assertStatus(200);

        // Verifikasi keberadaan header kolom
        $response->assertSee('Order ID');
        $response->assertSee('User Name');
        $response->assertSee('Address');
        $response->assertSee('Payment Method');
        $response->assertSee('Status');
        $response->assertSee('Total Price');
        $response->assertSee('Products');
        $response->assertSee('Action');

        // Verifikasi data order 1
        $response->assertSee('#' . $order1->id);
        $response->assertSee('User Satu');
        $response->assertSee('-'); // Jika address null
        $response->assertSee('BCA');
        $response->assertSee('Paid');
        $response->assertSee('Rp' . number_format($order1->total_price, 0, ',', '.')); // Format harga
        $response->assertSee('Kongsi Tower (x2)'); // Verifikasi produk di order 1
        $response->assertSee('Ship'); // Tombol 'Ship'

        // Verifikasi data order 2
        $response->assertSee('#' . $order2->id);
        $response->assertSee('User Dua');
        $response->assertSee('-'); // Jika address null
        $response->assertSee('Mandiri');
        $response->assertSee('Completed');
        $response->assertSee('Rp' . number_format($order2->total_price, 0, ',', '.')); // Format harga
        $response->assertSee('Kongkow Bouquet (x1)');
        $response->assertSee('Kongsi Tower (x1)');
        $response->assertSee('Ship'); // Tombol 'Ship'
    }




    public function test_edit_order_functionality()
    {
        // Membuat user untuk order
        $user = User::create([
            'name' => 'Test User',
            'email' => 'testuser@example.com',
            'password' => bcrypt('password123'),
        ]);

        // Membuat order menggunakan model Order dan mengisi user_id
        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 678000,
            'payment_method' => 'BCA',
            'status' => 'paid',  // Pastikan statusnya "paid"
        ]);

        // Mengakses halaman edit order untuk order tertentu
        $response = $this->get(route('order.edit', $order->id));

        // Verifikasi apakah status saat ini ada di form
        $response->assertSee('paid');

        // Mengubah status order
        $response = $this->post(route('order.ship', $order->id), [
            'status' => 'shipped',
        ]);

        // Verifikasi apakah status sudah diperbarui menjadi shipped
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        // Verifikasi bahwa pengalihan kembali terjadi setelah pembaruan
        $response->assertRedirect(route('admin.order.index'));
        $response->assertSessionHas('success', 'Order status updated to shipped.');
    }
}


