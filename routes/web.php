<?php

use App\Http\Controllers\Crud;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/crud', [Crud::class, 'list'])
    ->name('crud');

Route::get('/crud/{grid}', [Crud::class, 'grid'])
    ->name('crud.grid');

Route::get('/crud/{grid}/fetch', [Crud::class, 'gridFetch'])
    ->name('crud.grid.fetch');

Route::delete('/crud/{grid}/delete/{id}', [Crud::class, 'deleteRecord'])
    ->name('crud.grid.delete');

Route::get('/crud/{grid}/form/{id?}', [Crud::class, 'form'])
    ->name('crud.grid.form');

Route::post('/crud/{grid}/form/{id?}', [Crud::class, 'save'])
    ->name('crud.grid.form.post');

Route::pattern('grid', '\w+');
Route::pattern('id', '\d+');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});
