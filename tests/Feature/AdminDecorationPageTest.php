<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Decoration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DecorationExport; // Make sure to import your export class
use Illuminate\Contracts\Auth\Authenticatable; // Import this

class AdminDecorationPageTest extends TestCase
{
    use RefreshDatabase;

    protected function loginAsAdmin(): void // Add return type hint for clarity
    {
        /** @var \App\Models\User $admin */ // Add this PHPDoc for clarity
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin);
    }

    public function test_can_see_decoration_list()
    {
        $this->loginAsAdmin();

        Decoration::create([
            'name' => 'Golden Ribbon',
            'price' => 5000,
            'stock' => 10,
            'image' => 'golden.png'
        ]);

        $response = $this->get('/admin/decoration');
        $response->assertStatus(200);
        $response->assertSee('Golden Ribbon');
    }

    public function test_can_create_new_decoration()
    {
        $this->loginAsAdmin();

        Storage::fake('public');
        $file = UploadedFile::fake()->image('red.png');

        $response = $this->post('/admin/decoration', [
            'name' => 'Red Ribbon',
            'price' => 3000,
            'stock' => 10,
            'image' => $file,
        ]);

        $response->assertRedirect('/admin/decoration');
        $this->assertDatabaseHas('decorations', ['name' => 'Red Ribbon']);
    }

    public function test_create_decoration_requires_name()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/decoration', [
            'name' => '',
            'price' => 3000,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('r.png'),
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_edit_decoration()
    {
        $this->loginAsAdmin();

        $decoration = Decoration::create([
            'name' => 'Old Name',
            'price' => 1000,
            'stock' => 5,
            'image' => 'a.png'
        ]);

        $response = $this->put("/admin/decoration/{$decoration->id}", [
            'name' => 'New Name',
            'price' => 2000,
            'stock' => 6,
        ]);

        $response->assertRedirect('/admin/decoration');
        $this->assertDatabaseHas('decorations', ['name' => 'New Name']);
    }

    public function test_can_soft_delete_decoration()
    {
        $this->loginAsAdmin();

        $decoration = Decoration::create([
            'name' => 'Delete Me',
            'price' => 1000,
            'stock' => 5,
            'image' => 'a.png'
        ]);

        $response = $this->delete("/admin/decoration/{$decoration->id}");

        $response->assertRedirect('/admin/decoration');
        $this->assertSoftDeleted('decorations', ['name' => 'Delete Me']);
    }

    public function test_can_search_decoration()
    {
        $this->loginAsAdmin();

        Decoration::create(['name' => 'Blue Bow', 'price' => 1000, 'stock' => 5, 'image' => 'blue.png']);

        $response = $this->get('/admin/decoration?search=Blue');
        $response->assertStatus(200);
        $response->assertSee('Blue Bow');
    }

    public function test_search_decoration_no_result()
    {
        $this->loginAsAdmin();

        Decoration::create(['name' => 'Ribbon Red', 'price' => 500, 'stock' => 5, 'image' => 'ribbon.png']);

        $response = $this->get('/admin/decoration?search=Blue');
        $response->assertStatus(200);
        $response->assertDontSee('Ribbon Red');
    }

    public function test_import_invalid_csv_format()
    {
        $this->loginAsAdmin();

        Storage::fake('local');

        $file = UploadedFile::fake()->create('invalid.txt', 100, 'text/plain');

        $response = $this->post('/admin/decoration/import', [
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_export_decoration_excel()
    {
        $this->loginAsAdmin();

        Decoration::create([
            'name' => 'Golden Wrap',
            'price' => 50000,
            'stock' => 20,
            'image' => 'golden_wrap.png'
        ]);

        // Mock the Excel facade to prevent actual file download
        // and instead return a DownloadResponse which the test can then inspect.
        Excel::fake();

        $response = $this->get('/admin/decoration/export');

        $response->assertStatus(200);

        // Assert that the Excel::download method was called with the correct arguments
        Excel::assertDownloaded('decoration.xlsx', function(DecorationExport $export) {
            // You can add assertions here about the data in the export if needed
            // For example, to check if the 'Golden Wrap' decoration is included
            return $export->collection()->contains('name', 'Golden Wrap');
        });

        // The previous content checks are no longer necessary as Excel::fake handles the download assertion.
        // We're asserting that the download *would have happened* correctly, not checking the byte size of a streamed response.
    }

    public function test_price_must_be_positive()
    {
        $this->loginAsAdmin();

        $response = $this->post('/admin/decoration', [
            'name' => 'Negative Price',
            'price' => -100,
            'stock' => 10,
            'image' => UploadedFile::fake()->image('bad.png'),
        ]);

        $response->assertSessionHasErrors('price');
    }

    public function test_duplicate_decoration_name_should_fail()
    {
        $this->loginAsAdmin();

        Decoration::create([
            'name' => 'SameName',
            'price' => 3000,
            'stock' => 10,
            'image' => 'same.png'
        ]);

        $response = $this->post('/admin/decoration', [
            'name' => 'SameName',
            'price' => 4000,
            'stock' => 20,
            'image' => UploadedFile::fake()->image('dup.png'),
        ]);

        $response->assertSessionHasErrors('name');
    }
}
