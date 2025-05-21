<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['service_id']);
            
            // Add the new foreign key pointing to services table
            $table->foreign('service_id')->references('id')->on('services')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['service_id']);
            
            // Restore the original foreign key
            $table->foreign('service_id')->references('id')->on('maintenance_services')->onDelete('cascade');
        });
    }
};