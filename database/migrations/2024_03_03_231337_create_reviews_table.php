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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade'); // Assuming `books` is the table name.
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assuming `users` is the table name.
            $table->text('review_text');
            $table->foreignId('rating_id')->nullable()->constrained()->onDelete('set null'); // Assuming `ratings` is the table name and review might not have a rating.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
