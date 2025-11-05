<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('performance_metrics', function (Blueprint $table) {
            $table->string('test_type')->nullable()->after('avg_response_time_ms');
        });
    }

    public function down()
    {
        Schema::table('performance_metrics', function (Blueprint $table) {
            $table->dropColumn('test_type');
        });
    }
}; 