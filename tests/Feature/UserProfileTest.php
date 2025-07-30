<?php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login sebagai pengguna
     */
    protected function loginAsUser(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'gender' => 'Perempuan',          // Menambahkan gender
            'date_of_birth' => '1995-08-15',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
        ]);

        $this->actingAs($user);
    }



    public function test_navigate_to_user_info()
    {
        $this->loginAsUser();

        // Buat pengguna untuk menguji secara langsung menggunakan create()
        $user = User::create([
            'name' => 'Rahmat Hidayat',  // Pastikan 'name' diisi dengan data yang sesuai
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'), // Password harus di-hash
        ]);

        // Buat slug dari nama pengguna
        $slug = Str::slug($user->name);  // Contoh slug dari nama pengguna

        // Kirim dua parameter sesuai dengan yang diharapkan oleh controller
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // Verifikasi pengguna tetap berada di halaman 'User Info'
        $response->assertStatus(200);
        $response->assertSee('User Info'); // Sesuaikan dengan teks yang ada di halaman
    }

    public function test_navigate_to_address_using_carousel()
    {
        $this->loginAsUser();

        // Buat pengguna untuk menguji
        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'),
        ]);

        $slug = Str::slug($user->name); // Membuat slug berdasarkan nama pengguna

        // Kirim dua parameter sesuai dengan yang diharapkan oleh controller
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // Verifikasi status 200 untuk halaman
        $response->assertStatus(200);

        // Verifikasi apakah bagian carousel alamat (address) aktif saat diklik
        $response->assertSeeInOrder(['Address']); // Pastikan ada teks 'Address' di dalam HTML
    }


    /**
     * Test navigasi ke halaman 'FAQ'
     */
    public function test_navigate_to_faq_using_carousel()
    {
        $this->loginAsUser();

        // Buat pengguna untuk menguji
        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'),
        ]);

        $slug = Str::slug($user->name); // Membuat slug berdasarkan nama pengguna

        // Kirim dua parameter sesuai dengan yang diharapkan oleh controller
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // Verifikasi status 200 untuk halaman
        $response->assertStatus(200);

        // Klik pada menu FAQ dan pastikan bagian FAQ muncul
        $response->assertSeeInOrder(['Frequently Asked Questions', 'What is a Snack Tower?']); // Ganti sesuai dengan teks yang ada pada halaman FAQ
    }





    public function test_verify_user_name_and_role()
    {
        // Simulasikan login sebagai pengguna
        $this->loginAsUser();

        // Ambil data pengguna
        $user = Auth::user();  // Gunakan pengguna yang sedang login
        $slug = Str::slug($user->name);  // Slug untuk URL

        // Navigasi ke halaman "User Info"
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // Verifikasi status 200 untuk halaman
        $response->assertStatus(200);

        // Verifikasi bahwa Nama Pengguna dan Peran yang ditampilkan sesuai dengan data di database
        $response->assertSee($user->name);  // Verifikasi Nama Pengguna
        $response->assertSee($user->role);  // Verifikasi Peran Pengguna (misalnya: "User")
    }




    public function test_verify_user_info_displayed_correctly()
    {
        // 1. Membuat pengguna untuk pengujian menggunakan loginAsUser()
        $this->loginAsUser(); // Memanggil method loginAsUser()

        // 2. Mendapatkan data pengguna yang telah login
        $user = User::where('email', 'user@example.com')->first(); // Ambil pengguna berdasarkan email

        // 3. Navigasi ke halaman "User Info" menggunakan ID dan slug pengguna
        $slug = Str::slug($user->name);  // Membuat slug untuk nama pengguna
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // 4. Memeriksa status halaman
        $response->assertStatus(200);

        // 5. Memeriksa Gender yang ditampilkan
        $response->assertSee($user->gender);

        // 6. Memeriksa umur yang dihitung dari tanggal lahir
        $age = Carbon::parse($user->date_of_birth)->age;
        $response->assertSee($age);

        // 7. Memeriksa Email yang ditampilkan
        $response->assertSee($user->email);

        // 8. Memeriksa Nomor Telepon yang ditampilkan
        $response->assertSee($user->phone_number);
    }



    public function test_edit_button_shows_pop_up_with_user_data()
    {
        // Login sebagai pengguna
        $this->loginAsUser();

        // Mendapatkan data pengguna
        $user = User::where('email', 'user@example.com')->first();
        $slug = Str::slug($user->name);

        // Navigasi ke halaman User Info
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        // Memastikan halaman ditemukan
        $response->assertStatus(200);

        // Memastikan tombol "Edit" ada di halaman
        $response->assertSee('Edit');

        // Klik tombol "Edit"
        $response = $this->actingAs($user)->get('/profile/' . $user->id . '/' . $slug . '/edit');

        // Memastikan modal pop-up "Edit User Info" muncul dengan Nama User yang terisi
        $response->assertSee($user->name);  // Pastikan Nama User terisi di form edit

        // Validasi bahwa Peran tidak bisa diedit
        $response->assertDontSee($user->role);  // Pastikan Peran tidak ada dalam form
    }



}
