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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // UserID
            $table->dateTime('order_date'); // OrderDate
            $table->string('shipping_address', 255); // ShippingAddress
            $table->string('city', 100); // City
            $table->string('state_province', 100); // StateProvince
            $table->string('country', 100); // Country
            $table->decimal('total_amount', 10, 2); // TotalAmount
            $table->enum('status', ['Pending', 'Processing', 'Shipped', 'Delivered', 'Cancelled']); // Status
            $table->string('shipment_tracking_number', 255)->nullable(); // ShipmentTrackingNumber

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
