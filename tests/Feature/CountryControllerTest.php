<?php

namespace Tests\Feature;

use App\Http\Controllers\CountryController;
use App\Models\Category;
use App\Models\Country;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CountryControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Esegui i seeder per popolare il database con dati di esempio
        $this->seed();
    }

    public function testIndexEndpoint()
    {

        $response = $this->get('/countries');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data" => [
                '*' => [
                    "name",
                    "code",
                    "languages",
                    "categories",
                ],
            ],
            "status",
            "message",
        ]);
    }

    public function testShowEndpoint()
    {
        $country = Country::all()->random();

        $response = $this->get("/countries/{$country->code}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "data" => [
                "name",
                "code",
                "languages",
                "categories",
            ],
            "status",
            "message",
        ]);
    }

    public function testSyncCategoriesEndpoint()
    {
        $country = Country::all()->random();
        $category = Category::all()->random();

        $response = $this->post("/countries/{$country->code}/categories/{$category->name}");

        $response->assertStatus(201); // Categoria aggiunta
        $this->assertDatabaseHas('country_category', [
            'country_id' => $country->id,
            'category_id' => $category->id,
        ]);

        // Riprova per rimuovere la categoria
        $response = $this->post("/countries/{$country->code}/categories/{$category->name}");

        $response->assertStatus(200); // Categoria rimossa
        $this->assertDatabaseMissing('country_category', [
            'country_id' => $country->id,
            'category_id' => $category->id,
        ]);
    }
}
