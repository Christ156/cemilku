<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\MysteryBox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

class MysteryBoxTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);

        // Create active cart for user
        $this->cart = Cart::create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);

        // Login user
        $this->actingAs($this->user);

        // Clear relevant sessions
        Session::forget(['selectedBudget', 'selectedMood', 'mode']);
    }

    /**
     * TC1: Verifikasi Pemilihan Opsi Budget.
     * Menguji bahwa budget yang dipilih tersimpan di session.
     * (Verifikasi visual tidak bisa dilakukan di test fitur).
     *
     * @return void
     */
    public function test_tc1_verify_budget_option_selection()
    {
        // 1. Navigasi ke halaman "Choose Your Budget".
        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);
        // Pastikan halaman menampilkan judul "Choose Your Budget"
        $response->assertSeeText('Choose Your Budget');
        // Periksa bahwa mode awal di session adalah 'Budget'
        $this->assertEquals('Budget', session('mode'));
        // Periksa bahwa belum ada budget yang dipilih di session
        $this->assertNull(session('selectedBudget'));

        // 2. Klik salah satu opsi budget (simulasi pengiriman form POST).
        $selectedBudget = 'Rp 75.000,00';
        $response = $this->post(route('set-budget'), [
            'budget' => $selectedBudget,
            '_token' => csrf_token(), // Penting untuk POST request
        ]);

        // 3. Periksa apakah opsi yang dipilih ditandai secara visual.
        //    (Di test fitur, kita memverifikasi bahwa budget berhasil disimpan di session)
        $response->assertSessionHasNoErrors(); // Pastikan tidak ada error validasi
        $response->assertRedirect(route('mysterybox')); // Pastikan ada redirect kembali ke halaman mysterybox

        // Setelah redirect, pastikan session 'selectedBudget' sudah terisi dengan nilai yang benar
        $this->assertEquals($selectedBudget, session('selectedBudget'));
        // Dan pastikan mode sudah berubah menjadi 'Mood'
        $this->assertEquals('Mood', session('mode'));
    }

    /**
     * TC2: Verifikasi Transisi ke Langkah Berikutnya Menggunakan Tombol 'Next'.
     * Menguji bahwa setelah memilih budget dan klik next, mode berubah dan view baru tampil.
     * (Visual 'Next' button klik tidak bisa disimulasikan, hanya hasil POST form).
     *
     * @return void
     */
    public function test_tc2_verify_transition_to_next_step_with_next_button()
    {
        // 1. Navigasi ke halaman "Choose Your Budget".
        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);
        $response->assertSeeText('Choose Your Budget');
        $this->assertEquals('Budget', session('mode'));

        // 2. Pilih salah satu opsi budget.
        $selectedBudget = 'Rp 100.000,00'; // Contoh budget lain
        $response = $this->post(route('set-budget'), [
            'budget' => $selectedBudget,
            '_token' => csrf_token(),
        ]);

        // 3. Klik tombol "Next".
        // Karena tombol "Next" di blade adalah tombol submit untuk #budgetForm,
        // simulasi klik tombol "Next" adalah pengiriman form POST ke 'set-budget'.
        // Ini sudah dilakukan di langkah 2 dan menghasilkan redirect.
        $response->assertRedirect(route('mysterybox')); // Pastikan redirect terjadi
        $response->assertSessionHasNoErrors();
        $this->assertEquals($selectedBudget, session('selectedBudget'));
        $this->assertEquals('Mood', session('mode')); // Mode harus berubah setelah set-budget

        // 4. Periksa apakah sistem berpindah ke langkah "Set Mood".
        // Lakukan GET request lagi ke halaman mysterybox setelah redirect
        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);
        // Pastikan halaman sekarang menampilkan judul "Choose Your Mood"
        $response->assertSeeText('Choose Your Mood');
        // Dan tidak lagi menampilkan judul "Choose Your Budget"
        $response->assertDontSeeText('Choose Your Budget');
        // Pastikan mode di session memang sudah 'Mood'
        $this->assertEquals('Mood', session('mode'));
    }


    /**
     * TC3: Verify mood selection and add to cart flow -> error
     */
    public function test_tc3_verify_mood_selection_and_add_to_cart_flow()
    {
        // 1. Create test mystery box with matching budget and mood
        $mysteryBox = MysteryBox::create([
            'budget' => 75000.00,
            'mood' => 'Romantic',
            'name' => 'Romantic Mystery Box',
            'image' => null,
        ]);

        // 2. Set session with budget format that matches controller expectations
        $selectedBudget = 'Rp 75.000,00'; // Menggunakan format mata uang
        Session::put('selectedBudget', $selectedBudget);
        Session::put('mode', 'Mood');

        // 3. Submit mood selection (case matches exactly with database)
        $selectedMood = 'Romantic';
        $response = $this->post(route('set-mood'), [
            'mood' => $selectedMood,
            '_token' => csrf_token(),
        ]);

        // 4. Assertions
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Mystery Box successfully added to your cart!',
            ]);

        // Verify cart item was created
        $this->assertDatabaseHas('cart_items', [
            'mysterybox_id' => $mysteryBox->id,
            'quantity' => 1,
            'price' => 75000.00,
            'cart_id' => $this->cart->id,
        ]);

        // Verify sessions were cleared
        $this->assertNull(Session::get('selectedBudget'));
        $this->assertNull(Session::get('selectedMood'));
    }

}
