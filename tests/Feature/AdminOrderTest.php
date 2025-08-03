<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Collection;
use App\Models\Snack;
use App\Models\Decoration;
use App\Models\OrderDetail;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\OrderExport;
use Illuminate\Support\Str;

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

    // Verifikasi Fungsionalitas Kolom 'Search' (Pencarian Valid)
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


        $response = $this->get('/admin/order?search=Rahmat');
        $response->assertStatus(200);
        $response->assertSee('Rahmat Hidayat');
        $response->assertDontSee('Budi Santoso');


        $response = $this->get('/admin/order?search=BCA');
        $response->assertStatus(200);
        $response->assertSee('BCA');
        $response->assertDontSee('Mandiri');
    }


    // Verifikasi Fungsionalitas Kolom 'Search' (Pencarian Tidak Valid)

    public function test_search_order_no_results()
    {
        $this->loginAsAdmin();


        User::factory()->create(['name' => 'Adi']);
        Order::create([
            'user_id' => User::factory()->create(['name' => 'Joko'])->id,
            'total_price' => 10000,
            'payment_method' => 'BCA',
            'status' => 'completed',
        ]);

        $response = $this->get('/admin/order?search=KeywordTidakAda');
        $response->assertStatus(200);


        $response->assertDontSee('Joko');
        $response->assertDontSee('BCA');


        $response->assertSee('<tbody></tbody>', false);
    }


    //Verifikasi Tombol 'X' (Clear) pada Kolom Search
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

        $this->get('/admin/order?search=Customer A')
            ->assertSee('Customer A')
            ->assertDontSee('Customer B');


        $response = $this->get('/admin/order');

        $response->assertStatus(200);
        $response->assertSee('Customer A');
        $response->assertSee('Customer B');
    }

    //Verifikasi Tampilan Data Order di Tabel Entri
    public function test_can_see_order_list_and_data_in_table()
    {
        $this->loginAsAdmin();


        $user1 = User::factory()->create(['name' => 'User Satu']);
        $order1 = Order::create([
            'user_id' => $user1->id,
            'total_price' => 678000,
            'payment_method' => 'BCA',
            'status' => 'paid',
        ]);


        $collection1 = Collection::create([
            'name' => 'Kongsi Tower',
            'price' => 339000,
            'description' => 'Deskripsi Kongsi Tower',
            'image' => 'kongsi_tower_collection.png',
        ]);


        OrderDetail::create([
            'order_id' => $order1->id,
            'collection_id' => $collection1->id,
            'quantity' => 2,
            'price' => $collection1->price,
            'product_type' => 'App\\Models\\Collection',
        ]);


        $user2 = User::factory()->create(['name' => 'User Dua']);
        $order2 = Order::create([
            'user_id' => $user2->id,
            'total_price' => 623000,
            'payment_method' => 'Mandiri',
            'status' => 'completed',
        ]);


        $collection2 = Collection::create([
            'name' => 'Kongkow Bouquet',
            'price' => 400000,
            'description' => 'Deskripsi Kongkow Bouquet',
            'image' => 'kongkow_bouquet_collection.png',
        ]);


        $collection3 = Collection::create([
            'name' => 'Kongsi Tower',
            'price' => 223000,
            'description' => 'Deskripsi Kongsi Tower 2',
            'image' => 'kongsi_tower_collection_2.png',
        ]);


        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $collection2->id,
            'quantity' => 1,
            'price' => $collection2->price,
            'product_type' => 'App\\Models\\Collection',
        ]);


        OrderDetail::create([
            'order_id' => $order2->id,
            'collection_id' => $collection3->id,
            'quantity' => 1,
            'price' => $collection3->price,
            'product_type' => 'App\\Models\\Collection',
        ]);


        $response = $this->get('/admin/order');
        $response->assertStatus(200);


        $response->assertSee('Order ID');
        $response->assertSee('User Name');
        $response->assertSee('Address');
        $response->assertSee('Payment Method');
        $response->assertSee('Status');
        $response->assertSee('Total Price');
        $response->assertSee('Products');
        $response->assertSee('Action');


        $response->assertSee('#' . $order1->id);
        $response->assertSee('User Satu');
        $response->assertSee('-');
        $response->assertSee('BCA');
        $response->assertSee('Paid');
        $response->assertSee('Rp' . number_format($order1->total_price, 0, ',', '.'));
        $response->assertSee('Kongsi Tower (x2)');
        $response->assertSee('Ship');


        $response->assertSee('#' . $order2->id);
        $response->assertSee('User Dua');
        $response->assertSee('-');
        $response->assertSee('Mandiri');
        $response->assertSee('Completed');
        $response->assertSee('Rp' . number_format($order2->total_price, 0, ',', '.'));
        $response->assertSee('Kongkow Bouquet (x1)');
        $response->assertSee('Kongsi Tower (x1)');
        $response->assertSee('Ship');
    }



    // Memastikan admin bisa mengubah status pesanan dari 'paid' ke 'shipped'
    public function test_admin_can_ship_a_paid_order()
    {

        $this->loginAsAdmin();


        $user = User::factory()->create();
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'paid', 
        ]);

        $response = $this->post(route('adminorder.ship', $order->id));
        $order->refresh();
        $this->assertEquals('shipped', $order->status);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Status berhasil diubah menjadi shipped.');
    }

    //memastikan bisa export

    public function test_admin_can_export_orders_to_excel()
    {
        $this->loginAsAdmin();


        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat.hidayat@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0812' . Str::random(8),
            'email_verified_at' => now(),
        ]);

        $order = Order::create([
            'user_id' => $user->id,
            'total_price' => 150000,
            'payment_method' => 'BCA',
            'status' => 'completed',
        ]);


        $this->assertDatabaseHas('orders', ['id' => $order->id]);

        Excel::fake();


        $response = $this->get(route('adminorder.export'));

        $response->assertStatus(200);


        Excel::assertDownloaded('orders.xlsx', function (OrderExport $export) {
            return $export->collection()->count() === 1;
        });
    }
}
