<?php

use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FareController;
use App\Http\Controllers\SandersITController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/



//Route::get('/', function () {
//    return view('dashboard');
//})->middleware(['auth', 'verified']);


Route::get('product/export', [DashboardController::class, 'export'])->name('product.export');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'role:Admin']], function () {

        // Permissions
        Route::resource('permission', PermissionController::class);

        // User Managerment
        Route::get('user', [UserController::class, 'index'])->name('user');
        Route::get('user/new', [UserController::class, 'new'])->name('user.new');
        Route::post('user/save', [UserController::class, 'save'])->name('user.save');
        Route::get('user/edit/{id}', [UserController::class, 'edit'])->name('user.edit');
        Route::post('user/update/{id}', [UserController::class, 'update'])->name('user.update');

    });


    Route::get('product/import', [DashboardController::class, 'index'])->name('product.import');
    Route::get('product', [ProductController::class, 'products'])->name('product')->middleware('can:products-show');
    Route::get('product/deleted', [ProductController::class, 'deleteProducts'])->name('product.deleted')->middleware('can:deleted-products-show');
    Route::get('product/edit/{id}', [ProductController::class, 'editProduct'])->name('product.edit');
    Route::post('product/update/{id}', [ProductController::class, 'updateProduct'])->name('product.update');
    Route::post('product/delete', [ProductController::class, 'softDeleteProduct'])->name('product.delete');
    Route::post('product/recover', [ProductController::class, 'recoverProduct'])->name('product.recover');

    Route::get('notification', [DashboardController::class, 'notifications'])->name('notification');
    Route::get('notification/get', [DashboardController::class, 'getNotifications'])->name('notification.get');

    Route::get('supplier', [SupplierController::class, 'suppliers'])->name('suppliers')->middleware('can:suppliers-show');
    Route::get('supplier/edit/{id}', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::post('supplier/update/{id}', [SupplierController::class, 'update'])->name('suppliers.update');



    Route::get('category', [CategoryController::class, 'categories'])->name('category')->middleware('can:categories-show');
    Route::get('category/edit/{id}', [CategoryController::class, 'editCategory'])->name('category.edit');
    Route::get('category/delete/{id}', [CategoryController::class, 'deleteCategory'])->name('category.delete');
    Route::post('category/new', [CategoryController::class, 'saveCategories'])->name('category.new');
    Route::post('category/update/{categoryId}', [CategoryController::class, 'updateImage'])->name('category.update');
    Route::get('category/import', [CategoryController::class, 'importCategories'])->name('category.import');
    Route::post('product/delete/bulk', [CategoryController::class, 'deleteCategories'])->name('product.delete.bulk');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('fare/convert', [FareController::class, 'convert']);
Route::get('sandersit/convert', [SandersITController::class, 'convert']);

require __DIR__ . '/auth.php';
