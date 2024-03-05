<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {


            $table->id(); // Alias for $table->bigIncrements('id');
            $table->string('title', 255);
            $table->unsignedBigInteger('author_id');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('publisher_id');
            $table->date('publication_date')->nullable();
            $table->unsignedBigInteger('language_id');
            $table->string('isbn10', 10)->nullable();
            $table->string('isbn13', 13)->nullable();
            $table->integer('num_pages')->nullable();
            $table->string('dimensions', 50)->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->enum('format', ['PDF', 'Hard Copy', 'Audiobook'])->default('Hard Copy');
            $table->decimal('price', 10, 2);
            $table->integer('stock_quantity');
            $table->text('description')->nullable();

            // Foreign keys constraints
            $table->foreign('author_id')->references('id')->on('authors')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('publisher_id')->references('id')->on('publishers')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
