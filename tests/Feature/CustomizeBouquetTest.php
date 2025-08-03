<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customize;
use App\Models\Snack;
use App\Models\CustomizeSnack;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Decoration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class CustomizeBouquetTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $snack1;
    protected $snack2;
    protected $snack3;
    protected $snack4;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'user',
        ]);
        $this->actingAs($this->user);

        $this->snack1 = Snack::create(['name' => 'Choco Delight', 'price' => 15000, 'stock' => 50]);
        $this->snack2 = Snack::create(['name' => 'Strawberry Bliss', 'price' => 12000, 'stock' => 40]);
        $this->snack3 = Snack::create(['name' => 'Vanilla Dream', 'price' => 10000, 'stock' => 60]);
        $this->snack4 = Snack::create(['name' => 'Nutty Crunch', 'price' => 18000, 'stock' => 30]);
        Snack::create(['name' => 'Lemon Burst', 'price' => 13000, 'stock' => 45]);

        Decoration::create(['name' => 'Standard Ribbon', 'price' => 5000]);
        Decoration::create(['name' => 'Premium Balloon', 'price' => 7500]);
    }

    /** @test */
    public function user_can_access_bouquet_customization_page()
    {
        $response = $this->get(route('customize-tower-bouquet.bouquet', ['type' => 'bouquet']));
        $response->assertStatus(200);
        $response->assertSee('Create Your Bouquet!');
    }

    /** @test */
    public function user_can_select_bouquet_color_and_proceed_to_layer_selection()
    {
        $placeholderName = 'Bouquet Custom Test ' . Str::random(5);
        $actualDbType = 'bouquet';
        $baseImagePath = 'assets/bouquet_base/White.png';
        $initialPrice = 50000;
        $controllerDefaultLayer = 3;

        $postDataStage1 = [
            'name' => $placeholderName,
            'base_image_path' => $baseImagePath,
            'price' => $initialPrice,
        ];

        $responsePostStage1 = $this->post(route('customer-tower-bouquet.store', ['type' => 'bouquet']), $postDataStage1);

        $responsePostStage1->assertStatus(302);
        $responsePostStage1->assertSessionHasNoErrors();

        $redirectUrl = $responsePostStage1->headers->get('Location');
        parse_str(parse_url($redirectUrl, PHP_URL_QUERY), $queryParameters);
        $customizeId = $queryParameters['customize_id'] ?? null;

        $this->assertNotNull($customizeId, 'Customize ID should be present in the redirect URL.');

        $customize = Customize::find($customizeId);
        $this->assertNotNull($customize, 'Customize record should be created after base selection.');

        $this->assertEquals($placeholderName, $customize->name);
        $this->assertEquals($actualDbType, $customize->type);
        $this->assertEquals($baseImagePath, $customize->base_image_path);
        $this->assertEquals($initialPrice, $customize->price);
        $this->assertEquals($controllerDefaultLayer, $customize->layer);

        $responsePostStage1->assertRedirect(route('customize-tower-bouquet.bouquet', ['stage' => 'layer', 'customize_id' => $customize->id]));
    }

    /** @test */
    public function user_can_complete_full_bouquet_customization_flow()
    {
        $initialName = 'Bouquet MultiStage ' . Str::random(5);
        $initialType = 'bouquet';
        $initialBaseImagePath = 'assets/bouquet_base/Blue.png';
        $initialPrice = 60000;
        $controllerDefaultLayer = 2;

        $customize = Customize::create([
            'name' => $initialName,
            'type' => $initialType,
            'base_image_path' => $initialBaseImagePath,
            'price' => $initialPrice,
            'layer' => $controllerDefaultLayer,
            'user_id' => $this->user->id,
        ]);

        $this->assertNotNull($customize, 'Initial customize record must be created.');

        // --- Stage 2: Select Layers ---
        $selectedNumberOfLayers = 3;
        $priceAfterLayer = 65000;

        $postDataStage2 = [
            'customize_id' => $customize->id,
            'layer' => $selectedNumberOfLayers,
            'price' => $priceAfterLayer,
        ];

        $responsePostStage2 = $this->post(route('customer-tower-bouquet.store', ['type' => 'bouquet']), $postDataStage2);

        $responsePostStage2->assertStatus(302);
        $responsePostStage2->assertSessionHasNoErrors();
        $responsePostStage2->assertRedirect(route('customize-tower-bouquet.bouquet', ['stage' => 'snack', 'customize_id' => $customize->id]));

        $customize->refresh();
        $this->assertEquals($selectedNumberOfLayers, $customize->layer);
        $this->assertEquals($priceAfterLayer, $customize->price);

        // --- Stage 3: Select Snacks (No Decoration for Bouquet) ---
        $snack1Id = Snack::first()->id;
        $finalPrice = 100000;

        $postDataStage3 = [
            'customize_id' => $customize->id,
            'layer' => $selectedNumberOfLayers,
            'snack_1' => $snack1Id,
            'price' => $finalPrice,
        ];

        $responsePostStage3 = $this->post(route('customer-tower-bouquet.store', ['type' => 'bouquet']), $postDataStage3);

        $responsePostStage3->assertStatus(302);
        $responsePostStage3->assertSessionHasNoErrors();
        $responsePostStage3->assertRedirect(route('cart.index', ['id_user' => $this->user->id, 'slug' => Str::slug(Auth::user()->name)]));
        $responsePostStage3->assertSessionHas('success', 'Kustomisasi berhasil ditambahkan ke keranjang!');

        $customize->refresh();
        $this->assertEquals($selectedNumberOfLayers, $customize->layer);
        $this->assertEquals($finalPrice, $customize->price);
        $this->assertEquals($initialType, $customize->type);
        $this->assertEquals($initialBaseImagePath, $customize->base_image_path);

        $this->assertDatabaseHas('customize_snacks', [
            'customize_id' => $customize->id,
            'snack_id' => $snack1Id,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'customize_id' => $customize->id,
            'quantity' => 1,
            'total_price' => $finalPrice,
        ]);
    }

    /** @test */
    public function incomplete_snack_selection_submits_successfully_and_only_includes_provided_snacks()
    {
        $tempCustomize = Customize::create([
            'user_id' => $this->user->id,
            'name' => 'Temp Incomplete Snacks Test',
            'type' => 'bouquet',
            'layer' => 3,
            'price' => 0,
        ]);

        $bouquetName = 'Incomplete Snacks';
        $numberOfLayersInRequest = 4;
        $baseImagePath = 'assets/bouquet_base/White.png';

        $expectedFinalPrice = 50000 + ($this->snack1->price * 5) + ($this->snack2->price * 5);

        $snackSelections = [
            'snack_1' => $this->snack1->id,
            'snack_2' => $this->snack2->id,
        ];

        $postData = array_merge([
            'customize_id' => $tempCustomize->id,
            'name' => $bouquetName,
            'base_image_path' => $baseImagePath,
            'layer' => $numberOfLayersInRequest,
            'price' => $expectedFinalPrice,
            'type' => 'bouquet',
        ], $snackSelections);

        $response = $this->post(route('customer-tower-bouquet.store', ['type' => 'bouquet']), $postData);

        $response->assertStatus(302);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('cart.index', [
            'id_user' => $this->user->id,
            'slug' => Str::slug(Auth::user()->name)
        ]));
        $response->assertSessionHas('success', 'Kustomisasi berhasil ditambahkan ke keranjang!');

        $tempCustomize->refresh();

        $this->assertEquals($numberOfLayersInRequest, $tempCustomize->layer); // Controller Anda update layer dengan nilai dari request
        $this->assertEquals($expectedFinalPrice, $tempCustomize->price);

        $this->assertDatabaseHas('customize_snacks', [
            'customize_id' => $tempCustomize->id,
            'snack_id' => $this->snack1->id,
            'quantity' => 5,
        ]);
        $this->assertDatabaseHas('customize_snacks', [
            'customize_id' => $tempCustomize->id,
            'snack_id' => $this->snack2->id,
            'quantity' => 5,
        ]);

        $this->assertDatabaseMissing('customize_snacks', [
            'customize_id' => $tempCustomize->id,
            'snack_id' => $this->snack3->id,
        ]);
        $this->assertDatabaseMissing('customize_snacks', [
            'customize_id' => $tempCustomize->id,
            'snack_id' => $this->snack4->id,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'customize_id' => $tempCustomize->id,
            'quantity' => 1,
            'total_price' => $tempCustomize->price,
        ]);
    }
}
