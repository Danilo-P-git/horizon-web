<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = Country::all();
        $categories = Category::all();

        $minimumCategoriesPerCountry = 2;

        foreach ($countries as $country) {
            $categoriesToAdd = $minimumCategoriesPerCountry - $country->categories()->count();

            // Rimuovi le categorie già associate a questa country
            $availableCategories = $categories->diff($country->categories);

            for ($i = 0; $i < $categoriesToAdd; $i++) {
                $randomCategory = $availableCategories->random();
                $country->categories()->attach($randomCategory);
                $availableCategories = $availableCategories->diff([$randomCategory]);
            }
        }
    }
}
