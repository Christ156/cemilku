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

    
}
