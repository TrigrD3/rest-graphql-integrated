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
        Schema::table('request_logs', function (Blueprint $table) {
            // Menambahkan kolom untuk metrik performa sesuai metodologi penelitian
            $table->float('cpu_usage')->default(0)->after('winner_api');
            $table->float('memory_usage')->default(0)->after('cpu_usage');
            $table->string('complexity')->default('simple')->after('memory_usage'); // 'simple' atau 'complex'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->dropColumn(['cpu_usage', 'memory_usage', 'complexity']);
        });
    }
};
