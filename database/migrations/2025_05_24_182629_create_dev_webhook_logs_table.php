<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * ⚠️ DEV ONLY TABLE - DO NOT USE IN PRODUCTION ⚠️
     * This table is specifically for development webhook testing
     */
    public function up(): void
    {
        Schema::create('dev_webhook_logs', function (Blueprint $table) {
            $table->id();
            
            // Webhook identification
            $table->string('webhook_id')->nullable()->comment('Unique identifier for this webhook call');
            $table->string('object_type')->comment('Type of object: charge, authorize, invoice');
            $table->string('object_id')->comment('ID of the charge/authorize/invoice');
            $table->string('event_status')->comment('Status from webhook: CAPTURED, FAILED, etc.');
            
            // Payment details
            $table->decimal('amount', 10, 3)->nullable()->comment('Payment amount');
            $table->string('currency', 3)->nullable()->comment('Currency code');
            $table->string('gateway_reference')->nullable()->comment('Gateway reference from webhook');
            $table->string('payment_reference')->nullable()->comment('Payment reference from webhook');
            
            // Security validation
            $table->string('received_hashstring')->nullable()->comment('Hash received from Tap');
            $table->string('calculated_hashstring')->nullable()->comment('Hash calculated by our system');
            $table->boolean('hash_valid')->default(false)->comment('Whether hash validation passed');
            
            // Raw data
            $table->json('webhook_headers')->nullable()->comment('Full webhook headers');
            $table->json('webhook_payload')->comment('Complete webhook JSON payload');
            $table->text('processing_notes')->nullable()->comment('Notes about processing this webhook');
            
            // Processing status
            $table->enum('processing_status', ['received', 'validated', 'processed', 'failed'])
                ->default('received')
                ->comment('Status of webhook processing');
            $table->timestamp('processed_at')->nullable()->comment('When webhook was processed');
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['object_type', 'object_id']);
            $table->index(['event_status']);
            $table->index(['processing_status']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dev_webhook_logs');
    }
};
