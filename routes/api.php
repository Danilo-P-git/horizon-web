<?php

use App\Http\Controllers\CountryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('countries', [CountryController::class, 'index']);
Route::get('country/{code}', [CountryController::class, 'show']);
Route::post('country/{code}/{category}', [CountryController::class, 'syncCategories']);
Route::get('news/{code}/{category}', [CountryController::class, 'news']);
Route::get('paginate/news/{code}/{page?}', [CountryController::class, 'paginatedNews']);
