<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('electricity_bills', function (Blueprint $table) {
            $table->id();
            $table->decimal('default_balance', 10, 2);
            $table->decimal('current_balance', 10, 2);
            $table->decimal('subtracted_amount', 10, 2)->default(0);
            $table->foreignId('building_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('electricity_bills');
    }
};