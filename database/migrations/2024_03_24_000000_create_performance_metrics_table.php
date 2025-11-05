<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('performance_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('query_id');
            $table->string('api_type'); // 'rest', 'graphql', atau 'integrated'
            $table->float('cpu_usage_percent');
            $table->float('memory_usage_percent');
            $table->integer('request_count');
            $table->float('avg_response_time_ms');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('performance_metrics');
    }
}; 