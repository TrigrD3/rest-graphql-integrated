<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('query_id');
            $table->string('endpoint');
            $table->string('cache_status');
            $table->string('winner_api')->nullable();
            $table->integer('rest_response_time_ms')->nullable();
            $table->integer('graphql_response_time_ms')->nullable();
            $table->boolean('rest_succeeded');
            $table->boolean('graphql_succeeded');
            $table->longText('response_body')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};
