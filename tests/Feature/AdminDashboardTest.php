<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Collection;
use App\Models\Snack;
use App\Models\OrderDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**

     * @return void
     */
    protected function loginAsAdmin(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
        $admin->markEmailAsVerified();

        $this->actingAs($admin);
    }



    // Verifikasi Snack Count di Dashboard Admin.
    /*
     * @return void
     */
    public function test_tc1_verify_snack_count()
    {
        $this->loginAsAdmin();

        for ($i = 0; $i < 5; $i++) {
            Snack::create([
                'name' => 'Snack Test ' . ($i + 1),
                'price' => 10000,
                'stock' => 50,
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $expectedSnackCount = Snack::count();
        $response->assertSeeText(__('adminDashboard.snackCount'));
        $response->assertSeeText($expectedSnackCount);
    }


    //Verifikasi Collection Count di Dashboard Admin.
    /*
     * @return void
     */
    public function test_tc2_verify_collection_count()
    {
        $this->loginAsAdmin();

        for ($i = 0; $i < 10; $i++) {
            Collection::create([
                'name' => 'Collection Test ' . ($i + 1),
                'category' => 'Birthday',
                'type' => 'tower',
                'price' => 50000,
                'stock' => 20,
                'layer' => 4,
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $expectedCollectionCount = Collection::count();
        $response->assertSeeText(__('adminDashboard.collectionCount'));
        $response->assertSeeText($expectedCollectionCount);
    }

    //Verifikasi Order Count di Dashboard Admin.
     /*
     * @return void
     */
    public function test_tc3_verify_order_count()
    {
        $this->loginAsAdmin();

        $user1 = User::create([
            'name' => 'Test User One',
            'email' => 'testuser1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_number' => '0812' . Str::random(8),
        ]);

        for ($i = 0; $i < 7; $i++) {
            Order::create([
                'user_id' => $user1->id,
                'total_price' => 150000,
                'payment_method' => 'BCA',
                'status' => 'completed',
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $expectedOrderCount = Order::count();
        $response->assertSeeText(__('adminDashboard.orderCount'));
        $response->assertSeeText($expectedOrderCount);
    }

    public function test_user_count_is_correctly_displayed()
    {
        $this->loginAsAdmin();

        for ($i = 0; $i < 15; $i++) {
            User::create([
                'name' => 'Test User ' . ($i + 1),
                'email' => 'testuser' . ($i + 1) . '@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'email_verified_at' => now(),
                'phone_number' => '0813' . Str::random(8),
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $expectedUserCount = User::count();
        $response->assertSeeText(__('adminDashboard.userCount'));
        $response->assertSeeText($expectedUserCount);
    }









    //verifikasi koleksi terlaris
    public function test_best_selling_collection_is_correctly_displayed()
    {
        $this->loginAsAdmin();


        $user = User::create([
            'name' => 'Test User for Orders',
            'email' => 'orderuser@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_number' => '0812' . Str::random(8),
        ]);


        $collectionA = Collection::create([
            'name' => 'Koleksi A',
            'price' => 100000,
            'stock' => 50,
        ]);
        $collectionB = Collection::create([
            'name' => 'Koleksi B',
            'price' => 120000,
            'stock' => 50,
        ]);
        $collectionC = Collection::create([
            'name' => 'Koleksi C',
            'price' => 90000,
            'stock' => 50,
        ]);


        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
        ])->orderDetails()->create([
            'collection_id' => $collectionA->id,
            'quantity' => 5,
            'price' => $collectionA->price,
        ]);


        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
        ])->orderDetails()->create([
            'collection_id' => $collectionB->id,
            'quantity' => 3,
            'price' => $collectionB->price,
        ]);

        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
        ])->orderDetails()->create([
            'collection_id' => $collectionA->id,
            'quantity' => 2,
            'price' => $collectionA->price,
        ]);


        Order::create([
            'user_id' => $user->id,
            'status' => 'completed',
        ])->orderDetails()->create([
            'collection_id' => $collectionC->id,
            'quantity' => 4,
            'price' => $collectionC->price,
        ]);


        $expectedSoldCountA = 7;
        $expectedSoldCountB = 3;
        $expectedSoldCountC = 4;


        $response = $this->get(route('home'));
        $response->assertStatus(200);


        $response->assertSeeTextInOrder([
            'Koleksi A',
            $expectedSoldCountA . ' Sold',
        ]);


        $response->assertSeeTextInOrder([
            'Koleksi A',
            $expectedSoldCountA . ' Sold',
            'Koleksi C',
            $expectedSoldCountC . ' Sold',
            'Koleksi B',
            $expectedSoldCountB . ' Sold',
        ]);
    }


    //verifikasi data pesanan terbaru
    public function test_latest_orders_are_displayed_correctly_on_dashboard()
    {
        $this->loginAsAdmin();

        $user1 = User::create([
            'name' => 'Test User One',
            'email' => 'testuser1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_number' => '0812' . Str::random(8),
        ]);

        $user2 = User::create([
            'name' => 'Test User Two',
            'email' => 'testuser2@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_number' => '0813' . Str::random(8),
        ]);

        $now = Carbon::now();

        $order1 = Order::create([
            'user_id' => $user1->id,
            'total_price' => 100000,
            'payment_method' => 'BCA',
            'status' => 'paid',
            'created_at' => $now->copy()->subDays(2),
        ]);

        $order2 = Order::create([
            'user_id' => $user2->id,
            'total_price' => 150000,
            'payment_method' => 'Mandiri',
            'status' => 'shipped',
            'created_at' => $now->copy()->subDay(1),
        ]);


        $order4 = Order::create([
            'user_id' => $user2->id,
            'total_price' => 50000,
            'payment_method' => 'BCA',
            'status' => 'pending',
            'created_at' => $now->copy()->subHours(2),
        ]);

        $order3 = Order::create([
            'user_id' => $user1->id,
            'total_price' => 200000,
            'payment_method' => 'BCA',
            'status' => 'completed',
            'created_at' => $now->copy(),
        ]);


        $response = $this->get(route('home'));
        $response->assertStatus(200);


        $response->assertSeeTextInOrder([
            (string) $order3->id,
            $user1->name,
            'Completed',
            $order3->created_at->format('d M Y'),


            (string) $order4->id,
            $user2->name,
            'Pending',
            $order4->created_at->format('d M Y'),

            (string) $order2->id,
            $user2->name,
            'Shipped',
            $order2->created_at->format('d M Y'),

            (string) $order1->id,
            $user1->name,
            'Paid',
            $order1->created_at->format('d M Y'),
        ]);
    }
}
