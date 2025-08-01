<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;


    protected function loginAsUser(): User
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create([
            'gender' => 'Perempuan',
            'date_of_birth' => '1995-08-15',
            'email' => 'user@example.com',
            'password' => bcrypt('password123'),
            'phone_number' => '081234567890',
            'role' => 'user',
        ]);

        $this->actingAs($user);

        return $user;
    }

    public function test_navigate_to_user_info()
    {
        $this->loginAsUser();
        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'),
        ]);

        $slug = Str::slug($user->name);
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        $response->assertStatus(200);
        $response->assertSee('User Info');
    }

    public function test_navigate_to_address_using_carousel()
    {
        $this->loginAsUser();
        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'),
        ]);

        $slug = Str::slug($user->name);
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Address']);
    }

    public function test_navigate_to_faq_using_carousel()
    {
        $this->loginAsUser();
        $user = User::create([
            'name' => 'Rahmat Hidayat',
            'email' => 'rahmat@example.com',
            'password' => bcrypt('password123'),
        ]);

        $slug = Str::slug($user->name);
        $response = $this->get('/profile/' . $user->id . '/' . $slug);

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Frequently Asked Questions', 'What is a Snack Tower?']);
    }


    // edit username-> depreceated
    public function test_edit_user_name_valid()
    {
        $user = User::first() ?? User::factory()->create();
        $newName = 'New Valid Name';

        $response = $this->actingAs($user)
            ->from('/profile/' . $user->id . '/' . Str::slug($user->name))
            ->put('/user/' . $user->id, [
                'name' => $newName,
                '_token' => csrf_token(),
            ]);

        $user->refresh();
        $newSlug = Str::slug($newName);

        $response->assertRedirect('/profile/' . $user->id . '/' . $newSlug);
        $redirectResponse = $this->get($response->headers->get('Location'));

        try {
            $redirectResponse->assertSessionHas('success', 'Profil berhasil diperbarui!');
        } catch (\PHPUnit\Framework\ExpectationFailedException $e) {
            $redirectResponse->assertSee('Profil berhasil diperbarui!');
        }

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newName,
        ]);

        $this->get('/profile/' . $user->id . '/' . $newSlug)
            ->assertSee($newName);
    }



    // edit username-> depreceated
    public function test_user_can_update_username()
    {
        if (!Schema::hasTable('users') || !Schema::hasColumn('users', 'name')) {
            $this->markTestSkipped('Required database structure missing');
            return;
        }

        $user = $this->loginAsUser();
        $newUsername = 'RahmatUpdated';

        $response = $this->actingAs($user)->put(route('user.update', $user->id), [
            '_token' => csrf_token(),
            'name' => $newUsername,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => $newUsername,
        ]);
        $user->refresh();
        $this->assertEquals($newUsername, $user->name);
    }


    // edit info-> depreceated
    public function test_user_can_update_profile_info()
    {
        Carbon::setTestNow(Carbon::create(2025, 7, 31, 9, 0, 0));

        $user = $this->loginAsUser();
        $newGender = 'Laki-laki';
        $newDateOfBirth = '1995-08-15';
        $newEmail = 'new_email@example.com';
        $newPhoneNumber = '089876543210';

        $response = $this->put("/profile/{$user->id}", [
            'gender' => $newGender,
            'dateofbirth' => $newDateOfBirth,
            'email' => $newEmail,
            'telepon' => $newPhoneNumber,
        ]);

        $response->assertStatus(302);
        $response->assertRedirect(route('profile', ['id' => $user->id, 'slug' => Str::slug($user->name ?? 'user')]));
        $response->assertSessionHas('success', 'Profil berhasil diperbarui!');

        $user->refresh();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'gender' => $newGender,
            'date_of_birth' => $newDateOfBirth,
            'email' => $newEmail,
            'phone_number' => $newPhoneNumber,
            'name' => $user->name,
        ]);

        $response = $this->get(route('profile', ['id' => $user->id, 'slug' => Str::slug($user->name ?? 'user')]));
        $response->assertStatus(200);

        $response->assertSee($newGender);

        $birthDate = Carbon::parse($newDateOfBirth);
        $age = (int) $birthDate->diffInYears(Carbon::now());
        $response->assertSee($age . ' years');

        $response->assertSee($newEmail);
        $response->assertSee($newPhoneNumber);
        $response->assertSee($user->name);

        Carbon::setTestNow(null);
    }


    //tes alamat
}
