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
        Schema::create('discount_coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code'); // The discoumt coupon code
            $table->string('name')->nullable(); // The human readable discount coupon code name.
            $table->text('description')->nullable(); // The description of the coupon - not necessary.
            $table->integer('max_uses')->nullable(); // The max uses this discount coupon has.
            $table->integer('max_uses_user')->nullable(); // How many times a user can use this coupon code.
            $table->enum('type',['percent', 'fixed'])->default('fixed'); // Whether or not the coupon is a percentage or a fixed price.
            $table->double('discount_amount',10,2); // The amount to discount based on type.
            $table->double('min_amount',10,2)->nullable(); // Compare the min_amount with subtotal if subtotal equal or greaterthan min_amount.
            $table->integer('status')->default(1);    // Status active/deactive.
            $table->timestamp('starts_at')->nullable();    // When the coupon begins.
            $table->timestamp('expires_at')->nullable(); // When the coupon end.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_coupons');
    }
};
