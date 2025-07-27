<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'role' => 'user'
        ]);

        $this->actingAs($user);
    }

    /** @test */
    public function test_homepage_loads_successfully()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_customize_menu_display_and_swipe()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('snackMystery');
        $response->assertSee('snackBouquet');
        $response->assertSee('snackTower');
    }

    /** @test */
    public function test_homepage_layout_elements_present()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('carouselExampleAutoplaying');
        $response->assertSee('Your Snack, Your Way');
        $response->assertSee('Why Choose Us?');
        $response->assertSee('Contact Us');
    }

    /** @test */
    public function test_footer_displayed_correctly()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('cemilku@gmail.com');
        $response->assertSee('Instagram');
        $response->assertSee('Facebook');
        $response->assertSee('© 2025 Cemilku');
    }

    /** @test */
    public function test_navbar_elements_present()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Collections');
        $response->assertSee('Order');
        $response->assertSee('Language');
    }
}
