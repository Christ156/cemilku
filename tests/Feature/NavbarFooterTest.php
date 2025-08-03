<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;

class NavbarFooterTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

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

        $this->actingAs($this->user);
    }

    /** @test */
    public function test_home_navigation_button()
    {
        $this->get('/')->assertSee('Home');
    }

    /** @test */
    public function test_collections_navigation_button()
    {
        $this->get('/')->assertSee('Collections');
    }

    /** @test */
    public function test_order_navigation_button()
    {
        $this->get('/')->assertSee('Order');
    }

    /** @test */
    public function test_language_dropdown_button()
    {
        $this->get('/')->assertSee('Language');
    }

    /** @test */
    public function test_cart_navigation_button()
    {
        $user = $this->user;

        $slug = Str::slug($user->name);

        $cartUrl = route('cart.index', ['id_user' => $user->id, 'slug' => $slug]);

        $this->actingAs($user)
            ->get('/')
            ->assertSeeHtml('href="' . $cartUrl . '"');
    }

    /** @test */
    public function test_profile_dropdown_button()
    {
        $slug = Str::slug($this->user->name);
        $this->get('/')->assertSeeHtml('href="'. route('profile', ['id' => $this->user->id, 'slug' => $slug]) .'"');
    }

    /** @test */
    public function test_navbar_layout_ui()
    {
        $response = $this->get('/');
        $response->assertSee('Home');
        $response->assertSee('Collections');
        $response->assertSee('Order');

        $user = $this->user;

        $slug = Str::slug($user->name);

        $cartUrl = route('cart.index', ['id_user' => $user->id, 'slug' => $slug]);
        $response->assertSeeHtml('href="'. $cartUrl .'"');

        $profileUrl = route('profile', ['id' => $user->id, 'slug' => $slug]);
        $response->assertSeeHtml('href="'. $profileUrl .'"');
    }

    /** @test */
    public function test_mobile_hamburg_menu()
    {
        $this->get('/')->assertSeeHtml('<button class="navbar-toggler');
    }

    /** @test */
    public function test_drawer_menu_links()
    {
        $this->get('/')
            ->assertSee('Home')
            ->assertSee('Collections')
            ->assertSee('Order');
    }

    /** @test */
    public function test_logout_menu_is_present_when_authenticated()
    {
        $response = $this->get('/');
        $response->assertSeeHtml('<form action="' . route('logout') . '" method="post">');
    }

    /** @test */
    public function test_footer_email_clickable()
    {
        $this->get('/')->assertSee('mailto:cemilku@gmail.com');
    }
}
