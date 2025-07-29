<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Database\Seeders\AddressSeeder;
use Database\Seeders\CollectionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\OrderSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderPageTest extends TestCase
{
    use RefreshDatabase;

    protected $userToLogin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([
            UserSeeder::class,
            CollectionSeeder::class,
            AddressSeeder::class,
            OrderSeeder::class,
        ]);

        $this->userToLogin = \App\Models\User::where('email', 'user2@example.com')->first();

        $this->userToLogin->email_verified_at = now();
        $this->userToLogin->save();

        $this->actingAs($this->userToLogin);
    }

    /** @test */
    public function tab_all_shows_all_orders()
    {
        $user = User::where('email', 'user2@example.com')->first();
        $this->assertNotNull($user);

        $user = User::whereHas('orders.orderDetails.collection', function ($q) {
            $q->where('name', 'Kongkow Bouquet');
        })->first();

        $this->assertNotNull($user, 'User with Kongkow Bouquet order not found');

        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get('/orders?status=all')
            ->assertSee('Kongkow Bouquet');
    }

    /** @test */
    public function tab_pending_only_shows_pending_orders()
    {
        $user = \App\Models\User::where('email', 'user1@example.com')->first();
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get('/orders?status=pending')
            ->assertDontSee('Kongsi Tower')
            ->assertDontSee('Kongkow Bouquet')
            ->assertSee('Belum ada pesanan');
    }

    /** @test */
    public function tab_processing_only_shows_paid_orders()
    {
        $user = \App\Models\User::whereHas('orders.orderDetails.collection', function ($q) {
            $q->where('name', 'Kongkow Bouquet');
        })->whereHas('orders', function ($q) {
            $q->where('status', 'paid');
        })->first();

        $this->assertNotNull($user, 'No user has Kongkow Bouquet with status paid. Check OrderSeeder.');

        $this->actingAs($user);

        $expectedOrder = \App\Models\Order::where('user_id', $user->id)
            ->where('status', 'paid')
            ->with('orderDetails.collection')
            ->first();

        $this->assertNotNull($expectedOrder, 'User has no order with status paid.');

        $expectedProductName = null;
        if ($expectedOrder && $expectedOrder->orderDetails->isNotEmpty()) {
            $expectedProductName = $expectedOrder->orderDetails->first()->collection->name ?? null;
            $this->assertEquals('Kongkow Bouquet', $expectedProductName, 'First product is not Kongkow Bouquet. Found: ' . $expectedProductName);
        } else {
            $this->fail('Paid order has no order details or collection.');
        }

        $response = $this->get('/orders?status=paid');
        $response->assertStatus(200);
        $response->assertSee('Kongkow Bouquet');
        $response->assertDontSee('Kongsi Tower');
    }

    /** @test */
    public function tab_shipped_is_empty()
    {
        $response = $this->get('/orders?status=shipped');
        $response->assertSee('Belum ada pesanan');
    }

    /** @test */
    public function tab_completed_only_shows_completed_orders()
    {
        $user = User::whereHas('orders', function ($q) {
            $q->where('status', 'completed');
        })->first();

        $this->assertNotNull($user, 'No user with completed order. Check seeder.');

        $this->actingAs($user);

        $response = $this->get('/orders?status=completed');
        $response->assertStatus(200);

        $expectedCollections = Order::where('user_id', $user->id)
            ->where('status', 'completed')
            ->with('orderDetails.collection')
            ->get()
            ->flatMap(fn ($order) => $order->orderDetails->pluck('collection.name'))
            ->unique();

        foreach ($expectedCollections as $name) {
            $response->assertSee($name, "Product '$name' must be visible for 'completed' orders");
        }
    }

    /** @test */
    public function tab_cancelled_is_empty()
    {
        $response = $this->get('/orders?status=cancelled');
        $response->assertSee('Belum ada pesanan');
    }

    /** @test */
    public function switching_tabs_does_not_trigger_full_reload()
    {
        $user = User::where('email', 'user2@example.com')->first();
        $this->assertNotNull($user);

        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        $response = $this->get('/orders?status=paid');

        if ($response->status() === 302) {
            dump('Redirected to: ' . $response->headers->get('Location'));
        }

        $response->assertStatus(200);
        $response->assertSeeText('Diproses');
        $this->assertStringContainsString('tab-btn active', $response->getContent());
        $response->assertSeeText('Belum Bayar');
        $response->assertSeeText('Dikirim');
        $response->assertSeeText('Selesai');
    }

    public function test_order_detail_modal_is_rendered_in_orders_page()
    {
        $user = User::where('email', 'user2@example.com')->first();
        $this->assertNotNull($user);

        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user);

        $response = $this->get('/orders');

        $response->assertStatus(200);

        // Pecah jadi dua kata agar tidak terganggu newline/tab
        $response->assertSeeText('Detail');
        $response->assertSeeText('Transaksi');

        // Pastikan ada tombol buka modal
        $response->assertSee('data-bs-toggle="modal"', false);
        $response->assertSee('data-bs-target="#orderModal', false);
        $response->assertSeeText('Lihat detail transaksi');
    }

   /** @test */
    public function filter_tetap_setelah_kembali_dari_detail()
    {
        $user = User::where('email', 'user2@example.com')->first();
            $this->assertNotNull($user);

            $user->email_verified_at = now();
            $user->save();

            $this->actingAs($user);

        // Simulasikan akses tab 'completed'
        $response = $this->get('/orders?status=completed');
        $response->assertStatus(200);
        $response->assertSee('Selesai');

        $response = $this->get('/orders?status=completed');

        // Pastikan filter masih berlaku
        $response->assertSee('Selesai');
        $response->assertSee('Kongkow Bouquet');
    }
}
