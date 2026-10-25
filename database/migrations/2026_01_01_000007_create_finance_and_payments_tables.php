<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 3);
            $table->string('currency', 10)->default('JOD');
            $table->enum('provider', ['cod', 'card', 'tap', 'hyperpay', 'cliq'])->default('cod')->index();
            $table->string('transaction_id')->nullable()->index();
            $table->enum('status', ['initiated', 'authorized', 'captured', 'failed', 'refunded'])->default('initiated')->index();
            $table->json('payload')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained()->cascadeOnDelete(); // null = platform entry
            $table->enum('type', [
                'sale',
                'commission_deduction',
                'payout',
                'refund_deduction',
                'adjustment',
                'delivery_fee',
            ])->index();
            $table->enum('entry_type', ['credit', 'debit'])->index();
            $table->decimal('amount', 12, 3);
            $table->string('currency', 10)->default('JOD');
            $table->decimal('balance_after', 12, 3);
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['seller_id', 'created_at']);
        });

        Schema::create('cash_on_delivery_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_order_id')->constrained()->cascadeOnDelete();
            $table->string('courier_name');
            $table->decimal('amount_collected', 12, 3);
            $table->enum('status', [
                'pending_collection',
                'collected_by_courier',
                'remitted_to_platform',
                'settled_to_seller',
            ])->default('pending_collection')->index();
            $table->timestamp('collected_at')->nullable();
            $table->timestamp('remitted_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_on_delivery_reconciliations');
        Schema::dropIfExists('financial_ledger_entries');
        Schema::dropIfExists('payment_transactions');
    }
};
