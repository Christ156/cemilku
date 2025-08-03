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


        $this->user = User::factory()->create([
            'name' => 'User Test',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]);


        $this->cart = Cart::create([
            'user_id' => $this->user->id,
            'is_active' => true,
        ]);


        $this->actingAs($this->user);


        Session::forget(['selectedBudget', 'selectedMood', 'mode']);
    }

    //Verifikasi Pemilihan Opsi Budget.

    public function test_tc1_verify_budget_option_selection()
    {

        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);

        $response->assertSeeText('Choose Your Budget');

        $this->assertEquals('Budget', session('mode'));

        $this->assertNull(session('selectedBudget'));


        $selectedBudget = 'Rp 75.000,00';
        $response = $this->post(route('set-budget'), [
            'budget' => $selectedBudget,
            '_token' => csrf_token(),
        ]);


        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('mysterybox'));

        $this->assertEquals($selectedBudget, session('selectedBudget'));
        $this->assertEquals('Mood', session('mode'));
    }

    //Verifikasi Transisi ke Langkah Berikutnya Menggunakan Tombol 'Next'.
    public function test_tc2_verify_transition_to_next_step_with_next_button()
    {
        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);
        $response->assertSeeText('Choose Your Budget');
        $this->assertEquals('Budget', session('mode'));

        $selectedBudget = 'Rp 100.000,00';
        $response = $this->post(route('set-budget'), [
            'budget' => $selectedBudget,
            '_token' => csrf_token(),
        ]);


        $response->assertRedirect(route('mysterybox'));
        $response->assertSessionHasNoErrors();
        $this->assertEquals($selectedBudget, session('selectedBudget'));
        $this->assertEquals('Mood', session('mode'));

        $response = $this->get(route('mysterybox'));
        $response->assertStatus(200);
        $response->assertSeeText('Choose Your Mood');

        $response->assertDontSeeText('Choose Your Budget');

        $this->assertEquals('Mood', session('mode'));
    }


    // Verify mood selection and add to cart flow
    public function test_tc3_verify_mood_selection_and_add_to_cart_flow()
    {

        $mysteryBox = MysteryBox::create([
            'budget' => 75000.00,
            'mood' => 'Romantic',
            'name' => 'Romantic Mystery Box',
            'image' => null,
        ]);


        $selectedBudget = 'Rp 75.000,00';
        Session::put('selectedBudget', $selectedBudget);
        Session::put('mode', 'Mood');


        $selectedMood = 'Romantic';
        $response = $this->post(route('set-mood'), [
            'mood' => $selectedMood,
            '_token' => csrf_token(),
        ]);


        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Mystery Box successfully added to your cart!',
            ]);


        $this->assertDatabaseHas('cart_items', [
            'mysterybox_id' => $mysteryBox->id,
            'quantity' => 1,
            'price' => 75000.00,
            'cart_id' => $this->cart->id,
        ]);


        $this->assertNull(Session::get('selectedBudget'));
        $this->assertNull(Session::get('selectedMood'));
    }

}
