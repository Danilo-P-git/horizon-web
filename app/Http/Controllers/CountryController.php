<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use App\Models\Category;
use App\Models\Country;
use App\Utility\CacheHandler;
use App\Utility\ErrorHandler;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use NewsdataIO\NewsdataApi;


class CountryController extends Controller
{



    public function index()
    {

        try {

            $cacheKey = 'countries_index';

            $mappedCountries =  CacheHandler::rememberData($cacheKey, 20, function () {

                $countries = Country::with('languages', 'categories')->get();

                return $countries->map(function ($country) {
                    return Country::transformData($country);
                });
            });

            return response()->json([
                "data" => $mappedCountries,
                "status" => 200,
                "message" => "data recovered succesfuly"
            ], 200);
        } catch (Exception $e) {
            return ErrorHandler::handleException($e);
        }
    }

    public function show($code)
    {
        try {
            $cacheKey = 'country_' . $code;

            $mappedCountry = CacheHandler::rememberData($cacheKey, 60, function () use ($code) {
                $country = Country::where('code', $code)->with('languages', 'categories')->first();
                return Country::transformData($country);
            });

            return response()->json([
                "data" => $mappedCountry,
                "status" => 200,
                "message" => "data recovered succesfuly"
            ], 200);
        } catch (Exception $e) {
            return ErrorHandler::handleException($e);
        }
    }

    public function syncCategories($code, $category)
    {
        try {
            $country = Country::where('code', $code)->firstOrFail();
            $category = Category::where('name', $category)->firstOrFail();

            if ($country->categories->contains($category->id)) {

                $country->categories()->detach($category->id);
                $message = 'Category removed from the country.';
                $status = 200;
            } else {

                $country->categories()->attach($category->id);
                $message = 'Category added to the country.';
                $status = 201;
            }

            return response()->json([
                'status' => $status,
                'message' => $message,
                'data' => [
                    'country' => $country->name,
                    'category' => $category->name,
                ],
            ], $status);
        } catch (Exception $e) {
            return ErrorHandler::handleException($e);
        }
    }

    public function news($code, $category, NewsRequest $request)
    {
        $lang = $request->language;

        $newsdataApiObj = new NewsDataApi(env('NEWS_API_KEY'));

        $data = [
            "country" => $code,
            "category" => $category,
            "language" => $lang,
        ];
        $response = $newsdataApiObj->get_latest_news($data);

        return $response;
    }
}
