<?php
// Controllers
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\TypologyController;
use App\Http\Controllers\Api\OrderController;

// Models
use App\Models\Restaurant;
use App\Models\Typology;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
// });

Route::name('api.')->group(function(){
    Route::resource('restaurant', RestaurantController::class)->only([
        'index',
        'show'
    ]);

    Route::resource('typology', TypologyController::class)->only([
        'index'
    ]);

    Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
});