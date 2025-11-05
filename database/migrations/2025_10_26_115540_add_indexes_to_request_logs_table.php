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
            // Index untuk optimasi query yang sering digunakan
            $table->index('query_id', 'idx_query_id');
            $table->index('winner_api', 'idx_winner_api');
            $table->index('cache_status', 'idx_cache_status');
            $table->index('complexity', 'idx_complexity');
            $table->index('created_at', 'idx_created_at');
            
            // Composite index untuk query yang menggunakan multiple columns
            $table->index(['query_id', 'created_at'], 'idx_query_created');
            $table->index(['winner_api', 'created_at'], 'idx_winner_created');
            $table->index(['complexity', 'winner_api'], 'idx_complexity_winner');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('request_logs', function (Blueprint $table) {
            $table->dropIndex('idx_query_id');
            $table->dropIndex('idx_winner_api');
            $table->dropIndex('idx_cache_status');
            $table->dropIndex('idx_complexity');
            $table->dropIndex('idx_created_at');
            $table->dropIndex('idx_query_created');
            $table->dropIndex('idx_winner_created');
            $table->dropIndex('idx_complexity_winner');
        });
    }
};
