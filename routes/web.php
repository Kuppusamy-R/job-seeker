<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;

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
    return view('welcome');
});

Auth::routes();

// Route::name('admin.')->controller(AdminAuthController::class)->middleware("auth:webadmin")->group(function () {
//     Route::get('/admin', 'index')->name('home');
//     Route::get('/admin/login', 'login')->name('login');
//     Route::post('/admin/handleLogin', 'login')->name('login');
// });

Route::middleware(["auth:webadmin"])->prefix('/admin')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'login'])->name('admin.login')->withoutMiddleware("auth:webadmin");
    Route::post('/login', [AdminAuthController::class, 'handleLogin'])->name('admin.handleLogin')->withoutMiddleware("auth:webadmin");

    Route::get('/', [AdminAuthController::class, 'index'])->name('admin.home');
    Route::post('/', [AdminAuthController::class, 'filter'])->name('admin.filter');
    Route::delete('/delete/{id}', [AdminAuthController::class, 'delete'])->name('admin.delete');
});
// Route::get('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login');
// Route::post('/admin/login', [AdminAuthController::class, 'handleLogin'])->name('admin.handleLogin');

// Route::get('/admin', [AdminAuthController::class, 'index'])->middleware("auth:webadmin")->name('admin.home');
// Route::post('/admin', [AdminAuthController::class, 'filter'])->middleware("auth:webadmin")->name('admin.filter');
// Route::delete('/admin/delete/{id}', [AdminAuthController::class, 'delete'])->middleware("auth:webadmin")->name('admin.delete');


Route::middleware(["auth:web"])->prefix('/home')->group(function () {
    Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::get('/edit', [App\Http\Controllers\HomeController::class, 'edit'])->name('edit');
    Route::get('/editpassword', [App\Http\Controllers\HomeController::class, 'editPassword'])->name('editpassword');
    Route::post('/updatepassword', [App\Http\Controllers\HomeController::class, 'updatePassword'])->name('updatepassword');
    Route::post('/update', [App\Http\Controllers\HomeController::class, 'updateUser'])->name('update');
    Route::post('/update', [App\Http\Controllers\HomeController::class, 'updateUser'])->name('update');
});

