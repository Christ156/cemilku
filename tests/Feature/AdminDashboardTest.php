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
        // Menonaktifkan event model untuk mencegah crash terkait SoftDeletes/ActivityLog
        Event::fake();
    }

    /**
     * Helper function to log in as an admin.
     *
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
        // Force verify the email
        $admin->markEmailAsVerified();

        $this->actingAs($admin);
    }

    /**
     * Test bahwa admin bisa mengakses dashboard.
     * @return void
     */
    public function test_admin_can_access_dashboard()
    {
        $this->loginAsAdmin();

        // Debug: check if user is actually logged in
        $this->assertAuthenticated();

        $response = $this->get(route('home'));

        // Debug: check the redirection location
        if ($response->status() === 302) {
            dd($response->headers->get('Location'));
        }

        $response->assertStatus(200);
        $response->assertSeeText(__('adminDashboard.dashboard'));
    }

    /**
     * TC1: Verifikasi Snack Count di Dashboard Admin.
     *
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

    /**
     * TC2: Verifikasi Collection Count di Dashboard Admin.
     *
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

    /**
     * TC3: Verifikasi Order Count di Dashboard Admin.
     *
     * @return void
     */
    public function test_tc3_verify_order_count()
    {
        $this->loginAsAdmin();

        // Buat user untuk order
        $user1 = User::create([
            'name' => 'Test User One',
            'email' => 'testuser1@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
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

    /**
     * Test Verifikasi User Count di Dashboard Admin.
     *
     * @return void
     */
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
            ]);
        }

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $expectedUserCount = User::count();
        $response->assertSeeText(__('adminDashboard.userCount'));
        $response->assertSeeText($expectedUserCount);
    }
}
