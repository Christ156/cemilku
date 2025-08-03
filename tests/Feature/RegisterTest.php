<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_register_with_valid_data()
    {
        $email = 'newuser@example.com';

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'birth_date' => '2000-01-01',
            'phone_num' => '081234567890',
            'email' => $email,
            'password' => 'securepassword',
            'password_confirmation' => 'securepassword',
        ]);

        $response->assertRedirect('/');
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    /** @test */
    public function register_with_existing_email()
    {
        $existingEmail = 'exist@example.com';

        if (!\App\Models\User::where('email', $existingEmail)->exists()) {
            \App\Models\User::create([
                'name' => 'Existing User',
                'email' => $existingEmail,
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        $response = $this->post('/register', [
            'name' => 'Theodore Jacobson',
            'email' => $existingEmail,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_register_with_invalid_phone_number()
    {
        $response = $this->post('/register', [
            'name' => 'Lisa Monroe',
            'birth_date' => '2000-01-01',
            'phone_num' => 'invalidphone',
            'email' => 'user2@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['phone_num']);
    }

    

    /** @test */
    public function test_register_with_missing_fields()
    {
        $response = $this->post('/register', []);

        $response->assertSessionHasErrors([
            'name',
            'birth_date',
            'phone_num',
            'email',
            'password',
        ]);
    }
}
