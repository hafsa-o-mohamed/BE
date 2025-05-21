<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('apartment_owners')->onDelete('cascade');
            $table->string('bill_type');  // e.g., 'service', 'maintenance', 'rent'
            $table->decimal('due_amount', 10, 2);
            $table->string('status')->default('unpaid');  // e.g., 'paid', 'unpaid', 'overdue'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bills');
    }
};