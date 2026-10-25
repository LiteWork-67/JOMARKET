<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('national_id', 30)->nullable()->index();
            $table->string('avatar_url')->nullable();
            $table->text('bio')->nullable();
            $table->string('preferred_currency', 10)->default('JOD');
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['shipping', 'billing', 'both'])->default('shipping');
            $table->string('contact_name');
            $table->string('phone', 30);
            $table->string('governorate', 50)->index(); // Amman, Zarqa, Irbid, Balqa, Karak, Ma'an, Zarqa, Mafraq, Tafilah, Madaba, Jerash, Ajloun, Aqaba
            $table->string('city', 100)->index();
            $table->string('district', 100);
            $table->string('street_address');
            $table->string('building_number', 50)->nullable();
            $table->string('floor', 20)->nullable();
            $table->string('apartment', 20)->nullable();
            $table->string('landmark')->nullable();
            $table->text('delivery_instructions')->nullable();
            $table->boolean('is_default')->default(false)->index();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
        Schema::dropIfExists('user_profiles');
    }
};
