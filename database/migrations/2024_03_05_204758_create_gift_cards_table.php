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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->string('code', 255)->unique(); // Unique code for the gift card
            $table->decimal('amount', 10, 2); // Value of the gift card
            $table->dateTime('expiry_date')->nullable(); // Expiry date of the gift card
            $table->boolean('is_redeemed')->default(false); // Status of redemption

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
