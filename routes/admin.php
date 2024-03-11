<?php

use App\Http\Controllers\Admin\AdminAuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AuthorController;
use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\PublisherController;

Route::prefix('admin')->group(function () {

    Route::post('/login', [AdminAuthController::class, 'login']);

    Route::middleware(['admin'])->group(function () {
        Route::get('/categories', [CategoryController::class, 'index']);
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::get('/categories/{category}', [CategoryController::class, 'show']);
        Route::put('/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
    });

    Route::middleware('admin')->group(function () {
        Route::get('/authors', [AuthorController::class, 'index'])->name('admin.authors.index');
        Route::post('/authors', [AuthorController::class, 'store'])->name('admin.authors.store');
        Route::get('/authors/{author}', [AuthorController::class, 'show'])->name('admin.authors.show');
        Route::put('/authors/{author}', [AuthorController::class, 'update'])->name('admin.authors.update');
        Route::delete('/authors/{author}', [AuthorController::class, 'destroy'])->name('admin.authors.destroy');
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
        Route::post('/books', [BookController::class, 'store']);
        Route::get('/books/{book}', [BookController::class, 'show']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });
});
