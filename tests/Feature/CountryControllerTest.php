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

        $response = $this->get(env('APP_URL') . '/api/countries');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "result" => [
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

        $response = $this->get(env('APP_URL') . "/api/country/{$country->code}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "result" => [
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
        if ($country->categories->contains($category->id)) {
            $expectedStatus = 200;
        }

        $response = $this->post(env('APP_URL') . "/api/country/{$country->code}/{$category->name}");


        if ($country->categories->contains($category->id)) {
            $expectedStatus = 200;
            $response->assertStatus($expectedStatus);
            $this->assertDatabaseMissing('category_country', [
                'country_id' => $country->id,
                'category_id' => $category->id,
            ]);
        } else {
            $expectedStatus = 201;
            $response->assertStatus($expectedStatus);
            $this->assertDatabaseHas('category_country', [
                'country_id' => $country->id,
                'category_id' => $category->id,
            ]);
        }
    }

    public function testNewsEndpoint()
    {
        $country = Country::all()->random();
        $category = $country->categories[0]->name;
        $response = $this->get(env('APP_URL') . "/api/news/{$country->code}/{$category}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "totalResults",
            "results" => [
                "*" => [
                    "title",
                    "link",
                    "keywords",
                    "creator",
                    "video_url",
                    "description",
                    "content",
                    "pubDate",
                    "image_url",
                    "source_id",
                    "source_priority",
                    "country",
                    "category",
                    "language",
                ],
            ],
            "nextPage",
        ]);
    }
    public function testPaginatedNewsEndpoint()
    {
        $country = Country::all()->random();

        $response = $this->get(env('APP_URL') . "/api/paginate/news/{$country->code}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            "status",
            "totalResults",
            "results" => [
                "*" => [
                    "title",
                    "link",
                    "keywords",
                    "creator",
                    "video_url",
                    "description",
                    "content",
                    "pubDate",
                    "image_url",
                    "source_id",
                    "source_priority",
                    "country",
                    "category",
                    "language",
                ],
            ],
            "nextPage",
        ]);

        $nextPage = $response['nextPage'];

        $response2 = $this->get(env('APP_URL') . "/api/paginate/news/{$country->code}/{$nextPage}");
        $response2->assertStatus(200);
        $response2->assertJsonStructure([
            "status",
            "totalResults",
            "results" => [
                "*" => [
                    "title",
                    "link",
                    "keywords",
                    "creator",
                    "video_url",
                    "description",
                    "content",
                    "pubDate",
                    "image_url",
                    "source_id",
                    "source_priority",
                    "country",
                    "category",
                    "language",
                ],
            ],
            "nextPage",
        ]);
    }
}
