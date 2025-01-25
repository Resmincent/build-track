<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\MaterialController;
use App\Http\Controllers\MonthlyStockReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseMaterialController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RequestForMaterialController;
use App\Http\Controllers\UsersController;

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
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');





Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');



Route::middleware(['auth'])->group(function () {

    Route::resource('request-for-materials', RequestForMaterialController::class)
        ->only(['index', 'create', 'store', 'destroy']);


    Route::resource('purchase-materials', PurchaseMaterialController::class)
        ->only(['index', 'create', 'store', 'destroy']);

    // Custom routes for approval and rejection
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UsersController::class, 'create'])->name('users.create');
    Route::post('/users', [UsersController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [UsersController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UsersController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UsersController::class, 'destroy'])->name('users.destroy');

    Route::patch('/request-for-materials/{requestForMaterial}/approve', [RequestForMaterialController::class, 'approve'])
        ->name('request-for-materials.approve');

    Route::patch('/request-for-materials/{requestForMaterial}/reject', [RequestForMaterialController::class, 'reject'])
        ->name('request-for-materials.reject');

    Route::patch('/purchase-materials/{purchaseMaterial}/approve', [PurchaseMaterialController::class, 'approve'])
        ->name('purchase-materials.approve');

    Route::patch('/purchase-materials/{purchaseMaterial}/reject', [PurchaseMaterialController::class, 'reject'])
        ->name('purchase-materials.reject');

    Route::get('/admin/reports/monthly-stock', [MonthlyStockReportController::class, 'index'])
        ->name('admin.reports.monthly-stock');

    Route::resource('categories', CategoryController::class);

    Route::resource('materials', MaterialController::class);
});
