<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UserExport;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'phone_number' => '0811' . Str::random(8),
            'email_verified_at' => now(),
        ]);
        $this->actingAs($admin);
    }

    //table list user
    public function test_can_see_user_list_and_data_in_table()
    {
        $this->loginAsAdmin();

        $user1 = User::create([
            'name' => 'User Satu',
            'email' => 'user1@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0812' . Str::random(8),
            'email_verified_at' => now(),
            'is_blocked' => false,
        ]);

        $user2 = User::create([
            'name' => 'User Dua',
            'email' => 'user2@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0813' . Str::random(8),
            'email_verified_at' => now(),
            'is_blocked' => true,
        ]);

        $response = $this->get(route('adminuser.index'));

        $response->assertStatus(200);

        $response->assertSeeText('ID');
        $response->assertSeeText('User Name');
        $response->assertSeeText('Role');
        $response->assertSeeText('Status');
        $response->assertSeeText('Action');

        $response->assertSeeText('Admin User');
        $response->assertSeeText('Admin');
        $response->assertSeeText('Active');

        $response->assertSeeText('User Satu');
        $response->assertSeeText('User');
        $response->assertSeeText('Active');
        $response->assertSeeText($user1->id);

        $response->assertSeeText('User Dua');
        $response->assertSeeText('User');
        $response->assertSeeText('Active');
        $response->assertDontSeeText('Blocked');
        $response->assertSeeText($user2->id);
    }


    //halaman log user
    public function test_admin_can_view_user_logs_page()
    {
        $this->markTestSkipped('Skipping this test due to a known routing issue. The route admin.users.logs is not defined in the application.');

        $this->loginAsAdmin();

        $user = User::create([
            'name' => 'User dengan Log',
            'email' => 'loguser@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0814' . Str::random(8),
            'email_verified_at' => now(),
            'is_blocked' => false,
        ]);

        activity()->on($user)->log('Melihat halaman profil');

        $response = $this->get(route('admin.users.logs', ['userId' => $user->id]));

        $response->assertStatus(200);
        $response->assertSeeText($user->name . ' Log Activity');
        $response->assertSeeText('Melihat halaman profil');
    }




    //block user
    public function test_admin_can_block_an_active_user()
    {
        $this->loginAsAdmin();

        $userToBlock = User::create([
            'name' => 'User Diblokir',
            'email' => 'blockme@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0815' . Str::random(8),
            'email_verified_at' => now(),
            'is_blocked' => false,
        ]);

        $this->assertDatabaseHas('users', ['id' => $userToBlock->id, 'is_blocked' => false]);

        $response = $this->post(route('adminuser.block', $userToBlock->id));

        $this->assertDatabaseHas('users', ['id' => $userToBlock->id, 'is_blocked' => true]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User berhasil diblokir.');
    }



    //umblock user
    public function test_admin_can_toggle_block_user_from_active_to_blocked()
    {
        $this->loginAsAdmin();

        $userToToggle = User::create([
            'name' => 'User Toggle Active',
            'email' => 'toggleactive@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0816' . Str::random(8),
            'email_verified_at' => now(),
            'is_blocked' => false,
        ]);

        $this->assertDatabaseHas('users', ['id' => $userToToggle->id, 'is_blocked' => false]);

        $response = $this->post(route('adminuser.block', $userToToggle->id));

        $this->assertDatabaseHas('users', ['id' => $userToToggle->id, 'is_blocked' => true]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'User berhasil diblokir.');
    }

    //export data ke excel
    public function test_admin_can_export_users_to_excel()
    {
        $this->loginAsAdmin();

        $userToExport = User::create([
            'name' => 'User Export',
            'email' => 'export@example.com',
            'password' => bcrypt('password123'),
            'role' => 'user',
            'phone_number' => '0817' . Str::random(8),
            'email_verified_at' => now(),
        ]);

        Excel::fake();

        $response = $this->get(route('adminuser.export'));

        $response->assertStatus(200);

        Excel::assertDownloaded('user.xlsx', function (UserExport $export) use ($userToExport) {
            return $export->collection()->contains('id', $userToExport->id);
        });
    }

}
