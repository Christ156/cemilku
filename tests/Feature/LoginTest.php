<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    /** @test */
    public function test_login_with_valid_credentials()
    {
        $response = $this->post('/login', [
            'email' => 'validuser@example.com',
            'password' => 'validpassword',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    /** @test */
    public function test_login_with_wrong_password()
    {
        $response = $this->post('/login', [
            'email' => 'validuser@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function test_login_with_unregistered_email()
    {
        $response = $this->post('/login', [
            'email' => 'unregistered@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function test_login_with_empty_form()
    {
        $response = $this->post('/login', []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /** @test */
    public function test_login_with_sql_injection()
    {
        $response = $this->post('/login', [
            'email' => "' OR '1'='1",
            'password' => 'anything',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    /** @test */
    public function test_login_redirect_url_is_ignored()
    {
        $response = $this->post('/login?redirect=/admin', [
            'email' => 'secureuser@example.com',
            'password' => 'securepass',
        ]);

        $response->assertStatus(302); 
        $response->assertRedirect('/');
    }
}
