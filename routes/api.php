<?php

use App\Http\Controllers\Website\AuthController;
use App\Http\Controllers\Website\AuthorController;
use App\Http\Controllers\Website\BlogPostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Website\BookController;
use App\Http\Controllers\Website\CartController;
use App\Http\Controllers\Website\CategoryController;
use App\Http\Controllers\Website\FormatController;
use App\Http\Controllers\Website\OrderController;
use App\Http\Controllers\Website\PublisherController;
use App\Http\Controllers\Website\RatingController;
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



// Login
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

Route::get('/auth/{provider}', [SocialAuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback']);

// Logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

Route::prefix('books')->group(function () {
    Route::get('/', [BookController::class, 'index']);
    Route::get('/{id}/show', [BookController::class, 'show']);
    Route::get('/category/{categoryId}', [BookController::class, 'byCategory']);
    Route::get('/search', [BookController::class, 'search']);
    Route::get('/latest', [BookController::class, 'latest']);
    Route::get('/best-sellers', [BookController::class, 'bestSellers']);
});


Route::get('/price-range', [BookController::class, 'getPriceRange']);
Route::get('/year-range', [BookController::class, 'getYearRange']);
Route::get('/formats', [BookController::class, 'getFormats']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/publishers', [PublisherController::class, 'index']);
Route::get('/authors', [AuthorController::class, 'index']);
// Route::get('/formats', [FormatController::class, 'index']);


Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::post('/add', [CartController::class, 'addItem'])->name('cart.add');
    Route::put('/update', [CartController::class, 'updateItem'])->name('cart.update');
    Route::delete('/remove', [CartController::class, 'removeItem'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/wishlist', [WishListController::class, 'index']);
    Route::post('/wishlist/add', [WishListController::class, 'addItem']);
    Route::post('/wishlist/toggle', [WishListController::class, 'toggleItem']);
    Route::delete('/wishlist/remove', [WishListController::class, 'removeItem']);
    Route::post('/wishlist/clear', [WishListController::class, 'clear']);
    Route::get('/wishlist/check', [WishListController::class, 'checkItem']);
});


Route::prefix('blog')->group(function () {
    Route::get('/', [BlogPostController::class, 'index']);
    Route::get('/recent', [BlogPostController::class, 'recent']);
    Route::get('/tag/{tagSlug}', [BlogPostController::class, 'byTag']);
    Route::get('/{slug}', [BlogPostController::class, 'show']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{orderId}', [OrderController::class, 'show']);
    Route::put('/orders/{order}', [OrderController::class, 'update']);
    Route::delete('/orders/{order}', [OrderController::class, 'destroy']);
    Route::get('/orders/{orderId}/trace', [OrderController::class, 'trace']);
    Route::post('/orders/{orderId}/refund', [OrderController::class, 'refund']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books/{bookId}/rate', [RatingController::class, 'rateBook']);
    Route::get('/books/{bookId}/rating', [RatingController::class, 'getUserRating']);
    Route::put('/books/{bookId}/rating', [RatingController::class, 'updateRating']);
    Route::delete('/books/{bookId}/rating', [RatingController::class, 'deleteRating']);
});
