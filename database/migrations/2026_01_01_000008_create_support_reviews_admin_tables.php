<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('comment');
            $table->boolean('is_verified_purchase')->default(false)->index();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved')->index();
            $table->timestamps();

            $table->index(['product_id', 'status']);
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_order_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium')->index();
            $table->enum('status', ['open', 'in_progress', 'waiting_customer', 'resolved', 'closed'])->default('open')->index();
            $table->timestamps();
        });

        Schema::create('support_ticket_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('support_ticket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->text('message');
            $table->json('attachments')->nullable();
            $table->boolean('is_internal_note')->default(false);
            $table->timestamps();
        });

        Schema::create('cms_pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title_ar');
            $table->string('title_en');
            $table->longText('content_ar');
            $table->longText('content_en');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index(); // 'hero_banner', 'categories_grid', 'deals_carousel', 'featured_products', 'popular_sellers'
            $table->string('title_ar');
            $table->string('title_en');
            $table->json('config')->nullable();
            $table->integer('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general')->index();
            $table->boolean('is_public')->default(false)->index();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action')->index();
            $table->string('auditable_type')->nullable()->index();
            $table->unsignedBigInteger('auditable_id')->nullable()->index();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('cms_pages');
        Schema::dropIfExists('support_ticket_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('product_reviews');
    }
};
