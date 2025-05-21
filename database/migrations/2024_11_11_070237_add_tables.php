<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Users Table   - already exists
        // 2. Projects Table
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name');
            $table->string('address')->nullable();
            $table->timestamps();
        });

        // 3. Buildings Table
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->string('building_name');
            $table->integer('number_of_floors');
            $table->integer('number_of_apartments');
            $table->timestamps();
        });

        // 4. Apartments Table
Schema::create('apartments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('building_id')->constrained('buildings', 'id')->onDelete('cascade');
    $table->integer('floor_number');
    $table->string('apartment_number');
    $table->foreignId('owner_id')->nullable()->constrained('users')->onDelete('cascade'); // Changed this line
    $table->timestamps();
});

        // 5. Maintenance Services Table
        Schema::create('maintenance_services', function (Blueprint $table) {
            $table->id();
            $table->string('service_name');
            $table->decimal('price', 10, 2);
            $table->integer('offer_quota')->nullable();
            $table->timestamps();
        });

     // 6. Contracts Table
Schema::create('contracts', function (Blueprint $table) {
    $table->id();
    $table->enum('contract_type', ['Basic', 'Premium']);
    $table->foreignId('building_id')->constrained('buildings')->onDelete('cascade'); // Add building_id
    $table->integer('duration');
    $table->date('start_date');
    $table->date('end_date');
    $table->enum('status', ['Active', 'Expired', 'Canceled']);
    $table->timestamps();
});

        // 7. Contract Services Table
        Schema::create('contract_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts', 'id')->onDelete('cascade');
            $table->foreignId('service_id')->constrained('maintenance_services', 'id')->onDelete('cascade');
            $table->unique(['contract_id', 'service_id']); // Add unique constraint instead
            $table->timestamps();
        });

        // 8. Provided Services Table
        Schema::create('provided_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained('apartments', 'id')->onDelete('cascade');
            $table->string('service_name');
            $table->date('date_provided');
            $table->timestamps();
        });

       // 9. Service Requests Table
Schema::create('service_requests', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users')->onDelete('cascade'); // Changed this line
    $table->foreignId('apartment_id')->constrained('apartments', 'id')->onDelete('cascade');
    $table->foreignId('service_id')->constrained('maintenance_services', 'id')->onDelete('cascade');
    $table->decimal('due_price', 10, 2);
    $table->date('request_date');
    $table->enum('status', ['Pending', 'Completed']);       
    $table->timestamps();
});
    }

    public function down(): void
    {
        // Drop tables in reverse order to avoid foreign key constraints
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('provided_services');
        Schema::dropIfExists('contract_services');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('maintenance_services');
        Schema::dropIfExists('apartments');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('projects');
    }
};