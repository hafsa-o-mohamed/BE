<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_owners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Create pivot table for apartment-owner relationship
        Schema::create('apartment_owner_apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_owner_id')->constrained()->onDelete('cascade');
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->date('ownership_start_date')->nullable();
            $table->date('ownership_end_date')->nullable();
            $table->boolean('is_current_owner')->default(true);
            $table->unique(['apartment_id', 'is_current_owner', 'ownership_end_date'], 'unique_current_owner');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_owner_apartments');
        Schema::dropIfExists('apartment_owners');
    }
};