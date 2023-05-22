<?php

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum', 'ability:read'])->group(function () {
    Route::prefix('product')->group(function () {
        Route::get('/{id?}', function (int $id = null) {
            return $id
                ? new \App\Http\Resources\Product(\App\Models\Product::findOrFail($id))
                : new \App\Http\Resources\ProductCollection(\App\Models\Product::paginate());
        });
    });
    Route::prefix('pack')->group(function () {
        Route::get('/{id?}', function (int $id = null) {
            return $id
                ? new \App\Http\Resources\Pack(\App\Models\Pack::findOrFail($id))
                : new \App\Http\Resources\PackCollection(\App\Models\Pack::paginate());
        });

        Route::get('/{id}/product', function (int $id) {
            return new \App\Http\Resources\ProductCollection(\App\Models\Pack::findOrFail($id)->products()->paginate());
        });
    });
});
