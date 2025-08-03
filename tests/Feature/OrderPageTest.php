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
use Illuminate\Support\Facades\App;

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

    public function tab_pending_only_shows_pending_orders()
    {
        // Set the locale for this specific test
        App::setLocale('id');

        $user = \App\Models\User::where('email', 'user1@example.com')->first();
        $user->email_verified_at = now();
        $user->save();

        $this->actingAs($user)
            ->get('/orders?status=pending')
            ->assertDontSee('Kongsi Tower')
            ->assertDontSee('Kongkow Bouquet')
            ->assertSee('You haven’t placed any orders yet.');
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
        $response->assertSee('You haven’t placed any orders yet.');
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
        $response->assertSee('You haven’t placed any orders yet.');
    }
}
