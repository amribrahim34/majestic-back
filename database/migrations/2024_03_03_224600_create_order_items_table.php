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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // OrderID
            $table->unsignedBigInteger('book_id'); // BookID
            $table->integer('quantity'); // Quantity
            $table->decimal('price', 10, 2); // Price
            $table->decimal('discount', 10, 2)->nullable(); // Discount

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
