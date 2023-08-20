<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Language;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { {
            $data = [
                ['name' => 'Belgium', 'code' => 'be', 'language' => ['nl']],
                ['name' => 'Canada', 'code' => 'ca', 'language' => ['en', 'fr']],
                ['name' => 'France', 'code' => 'fr', 'language' => ['fr']],
                ['name' => 'Germany', 'code' => 'de', 'language' => ['de']],
                ['name' => 'United Kingdom', 'code' => 'gb', 'language' => ['en']]
            ];

            foreach ($data as $item) {
                $country = Country::create([
                    'name' => $item['name'],
                    'code' => $item['code']
                ]);

                $languages = [];
                foreach ($item['language'] as $lang) {
                    $language = Language::firstOrCreate(['language' => $lang]);
                    $languages[] = $language->id;
                }

                $country->languages()->sync($languages);
            }
        }
    }
}
