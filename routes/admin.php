<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\LanguageController;
use App\Http\Controllers\Admin\PublisherController;
use App\Http\Controllers\Admin\OrderController;

Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['admin'])->group(function () {

        Route::post('/categories/import', [CategoryController::class, 'import']);
        Route::get('/categories/export', [CategoryController::class, 'export']);
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
        Route::post('/categories/bulk-delete', [CategoryController::class, 'bulkDelete']);
    });

    Route::middleware(['admin'])->group(function () {
        Route::get('/posts', [BlogPostController::class, 'index']);
        Route::post('/posts', [BlogPostController::class, 'store']);
        Route::get('/posts/{post}', [BlogPostController::class, 'show']);
        Route::put('/posts/{post}', [BlogPostController::class, 'update']);
        Route::delete('/posts/{post}', [BlogPostController::class, 'destroy']);
        Route::post('/posts/bulk-delete', [BlogPostController::class, 'bulkDelete']);
    });

    Route::middleware('admin')->group(function () {
        Route::post('/authors/import', [AuthorController::class, 'import']);
        Route::get('/authors/export', [AuthorController::class, 'export']);
        Route::get('/authors', [AuthorController::class, 'index'])->name('admin.authors.index');
        Route::post('/authors', [AuthorController::class, 'store'])->name('admin.authors.store');
        Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('admin.authors.show');
        Route::put('/authors/{author}', [AuthorController::class, 'update'])->name('admin.authors.update');
        Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])->name('admin.authors.destroy');

        Route::post('/authors/update-from-csv', [AuthorController::class, 'updateFromCSV']);
        Route::get('/authors/update-progress/{jobId}', [AuthorController::class, 'updateProgress']);
    });

    Route::middleware('admin')->group(function () {
        Route::get('/publishers', [PublisherController::class, 'index']);
        Route::post('/publishers', [PublisherController::class, 'store']);
        Route::get('/publishers/{publisher}', [PublisherController::class, 'show']);
        Route::put('/publishers/{publisher}', [PublisherController::class, 'update']);
        Route::delete('/publishers/{publisher}', [PublisherController::class, 'destroy']);
    });


    Route::middleware('admin')->group(function () {
        Route::get('/books', [BookController::class, 'index']);
        Route::post('/books/import-update', [BookController::class, 'importUpdate']);
        Route::get('/books/export', [BookController::class, 'export']);

        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{book}', [BookController::class, 'show']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);

        Route::post('/books/import', [BookController::class, 'import']);
        Route::post('/books/import-images', [BookController::class, 'importImages']);
        Route::get('/template/book', [BookController::class, 'downloadTemplate']);
    });

    Route::middleware('admin')->group(function () {
        Route::get('/languages', [LanguageController::class, 'index']);
        Route::get('/languages/{language}', [LanguageController::class, 'show']);
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/verify-token', function () {
            return response()->json(['message' => 'Token is valid']);
        });
    });

    Route::middleware('admin')->prefix('orders')->group(function () {
        Route::get('/', [OrderController::class, 'index']);
        Route::post('/', [OrderController::class, 'store']);
        Route::get('/{id}', [OrderController::class, 'show']);
        Route::put('/{id}/status', [OrderController::class, 'updateStatus']);
        Route::get('/revenue', [OrderController::class, 'revenue']);
        Route::get('/top-customers', [OrderController::class, 'topCustomers']);
    });
});
