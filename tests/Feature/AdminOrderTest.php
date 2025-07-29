<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
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



    //test 2

}
