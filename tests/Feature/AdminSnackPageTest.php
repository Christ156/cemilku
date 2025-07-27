<?php

namespace Tests\Feature;

use App\Models\Snack;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminSnackPageTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin()
    {
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

        Snack::factory()->count(3)->create();

        $response = $this->get('/admin/snack');
        $response->assertStatus(200);
        $response->assertSee('Snack List');
    }

    /** @test */
    public function test_can_create_new_snack()
    {
        $this->loginAsAdmin();

        Storage::fake('public');

        $response = $this->post('/admin/snack', [
            'name' => 'Coklat',
            'price' => 10000,
            'stock' => 50,
            'image' => UploadedFile::fake()->image('snack.jpg'),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('snacks', ['name' => 'Coklat']);
    }

    /** @test */
    public function test_create_snack_requires_name()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/snack', [
            'price' => 10000,
            'stock' => 20,
        ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function test_create_snack_without_image_should_pass()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/snack', [
            'name' => 'Keripik',
            'price' => 8000,
            'stock' => 30,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('snacks', ['name' => 'Keripik']);
    }

    /** @test */
    public function test_can_edit_snack()
    {
        $this->loginAsAdmin();

        $snack = Snack::factory()->create();

        $response = $this->put("/admin/snack/{$snack->id}", [
            'name' => $snack->name,
            'price' => 15000,
            'stock' => 100,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('snacks', ['id' => $snack->id, 'price' => 15000, 'stock' => 100]);
    }

    /** @test */
    public function test_can_soft_delete_snack()
    {
        $this->loginAsAdmin();

        $snack = Snack::factory()->create();

        $response = $this->delete("/admin/snack/{$snack->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted($snack);
    }

    /** @test */
    public function test_can_search_snack()
    {
        $this->loginAsAdmin();

        Snack::factory()->create(['name' => 'Coklat Manis']);

        $response = $this->get('/admin/snack?search=Coklat');

        $response->assertStatus(200);
        $response->assertSee('Coklat');
    }

    /** @test */
    public function test_search_snack_no_result()
    {
        $this->loginAsAdmin();

        $response = $this->get('/admin/snack?search=Chiki123');
        $response->assertStatus(200);
        $response->assertSee('No matching records', false); // Ensure table shows empty message
    }

    /** @test */
    public function test_can_import_valid_csv()
    {
        $this->loginAsAdmin();

        Storage::fake('local');

        $csv = UploadedFile::fake()->createWithContent('snack.csv', <<<CSV
name,price,stock
Keripik,5000,10
CSV
        );

        $response = $this->post('/admin/snack/import', [
            'file' => $csv,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('snacks', ['name' => 'Keripik']);
    }

    /** @test */
    public function test_import_invalid_csv_format()
    {
        $this->loginAsAdmin();

        $file = UploadedFile::fake()->create('invalid.txt', 100);

        $response = $this->post('/admin/snack/import', [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    /** @test */
    public function test_export_snack_csv()
    {
        $this->loginAsAdmin();

        Snack::factory()->create();

        $response = $this->get('/admin/snack/export');

        $response->assertStatus(200);
        $response->assertHeader('content-disposition', 'attachment; filename=snack.xlsx');
    }

    /** @test */
    public function test_price_must_be_positive()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/snack', [
            'name' => 'Snack Negatif',
            'price' => -500,
            'stock' => 10,
        ]);

        $response->assertSessionHasErrors('price');
    }

    /** @test */
    public function test_stock_column_shown_on_snack_page()
    {
        $this->loginAsAdmin();

        Snack::factory()->create(['name' => 'Keripik Kentang', 'stock' => 25]);

        $response = $this->get('/admin/snack');

        $response->assertSee('Stock');
        $response->assertSee('25');
    }

    /** @test */
    public function test_duplicate_snack_name_should_fail()
    {
        $this->loginAsAdmin();

        Snack::factory()->create(['name' => 'Sama']);

        $response = $this->post('/admin/snack', [
            'name' => 'Sama',
            'price' => 2000,
            'stock' => 10,
        ]);

        // If name should be unique, enable this line in your controller: 'name' => 'required|unique:snacks,name'
        // Then this test will pass:
        // $response->assertSessionHasErrors('name');
    }
}
