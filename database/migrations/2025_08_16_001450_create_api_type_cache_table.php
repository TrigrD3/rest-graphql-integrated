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
        Schema::create('api_type_cache', function (Blueprint $table) {
            $table->id();
            $table->string('query_id')->index();
            $table->string('fastest_api_type'); // 'rest' or 'graphql'
            $table->integer('rest_avg_time')->nullable(); // average response time in ms
            $table->integer('graphql_avg_time')->nullable(); // average response time in ms
            $table->integer('test_count')->default(1); // number of tests performed
            $table->timestamp('last_updated')->useCurrent();
            $table->timestamps();
            
            $table->unique('query_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_type_cache');
    }
};
