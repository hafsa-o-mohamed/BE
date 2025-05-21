<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuggestionRepliesTable extends Migration
{
    public function up()
    {
        Schema::create('suggestion_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('suggestion_id')->constrained('suggestions')->onDelete('cascade');
            $table->text('reply');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // who replied, optional
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suggestion_replies');
    }
}