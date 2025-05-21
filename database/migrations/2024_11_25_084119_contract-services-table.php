<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('contract_services', function (Blueprint $table) {
            $table->dropColumn('quantity');
            $table->enum('frequency', ['monthly', 'yearly', 'quarterly', 'daily', 'biannually'])->after('service_id');
        });
    }
    

    public function down()
    {
        Schema::table('contract_services', function (Blueprint $table) {
            $table->integer('quantity')->after('service_id');
            $table->dropColumn('frequency');
        });
    }
};