<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ───────────────────────────────────────────────────────────────────────
        // Disable FK checks while we build everything
        // ───────────────────────────────────────────────────────────────────────
        Schema::disableForeignKeyConstraints();

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `users` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('users', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            $t->string('name');

            // SQL: `email` varchar(255) … NOT NULL, UNIQUE KEY `users_email_unique`
            $t->string('email')->unique();

            // SQL: `email_verified_at` timestamp NULL DEFAULT NULL
            $t->timestamp('email_verified_at')->nullable();

            // SQL: `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            $t->string('password');

            // SQL: `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            $t->string('phone_number')->nullable();

            // SQL: `role` enum('owner','admin','accountant') COLLATE utf8mb4_unicode_ci NOT NULL
            $t->enum('role', ['owner', 'admin', 'accountant']);

            // SQL: `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            $t->rememberToken();

            // SQL: `created_at`, `updated_at` timestamp NULL DEFAULT NULL
            $t->timestamps();

            // SQL: `fcm_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            $t->string('fcm_token')->nullable();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `projects` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('projects', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `project_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            $t->string('project_name');

            // SQL: `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            $t->string('address')->nullable();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `buildings` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('buildings', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `project_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   ADD KEY `buildings_project_id_foreign` (`project_id`);
            // SQL:   FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
            $t->foreignId('project_id')->constrained('projects')->cascadeOnDelete();

            // SQL: `building_name` varchar(255) … NOT NULL
            $t->string('building_name');

            // SQL: `number_of_floors` int(11) NOT NULL
            $t->integer('number_of_floors');

            // SQL: `number_of_apartments` int(11) NOT NULL
            $t->integer('number_of_apartments');

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `apartments` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('apartments', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `building_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   ADD KEY `apartments_building_id_foreign` (`building_id`);
            // SQL:   FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
            $t->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();

            // SQL: `floor_number` int(11) NOT NULL
            $t->integer('floor_number');

            // SQL: `apartment_number` varchar(255) … NOT NULL
            $t->string('apartment_number');

            // SQL: `owner_id` bigint(20) UNSIGNED DEFAULT NULL
            // SQL:   FOREIGN KEY (`owner_id`) REFERENCES `apartment_owners` (`id`) ON DELETE SET NULL
            $t->foreignId('owner_id')->nullable()->constrained('apartment_owners')->nullOnDelete();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();

            // SQL: `owner_number` varchar(15) COLLATE utf8mb4_unicode_ci DEFAULT NULL
            $t->string('owner_number', 15)->nullable();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `services` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('services', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `service_name` varchar(255) … NOT NULL
            $t->string('service_name');

            // SQL: `description` varchar(255) … DEFAULT NULL
            $t->string('description')->nullable();

            // SQL: `image_url` varchar(255) … DEFAULT NULL
            $t->string('image_url')->nullable();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `apartment_owners` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('apartment_owners', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `name` varchar(255) … NOT NULL
            $t->string('name');

            // SQL: `phone` varchar(255) … DEFAULT NULL
            $t->string('phone')->nullable();

            // SQL: `email` varchar(255) … DEFAULT NULL
            $t->string('email')->nullable();

            // SQL: `user_id` bigint(20) UNSIGNED DEFAULT NULL
            // SQL:   FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `bills` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('bills', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `owner_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   KEY `bills_owner_id_foreign` (`owner_id`)
            // SQL:   FOREIGN KEY (`owner_id`) REFERENCES `apartment_owners` (`id`) ON DELETE CASCADE
            $t->foreignId('owner_id')->constrained('apartment_owners')->cascadeOnDelete();

            // SQL: `bill_type` varchar(255) … NOT NULL
            $t->string('bill_type');

            // SQL: `due_amount` decimal(10,2) NOT NULL
            $t->decimal('due_amount', 10, 2);

            // SQL: `status` varchar(255) … NOT NULL DEFAULT 'unpaid'
            $t->string('status')->default('unpaid');

            // SQL: `created_at`, `updated_at`
            $t->timestamps();

            // SQL: `due_date` date DEFAULT NULL
            $t->date('due_date')->nullable();

            // SQL: `description` varchar(255) … DEFAULT NULL
            $t->string('description')->nullable();

            // SQL: `reference_id` bigint(20) UNSIGNED DEFAULT NULL
            $t->unsignedBigInteger('reference_id')->nullable();

            // SQL: `reference_type` varchar(255) … DEFAULT NULL
            $t->string('reference_type')->nullable();

            // to mimic later index on (reference_id, reference_type)
            $t->index(['reference_id', 'reference_type']);
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `contracts` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('contracts', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `contract_type` varchar(255) … NOT NULL
            $t->string('contract_type');

            // SQL: `duration` int(11) NOT NULL
            $t->integer('duration');

            // SQL: `start_date` date NOT NULL
            $t->date('start_date');

            // SQL: `end_date` date NOT NULL
            $t->date('end_date');

            // SQL: `status` enum('Active','Expired','Canceled') … NOT NULL
            $t->enum('status', ['Active', 'Expired', 'Canceled']);

            // SQL: `building_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   KEY `contracts_building_id_foreign`
            // SQL:   FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
            $t->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();

            // SQL: `yearly_price` decimal(10,2) NOT NULL
            $t->decimal('yearly_price', 10, 2);

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `contract_services` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('contract_services', function (Blueprint $t) {
            // SQL: `id` int(11) NOT NULL AUTO_INCREMENT
            $t->integer('id', true);

            // SQL: `service_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   KEY `contract_services_service_id_foreign`
            // SQL:   FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
            $t->foreignId('service_id')->constrained('services')->cascadeOnDelete();

            // SQL: `contract_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('contract_id')->constrained('contracts')->cascadeOnDelete();

            // SQL: `frequency` enum('monthly','yearly','quarterly','daily','biannually') NOT NULL
            $t->enum('frequency', ['monthly', 'yearly', 'quarterly', 'daily', 'biannually']);

            // SQL: `created_at`, `updated_at`
            $t->timestamps();

            // SQL: PRIMARY KEY (`id`,`service_id`)
            // Laravel: single PK + unique index
            $t->unique(['contract_id', 'service_id'], 'contract_service_unique');
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `maintenance_services` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('maintenance_services', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `name` varchar(255) … NOT NULL
            $t->string('name');

            // SQL: `image_url` varchar(255) … NOT NULL
            $t->string('image_url');

            // SQL: `desc` text COLLATE utf8mb4_unicode_ci
            $t->text('desc')->nullable();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `electricity_bills` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('electricity_bills', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `default_balance` decimal(10,2) NOT NULL
            $t->decimal('default_balance', 10, 2);

            // SQL: `current_balance` decimal(10,2) NOT NULL
            $t->decimal('current_balance', 10, 2);

            // SQL: `subtracted_amount` decimal(10,2) NOT NULL DEFAULT '0.00'
            $t->decimal('subtracted_amount', 10, 2)->default(0);

            // SQL: `building_id` bigint(20) UNSIGNED NOT NULL
            // SQL:   FOREIGN KEY (`building_id`) REFERENCES `buildings` (`id`) ON DELETE CASCADE
            $t->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `water_bills` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('water_bills', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `default_balance` decimal(10,2) NOT NULL
            $t->decimal('default_balance', 10, 2);

            // SQL: `current_balance` decimal(10,2) NOT NULL
            $t->decimal('current_balance', 10, 2);

            // SQL: `subtracted_amount` decimal(10,2) NOT NULL DEFAULT '0.00'
            $t->decimal('subtracted_amount', 10, 2)->default(0);

            // SQL: `building_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('building_id')->constrained('buildings')->cascadeOnDelete();

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `provided_services` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('provided_services', function (Blueprint $t) {
            // SQL: `provided_id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('provided_id');

            // SQL: `apartment_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete();

            // SQL: `service_name` varchar(255) NOT NULL
            $t->string('service_name');

            // SQL: `date_provided` date NOT NULL
            $t->date('date_provided');

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `service_requests` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('service_requests', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `owner_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('owner_id')->constrained('users')->cascadeOnDelete();

            // SQL: `apartment_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('apartment_id')->constrained('apartments')->cascadeOnDelete();

            // SQL: `service_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('service_id')->constrained('services')->cascadeOnDelete();

            // SQL: `due_price` decimal(10,2) NOT NULL
            $t->decimal('due_price', 10, 2);

            // SQL: `request_date` date NOT NULL
            $t->date('request_date');

            // SQL: `status` enum('requested','pending','completed') NOT NULL DEFAULT 'requested'
            $t->enum('status', ['requested', 'pending', 'completed'])->default('requested');

            // SQL: `payment_status` enum('paid','unpaid') NOT NULL DEFAULT 'unpaid'
            $t->enum('payment_status', ['paid', 'unpaid'])->default('unpaid');

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `suggestions` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('suggestions', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `user_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('user_id')->constrained('users');

            // SQL: `type` enum('suggestion','complaint') NOT NULL
            $t->enum('type', ['suggestion', 'complaint']);

            // SQL: `content` text COLLATE utf8mb4_unicode_ci NOT NULL
            $t->text('content');

            // SQL: `status` enum('pending','reviewed','resolved') NOT NULL DEFAULT 'pending'
            $t->enum('status', ['pending', 'reviewed', 'resolved'])->default('pending');

            // SQL: `created_at`, `updated_at`
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // SQL: CREATE TABLE `suggestion_replies` ( … )
        // ───────────────────────────────────────────────────────────────────────
        Schema::create('suggestion_replies', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');

            // SQL: `suggestion_id` bigint(20) UNSIGNED NOT NULL
            $t->foreignId('suggestion_id')->constrained('suggestions')->cascadeOnDelete();

            // SQL: `reply` text COLLATE utf8_unicode_ci NOT NULL
            $t->text('reply');

            // SQL: `user_id` bigint(20) UNSIGNED DEFAULT NULL
            $t->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // SQL: created_at/updated_at DEFAULT CURRENT_TIMESTAMP/ON UPDATE CURRENT_TIMESTAMP
            $t->timestamps();
        });

        // ───────────────────────────────────────────────────────────────────────
        // Laravel standard tables, from dump: `personal_access_tokens`, `password_reset_tokens`, `failed_jobs`, `migrations`
        // ───────────────────────────────────────────────────────────────────────
        // Schema::create('personal_access_tokens', function (Blueprint $t) {
        //     // SQL: `id` bigint(20) UNSIGNED NOT NULL
        //     $t->bigIncrements('id');
        //     // SQL: `tokenable_type`, `tokenable_id`
        //     $t->morphs('tokenable');
        //     // SQL: `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
        //     $t->string('name');
        //     // SQL: `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL, UNIQUE KEY `personal_access_tokens_token_unique`
        //     $t->string('token', 64)->unique();
        //     // SQL: `abilities` text COLLATE utf8mb4_unicode_ci
        //     $t->text('abilities')->nullable();
        //     // SQL: `last_used_at` timestamp NULL DEFAULT NULL
        //     $t->timestamp('last_used_at')->nullable();
        //     // SQL: `expires_at` timestamp NULL DEFAULT NULL
        //     $t->timestamp('expires_at')->nullable();
        //     // SQL: `created_at`, `updated_at`
        //     $t->timestamps();
        // });

        Schema::create('password_reset_tokens', function (Blueprint $t) {
            // SQL: `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, PRIMARY KEY (`email`)
            $t->string('email')->primary();
            // SQL: `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
            $t->string('token');
            // SQL: `created_at` timestamp NULL DEFAULT NULL
            $t->timestamp('created_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $t) {
            // SQL: `id` bigint(20) UNSIGNED NOT NULL
            $t->bigIncrements('id');
            // SQL: `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL, UNIQUE KEY `failed_jobs_uuid_unique`
            $t->string('uuid')->unique();
            // SQL: `connection` text COLLATE utf8mb4_unicode_ci NOT NULL
            $t->text('connection');
            // SQL: `queue` text COLLATE utf8mb4_unicode_ci NOT NULL
            $t->text('queue');
            // SQL: `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL
            $t->longText('payload');
            // SQL: `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL
            $t->longText('exception');
            // SQL: `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            $t->timestamp('failed_at')->useCurrent();
        });

        // Schema::create('migrations', function (Blueprint $t) {
        //     // SQL: `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)
        //     $t->increments('id');
        //     // SQL: `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
        //     $t->string('migration');
        //     // SQL: `batch` int(11) NOT NULL
        //     $t->integer('batch');
        // });

        // ───────────────────────────────────────────────────────────────────────
        // Re-enable FK checks now that all tables exist
        // ───────────────────────────────────────────────────────────────────────
        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Drop in reverse order of dependencies
        Schema::dropIfExists('failed_jobs');
        // Schema::dropIfExists('migrations');
        Schema::dropIfExists('password_reset_tokens');
        // Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('suggestion_replies');
        Schema::dropIfExists('suggestions');
        Schema::dropIfExists('service_requests');
        Schema::dropIfExists('provided_services');
        Schema::dropIfExists('water_bills');
        Schema::dropIfExists('electricity_bills');
        Schema::dropIfExists('maintenance_services');
        Schema::dropIfExists('contract_services');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('bills');
        Schema::dropIfExists('apartment_owners');
        Schema::dropIfExists('services');
        Schema::dropIfExists('apartments');
        Schema::dropIfExists('buildings');
        Schema::dropIfExists('projects');
        Schema::dropIfExists('users');

        Schema::enableForeignKeyConstraints();
    }
};