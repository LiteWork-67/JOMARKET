<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('guest_token', 80)->nullable()->index();
            $table->string('currency', 10)->default('JOD');
            $table->enum('status', ['active', 'merged', 'abandoned', 'converted'])->default('active')->index();
            $table->timestamps();
        });

        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cart_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_offer_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 3);
            $table->timestamps();

            $table->unique(['cart_id', 'seller_offer_id']);
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained()->cascadeOnDelete(); // null = platform-wide
            $table->string('code')->unique();
            $table->enum('type', ['fixed', 'percentage'])->default('fixed');
            $table->decimal('value', 12, 3);
            $table->decimal('min_order_amount', 12, 3)->default(0.000);
            $table->decimal('max_discount_amount', 12, 3)->nullable();
            $table->unsignedInteger('usage_limit')->default(0); // 0 = unlimited
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('coupon_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('parent_order_id')->nullable()->index();
            $table->decimal('discount_amount', 12, 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_redemptions');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('wishlists');
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('carts');
    }
};
