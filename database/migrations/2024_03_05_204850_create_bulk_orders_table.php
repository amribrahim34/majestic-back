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
        Schema::create('bulk_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id'); // Links to the Orders table
            $table->string('institution_name', 255); // Name of the institution placing the order
            $table->integer('quantity'); // Quantity of books in the bulk order
            $table->decimal('total_amount', 10, 2); // Total amount for the bulk order

            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_orders');
    }
};
