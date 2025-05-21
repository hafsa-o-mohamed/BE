<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // First drop the existing table
        Schema::dropIfExists('contract_services');

        // Recreate the table with correct structure
        Schema::create('contract_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts', 'id')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('services', 'id')->onDelete('cascade');
            $table->integer('quantity')->nullable();
            $table->unique(['contract_id', 'service_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contract_services');
    }
};