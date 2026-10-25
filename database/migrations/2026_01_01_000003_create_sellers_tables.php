<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('store_name_ar');
            $table->string('store_name_en');
            $table->string('slug')->unique();
            $table->string('logo_url')->nullable();
            $table->string('banner_url')->nullable();
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            $table->decimal('commission_rate', 5, 2)->default(10.00); // 10.00%
            $table->enum('status', ['pending', 'approved', 'rejected', 'suspended'])->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->string('commercial_registration_no')->nullable()->index();
            $table->string('tax_number')->nullable()->index();
            $table->string('bank_name')->nullable();
            $table->string('iban', 34)->nullable();
            $table->string('account_holder_name')->nullable();
            $table->string('contact_email');
            $table->string('contact_phone', 30);
            $table->string('governorate', 50)->index();
            $table->string('city', 100);
            $table->text('address_details')->nullable();
            $table->decimal('rating', 3, 2)->default(5.00);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('seller_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('role')->default('manager');
            $table->json('permissions')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['seller_id', 'user_id']);
        });

        Schema::create('seller_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->enum('document_type', ['commercial_register', 'tax_certificate', 'national_id', 'bank_statement', 'license'])->index();
            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedInteger('file_size')->default(0);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });

        Schema::create('seller_payout_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 12, 3);
            $table->string('currency', 10)->default('JOD');
            $table->enum('status', ['requested', 'processing', 'completed', 'rejected'])->default('requested')->index();
            $table->foreignId('requested_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('rejection_reason')->nullable();
            $table->string('transaction_reference')->nullable()->index();
            $table->text('notes')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seller_payout_requests');
        Schema::dropIfExists('seller_documents');
        Schema::dropIfExists('seller_staff');
        Schema::dropIfExists('sellers');
    }
};
