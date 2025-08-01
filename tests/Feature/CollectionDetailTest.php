<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Collection;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class CollectionDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Inisialisasi user di setUp
        $this->user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
            'phone_number' => '081234567890',
        ]);

        $this->actingAs($this->user);
    }

    // search item ->depreceated
    /*
     * @return void
     */
    public function test_user_can_search_collection_by_name()
    {

        Collection::create([
            'name' => 'Aidin Tower',
            'category' => 'Ramadhan',
            'type' => 'tower',
            'description' => 'Tower snack untuk Ramadhan.',
            'price' => 120000.00,
            'stock' => 10,
            'image' => null,
            'layer' => 4,
        ]);

        Collection::create([
            'name' => 'Sukses Bouquet',
            'category' => 'Graduation',
            'type' => 'bouquet',
            'description' => 'Buketan bunga untuk wisuda.',
            'price' => 80000.00,
            'stock' => 5,
            'image' => null,
            'layer' => 3,
        ]);

        Collection::create([
            'name' => 'Hampers Natal Ceria',
            'category' => 'Christmas',
            'type' => 'tower',
            'description' => 'Hampers spesial Natal.',
            'price' => 180000.00,
            'stock' => 8,
            'image' => null,
            'layer' => 5,
        ]);


        $response = $this->post('/collections/search?search=Aidin');
        $response->assertStatus(200);
        $response->assertSee('Aidin Tower');
        $response->assertDontSee('Sukses Bouquet');
        $response->assertDontSee('Hampers Natal Ceria');


        $response = $this->post('/collections/search?search=Sukses');
        $response->assertStatus(200);
        $response->assertSee('Sukses Bouquet');
        $response->assertDontSee('Aidin Tower');
        $response->assertDontSee('Hampers Natal Ceria');
    }


    //Tombol '+' (Link Kartu Koleksi) Mengarahkan ke Halaman Detail yang Benar.-> depreceated

    public function test_plus_button_redirects_to_correct_detail_page()
    {



        $collection = Collection::create([
            'name' => 'Kongsi Tower',
            'type' => 'tower',
            'category' => 'Chinese New Year',
            'description' => 'Deskripsi Kongsi Tower yang indah.',
            'price' => 339000,
            'stock' => 100,
            'image' => 'kongsi_tower_test.png',
            'layer' => '4',
        ]);


        $response = $this->get('/collections');
        $response->assertStatus(200);


        $detailResponse = $this->get(route('collections.show', $collection->id));


        $detailResponse->assertStatus(200);


        $detailResponse->assertSee($collection->name);
        $detailResponse->assertSee('Rp ' . number_format($collection->price, 0, ',', '.'));
        $detailResponse->assertSee($collection->description);
        $detailResponse->assertSee($collection->category);


    }


    //Verifikasi Fungsionalitas Quantity Selector (+/-) dan Validasi Stok, item masuk ke cart-> depreceated

    public function test_add_to_cart_quantity_validation_and_stock_limit()
    {

        $collection = Collection::create([
            'name' => 'Test Collection',
            'type' => 'bouquet',
            'category' => 'Birthday',
            'description' => 'Deskripsi singkat.',
            'price' => 100000,
            'stock' => 5,
            'image' => 'test_collection.png',
            'layer' => '3',
        ]);


        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 3]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),

        ]);

        $response->assertRedirect(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name)]));
        $response->assertSessionHas('success', 'Produk berhasil ditambahkan ke keranjang!');
        $this->assertDatabaseHas('cart_items', [
            'cart_id' => Cart::where('user_id', $this->user->id)->first()->id,
            'collection_id' => $collection->id,
            'quantity' => 3,
            'price' => $collection->price,
            'total_price' => $collection->price * 3,
        ]);


        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 6]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHas('error', 'Kuantitas tidak valid atau melebihi stok yang tersedia.'); // Asumsi pesan error dari controller
        $response->assertStatus(302);


        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 3]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHas('error', 'Penambahan kuantitas melebihi stok yang tersedia (' . $collection->stock . ' pcs).'); // Asumsi pesan error dari controller
        $response->assertStatus(302);


        $this->assertDatabaseHas('cart_items', [

            'cart_id' => Cart::where('user_id', $this->user->id)->first()->id,
            'collection_id' => $collection->id,
            'quantity' => 3,
        ]);
    }
}
