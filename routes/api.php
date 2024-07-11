<?php

use App\Http\Controllers\Website\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\BookController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\WishListController;
use App\Http\Controllers\Website\SocialAuthController;

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


// Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);


Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}', [BookController::class, 'show']);
    Route::get('/category/{categoryId}', [BookController::class, 'byCategory']);
    Route::get('/search', [BookController::class, 'search']);
    Route::get('/latest', [BookController::class, 'latest']);
    Route::get('/best-sellers', [BookController::class, 'bestSellers']);
});


Route::prefix('cart')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::put('/update', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [WishListController::class, 'index']);
    Route::post('/wishlist/add', [WishListController::class, 'addItem']);
    Route::delete('/wishlist/remove', [WishListController::class, 'removeItem']);
    Route::post('/wishlist/clear', [WishListController::class, 'clear']);
    Route::get('/wishlist/check', [WishListController::class, 'checkItem']);
});
