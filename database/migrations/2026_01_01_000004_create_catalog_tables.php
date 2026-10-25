<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->string('image_url')->nullable();
            $table->string('icon')->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->integer('sort_order')->default(0)->index();
            $table->timestamps();
        });

        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('slug')->unique();
            $table->longText('description_ar')->nullable();
            $table->longText('description_en')->nullable();
            $table->text('short_description_ar')->nullable();
            $table->text('short_description_en')->nullable();
            $table->string('main_image_url')->nullable();
            $table->string('barcode')->nullable()->index();
            $table->string('sku_prefix')->nullable()->index();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'archived'])->default('pending_approval')->index();
            $table->text('rejection_reason')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_url');
            $table->boolean('is_primary')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attribute_id')->constrained('product_attributes')->cascadeOnDelete();
            $table->string('value_ar');
            $table->string('value_en');
            $table->string('hex_color')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->index();
            $table->string('title_ar')->nullable();
            $table->string('title_en')->nullable();
            $table->decimal('weight_kg', 8, 3)->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('product_variant_attribute_value', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->cascadeOnDelete();
            $table->foreignId('attribute_value_id')->constrained('product_attribute_values')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['product_variant_id', 'attribute_value_id'], 'var_attr_val_unique');
        });

        Schema::create('seller_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('seller_sku')->nullable()->index();
            $table->enum('condition', ['new', 'refurbished', 'open_box'])->default('new');
            $table->decimal('price', 12, 3); // JOD with 3 decimals
            $table->decimal('sale_price', 12, 3)->nullable();
            $table->decimal('cost_price', 12, 3)->nullable();
            $table->string('currency', 10)->default('JOD');
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0);
            $table->integer('min_order_quantity')->default(1);
            $table->integer('max_order_quantity')->default(10);
            $table->integer('handling_days')->default(1);
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_featured')->default(false)->index();
            $table->timestamps();

            $table->index(['seller_id', 'product_id']);
        });

        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_offer_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['initial', 'restock', 'reservation', 'release', 'sale', 'refund', 'adjustment'])->index();
            $table->integer('quantity');
            $table->integer('balance_after');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('inventory_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_offer_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('cart_id')->nullable()->index();
            $table->string('checkout_token')->index();
            $table->integer('quantity');
            $table->timestamp('expires_at')->index();
            $table->enum('status', ['active', 'converted', 'expired', 'released'])->default('active')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_reservations');
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('seller_offers');
        Schema::dropIfExists('product_variant_attribute_value');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('product_attribute_values');
        Schema::dropIfExists('product_attributes');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('products');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('categories');
    }
};
