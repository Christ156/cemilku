<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Collection; // Import model Collection
use App\Models\Cart; // Import model Cart
use App\Models\CartItem; // Import model CartItem
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str; // Diperlukan untuk Str::slug jika digunakan di rute

class CollectionDetailTest extends TestCase
{
    use RefreshDatabase;

    protected User $user; // Deklarasikan properti user

    /**
     * Set up the test environment.
     * This method is called before each test method.
     *
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
            'phone_number' => '081234567890', // Pastikan phone_number unik
        ]);

        $this->actingAs($this->user); // Login sebagai user yang baru dibuat
    }

    /**
     * search item ->depreceated
     *
     * @return void
     */
    public function test_user_can_search_collection_by_name()
    {
        // User sudah login otomatis via setUp()

        // Buat data koleksi untuk diuji
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

        // Skenario 1: Pencarian berdasarkan kata kunci yang cocok dengan nama item
        $response = $this->post('/collections/search?search=Aidin');
        $response->assertStatus(200);
        $response->assertSee('Aidin Tower');
        $response->assertDontSee('Sukses Bouquet');
        $response->assertDontSee('Hampers Natal Ceria');

        // Skenario 2: Pencarian berdasarkan kata kunci lain
        $response = $this->post('/collections/search?search=Sukses');
        $response->assertStatus(200);
        $response->assertSee('Sukses Bouquet');
        $response->assertDontSee('Aidin Tower');
        $response->assertDontSee('Hampers Natal Ceria');
    }


    /**
     * Tombol '+' (Link Kartu Koleksi) Mengarahkan ke Halaman Detail yang Benar.-> depreceated
     */
    public function test_plus_button_redirects_to_correct_detail_page()
    {


        // Buat data koleksi yang akan diuji
        // Menggunakan Collection::create() secara langsung karena tidak ingin factory
        $collection = Collection::create([
            'name' => 'Kongsi Tower',
            'type' => 'tower',
            'category' => 'Chinese New Year', // Sesuaikan dengan kategori yang ditampilkan di index
            'description' => 'Deskripsi Kongsi Tower yang indah.',
            'price' => 339000,
            'stock' => 100,
            'image' => 'kongsi_tower_test.png',
            'layer' => '4', // Sesuaikan jika ada kolom 'layer'
        ]);

        // Simulasikan GET request ke halaman index koleksi
        // Ini akan memuat halaman yang berisi link ke detail koleksi
        $response = $this->get('/collections');
        $response->assertStatus(200);

        // Sekarang, simulasikan klik pada link kartu koleksi
        // Link ini mengarah ke route('collections.show', $item->id)
        $detailResponse = $this->get(route('collections.show', $collection->id));

        // Verifikasi bahwa request berhasil diarahkan ke halaman detail (status 200 OK)
        $detailResponse->assertStatus(200);

        // Verifikasi bahwa halaman detail menampilkan informasi yang benar dari koleksi
        $detailResponse->assertSee($collection->name); // Judul koleksi
        $detailResponse->assertSee('Rp ' . number_format($collection->price, 0, ',', '.')); // Harga
        $detailResponse->assertSee($collection->description); // Deskripsi
        $detailResponse->assertSee($collection->category); // Kategori (jika ditampilkan)


    }


    /**
     * Verifikasi Fungsionalitas Quantity Selector (+/-) dan Validasi Stok, item masuk ke cart-> depreceated
     *
     */
    public function test_add_to_cart_quantity_validation_and_stock_limit()
    {
        // User sudah login dari setUp(), gunakan $this->user

        // Buat koleksi dengan stok terbatas untuk pengujian
        $collection = Collection::create([
            'name' => 'Test Collection',
            'type' => 'bouquet',
            'category' => 'Birthday',
            'description' => 'Deskripsi singkat.',
            'price' => 100000,
            'stock' => 5, // Stok terbatas untuk pengujian
            'image' => 'test_collection.png',
            'layer' => '3',
        ]);

        // Skenario 1: Tambahkan kuantitas valid (dalam batas stok)
        // Kuantitas 3 dikirim sebagai parameter rute
        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 3]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),
            // 'quantity' tidak lagi dikirim di body karena diambil dari route parameter
        ]);
        // Gunakan $this->user untuk assertRedirect
        $response->assertRedirect(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug($this->user->name)]));
        $response->assertSessionHas('success', 'Produk berhasil ditambahkan ke keranjang!');
        $this->assertDatabaseHas('cart_items', [
            // Gunakan $this->user untuk mencari Cart
            'cart_id' => Cart::where('user_id', $this->user->id)->first()->id,
            'collection_id' => $collection->id,
            'quantity' => 3,
            'price' => $collection->price,
            'total_price' => $collection->price * 3,
        ]);

        // Skenario 4: Coba tambahkan kuantitas melebihi stok yang tersedia (misal: stok 5, coba tambah 6)
        // Kuantitas 6 dikirim sebagai parameter rute
        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 6]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHas('error', 'Kuantitas tidak valid atau melebihi stok yang tersedia.'); // Asumsi pesan error dari controller
        $response->assertStatus(302); // Redirect back on error

        // Skenario 5: Tambahkan lagi ke item yang sudah ada, melebihi stok total
        // Cart sudah ada dengan 3 item dari Skenario 1. Coba tambah 3 lagi (total jadi 6)
        // Kuantitas 3 dikirim sebagai parameter rute
        $response = $this->post(route('collection.to.cart', ['id_collection' => $collection->id, 'quantity' => 3]), [
            'collection_id' => $collection->id,
            'price' => $collection->price,
            '_token' => csrf_token(),
        ]);
        $response->assertSessionHas('error', 'Penambahan kuantitas melebihi stok yang tersedia (' . $collection->stock . ' pcs).'); // Asumsi pesan error dari controller
        $response->assertStatus(302); // Redirect back on error

        // Pastikan kuantitas di database tidak berubah dari 3
        $this->assertDatabaseHas('cart_items', [
            // Gunakan $this->user untuk mencari Cart
            'cart_id' => Cart::where('user_id', $this->user->id)->first()->id,
            'collection_id' => $collection->id,
            'quantity' => 3,
        ]);
    }
}
