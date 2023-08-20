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
                "result" => $mappedCountries,
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

            $mappedCountry = CacheHandler::rememberData($cacheKey, 20, function () use ($code) {
                $country = Country::where('code', $code)->with('languages', 'categories')->first();
                return Country::transformData($country);
            });

            return response()->json([
                "result" => $mappedCountry,
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

        $cacheKey = "news_{$code}_{$category}_{$lang}";
        $minutes = 20;

        return CacheHandler::rememberData($cacheKey, $minutes, function () use ($code, $category, $lang) {
            $newsdataApiObj = new NewsDataApi(env('NEWS_API_KEY'));

            $data = [
                "country" => $code,
                "category" => $category,
                "language" => $lang,
            ];
            return $newsdataApiObj->get_latest_news($data);
        });
    }

    public function paginatedNews($code, int $page = null)
    {
        $languages = Country::with('languages')->where('code', $code)->first()->languages->pluck('language')->toArray();

        $cacheKey = "news_{$code}_page_{$page}";
        $minutes = 20;

        return CacheHandler::rememberData($cacheKey, $minutes, function () use ($code, $languages, $page) {
            $newsdataApiObj = new NewsDataApi(env('NEWS_API_KEY'));

            $data = [
                "country" => $code,
                "language" => $languages,
                "page" => $page
            ];
            return $newsdataApiObj->get_latest_news($data);
        });
    }
}
