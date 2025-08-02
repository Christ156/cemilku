<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Snack;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Contracts\Auth\Authenticatable;

class AdminSnackPageTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin(): void
    {
        /** @var \App\Models\User $admin */
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        $this->actingAs($admin);
    }

    /** @test */
    public function test_can_see_snack_list()
    {
        $this->loginAsAdmin();

        Snack::create([
            'name' => 'Chiki Balls',
            'price' => 1000,
            'stock' => 50,
            'image' => 'chiki.png',
        ]);

        $response = $this->get('/admin/snack');
        $response->assertStatus(200);
        $response->assertSee('Chiki Balls');
    }

    /** @test */
    public function test_can_create_new_snack()
    {
        $this->loginAsAdmin();
        Storage::fake('public');

        $response = $this->post('/admin/snack', [
            'name' => 'Test Snack',
            'price' => 1500,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('snack.png'),
        ]);

        $response->assertRedirect('/admin/snack');
        $this->assertDatabaseHas('snacks', ['name' => 'Test Snack']);
    }

    /** @test */
    public function test_create_snack_requires_name()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/snack', [
            'name' => '',
            'price' => 1000,
            'stock' => 10,
            // 'image' is not required for validation error, so it's okay if it's missing here
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_can_edit_snack()
    {
        $this->loginAsAdmin();

        $snack = Snack::create([
            'name' => 'Original',
            'price' => 1000,
            'stock' => 10,
            'image' => 'original.png',
        ]);

        $response = $this->put("/admin/snack/{$snack->id}", [
            'name' => 'Updated',
            'price' => 2000,
            'stock' => 5,
            'image' => null,
        ]);

        $response->assertRedirect('/admin/snack');
        $this->assertDatabaseHas('snacks', ['name' => 'Updated']);
    }

    /** @test */
    public function test_can_soft_delete_snack()
    {
        $this->loginAsAdmin();

        $snack = Snack::create([
            'name' => 'ToDelete',
            'price' => 1000,
            'stock' => 5,
            'image' => 'delete.png',
        ]);

        $response = $this->delete("/admin/snack/{$snack->id}");
        $response->assertRedirect('/admin/snack');
        $this->assertSoftDeleted('snacks', ['id' => $snack->id]);
    }

    /** @test */
    public function test_can_search_snack()
    {
        $this->loginAsAdmin();

        Snack::create(['name' => 'Chiki Balls', 'price' => 1000, 'stock' => 10, 'image' => 'chiki.png']);
        Snack::create(['name' => 'Oreo', 'price' => 1500, 'stock' => 10, 'image' => 'oreo.png']);

        $response = $this->get('/admin/snack?search=Chiki');
        $response->assertStatus(200);
        $response->assertSee('Chiki Balls');
        $response->assertDontSee('Oreo');
    }

    /** @test */
    public function test_search_snack_no_result()
    {
        $this->loginAsAdmin();

        Snack::create(['name' => 'Chiki Balls', 'price' => 1000, 'stock' => 10, 'image' => 'chiki.png']);

        $response = $this->get('/admin/snack?search=Chiki123');
        $response->assertStatus(200);
        $response->assertDontSee('Chiki Balls');
        $response->assertSee('<tbody></tbody>', false);
    }

    /** @test */
    public function test_import_invalid_csv_format()
    {
        $this->loginAsAdmin();

        $file = UploadedFile::fake()->create('invalid.txt', 100, 'text/plain');

        $response = $this->post('/admin/snack/import', [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function test_export_snack_csv()
    {
        $this->loginAsAdmin();

        Snack::create([
            'name' => 'Oreo',
            'price' => 2000,
            'stock' => 5,
            'image' => 'oreo.png',
        ]);

        \Maatwebsite\Excel\Facades\Excel::fake();

        $response = $this->get('/admin/snack/export');

        $response->assertStatus(200);

        \Maatwebsite\Excel\Facades\Excel::assertDownloaded('snack.xlsx', function(\App\Exports\SnackExport $export) {
            return $export->collection()->contains('name', 'Oreo');
        });
    }

    /** @test */
    public function test_price_must_be_positive()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/snack', [
            'name' => 'Negative Snack',
            'price' => -1000,
            'stock' => 5,
        ]);

        $response->assertSessionHasErrors('price');
    }

    /** @test */
    public function test_duplicate_snack_name_should_fail()
    {
        $this->loginAsAdmin();

        Snack::create([
            'name' => 'UniqueName',
            'price' => 2000,
            'stock' => 10,
            'image' => 'img.png',
        ]);

        $response = $this->post('/admin/snack', [
            'name' => 'UniqueName',
            'price' => 2000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('dup.png'),
        ]);

        $response->assertSessionHasErrors('name');
    }
}
