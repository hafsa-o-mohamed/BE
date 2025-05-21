<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateServiceRequestsForeignKey extends Migration
{
    public function up()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // First drop the existing foreign key
            $table->dropForeign(['service_id']);
            
            // Then add the new foreign key pointing to maintenance_services
            $table->foreign('service_id')
                  ->references('id')
                  ->on('maintenance_services')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('service_requests', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['service_id']);
            
            // Restore the original foreign key
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('cascade');
        });
    }
}