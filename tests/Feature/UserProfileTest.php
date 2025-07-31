<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile; // Diperlukan jika ada test upload gambar
use Illuminate\Support\Facades\Storage; // Diperlukan jika ada test upload gambar

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Login sebagai pengguna
     *
     * @return \App\Models\User
     */
    protected function loginAsUser(): User // Mengubah return type dari void menjadi User
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'gender' => 'Perempuan',
            'date_of_birth' => '1995-08-15',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'phone_number' => '081234567890', // Pastikan ini unik atau UserFactory sudah diatur unique()
            'role' => 'user', // Pastikan role juga diset jika ada
        ]);

        $this->actingAs($user); // Memanggil actingAs di sini

        return $user; // Mengembalikan instance user
    }

    /**
     * Test untuk memverifikasi update informasi profil pengguna lainnya.
     * Ini adalah test yang Anda berikan.
     */
    public function test_user_can_update_profile_info()
    {
        $user = $this->loginAsUser();

        // Data baru untuk update
        $newGender = 'Laki-laki';
        $newDateOfBirth = '2000-01-01';
        $newEmail = 'new_email@example.com';
        $newPhoneNumber = '089876543210';
        // $newName = 'User Baru'; // Username tidak bisa diedit

        // Menggunakan PUT request ke rute update profil dengan ID user
        // Sesuai dengan UserController Anda yang menerima ID di URL
        $response = $this->put("/profile/{$user->id}", [
            'gender' => $newGender,
            'dateofbirth' => $newDateOfBirth, // Nama input di controller Anda adalah 'dateofbirth'
            'email' => $newEmail,
            'telepon' => $newPhoneNumber, // Nama input di controller Anda adalah 'telepon'
            // 'name' => $newName, // Dihapus karena username tidak bisa diedit melalui form ini
        ]);

        // Assert bahwa request berhasil dan mengarah ke halaman profil kembali
        $response->assertStatus(302);
        // Redirect akan tetap ke slug nama lama jika nama tidak berubah
        $response->assertRedirect(route('profile', ['id' => $user->id, 'slug' => Str::slug($user->name)]));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui!'); // Asumsi ada pesan sukses

        // Verifikasi data di database telah diperbarui
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'gender' => $newGender,
            'date_of_birth' => $newDateOfBirth,
            'email' => $newEmail,
            'phone_number' => $newPhoneNumber, // Kolom di database adalah 'phone_number'
            'name' => $user->name, // Nama tidak berubah, jadi assert dengan nama asli user
        ]);

        // Verifikasi data yang diperbarui terlihat di halaman profil
        $response = $this->get(route('profile', ['id' => $user->id, 'slug' => Str::slug($user->name)]));
        $response->assertStatus(200);
        $response->assertSee($newGender);
        $response->assertSee(Carbon::parse($newDateOfBirth)->format('d/m/Y'));
        $response->assertSee($newEmail);
        $response->assertSee($newPhoneNumber);
        $response->assertSee($user->name); // Assert nama asli user
    }
}
