<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['pending', 'processing', 'completed', 'cancelled'])->default('pending')->index();
            $table->decimal('subtotal_amount', 12, 3);
            $table->decimal('shipping_amount', 12, 3)->default(0.000);
            $table->decimal('discount_amount', 12, 3)->default(0.000);
            $table->decimal('tax_amount', 12, 3)->default(0.000);
            $table->decimal('total_amount', 12, 3);
            $table->string('currency', 10)->default('JOD');
            $table->enum('payment_status', ['pending', 'authorized', 'paid', 'failed', 'refunded'])->default('pending')->index();
            $table->enum('payment_method', ['cash_on_delivery', 'card', 'wallet', 'bank_transfer'])->default('cash_on_delivery')->index();
            $table->string('payment_reference')->nullable()->index();
            $table->json('shipping_address_snapshot');
            $table->json('billing_address_snapshot')->nullable();
            $table->text('customer_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('seller_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->enum('status', [
                'pending_confirmation',
                'confirmed',
                'processing',
                'ready_to_ship',
                'shipped',
                'delivered',
                'completed',
                'cancelled',
                'return_requested',
                'returned',
                'refunded',
            ])->default('pending_confirmation')->index();
            $table->decimal('subtotal', 12, 3);
            $table->decimal('shipping_fee', 12, 3)->default(0.000);
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->decimal('commission_amount', 12, 3)->default(0.000);
            $table->decimal('seller_net_amount', 12, 3)->default(0.000);
            $table->decimal('discount_amount', 12, 3)->default(0.000);
            $table->decimal('tax_amount', 12, 3)->default(0.000);
            $table->decimal('total_amount', 12, 3);
            $table->string('shipping_method')->default('standard');
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable()->index();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('seller_notes')->nullable();
            $table->timestamps();

            $table->index(['seller_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_offer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_title_ar');
            $table->string('product_title_en');
            $table->string('variant_title_ar')->nullable();
            $table->string('variant_title_en')->nullable();
            $table->string('sku');
            $table->decimal('unit_price', 12, 3);
            $table->integer('quantity');
            $table->decimal('subtotal', 12, 3);
            $table->decimal('discount_amount', 12, 3)->default(0.000);
            $table->decimal('tax_amount', 12, 3)->default(0.000);
            $table->decimal('total_amount', 12, 3);
            $table->decimal('commission_rate', 5, 2)->default(10.00);
            $table->decimal('commission_amount', 12, 3)->default(0.000);
            $table->decimal('seller_net_amount', 12, 3)->default(0.000);
            $table->timestamps();
        });

        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->cascadeOnDelete();
            $table->string('status_from')->nullable();
            $table->string('status_to');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('cancellations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->text('reason');
            $table->enum('status', ['requested', 'approved', 'rejected'])->default('requested')->index();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'item_received', 'refunded'])->default('pending')->index();
            $table->enum('resolution_type', ['refund', 'exchange'])->default('refund');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('return_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->text('condition_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_order_id')->nullable()->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 3);
            $table->string('currency', 10)->default('JOD');
            $table->enum('status', ['pending', 'completed', 'failed'])->default('pending')->index();
            $table->string('refund_method')->default('original_payment');
            $table->string('transaction_reference')->nullable()->index();
            $table->text('reason');
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('refunds');
        Schema::dropIfExists('return_items');
        Schema::dropIfExists('return_requests');
        Schema::dropIfExists('cancellations');
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('seller_orders');
        Schema::dropIfExists('parent_orders');
    }
};
