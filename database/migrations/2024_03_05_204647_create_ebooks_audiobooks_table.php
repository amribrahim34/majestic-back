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
        Schema::create('ebooks_audiobooks', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('book_id'); // Links to the Books table
            $table->enum('format', ['Ebook', 'Audiobook']); // Type of digital format
            $table->string('file_url'); // URL to access/download the digital content
            $table->string('duration')->nullable(); // Relevant for audiobooks, length of audio

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ebooks_audiobooks');
    }
};
