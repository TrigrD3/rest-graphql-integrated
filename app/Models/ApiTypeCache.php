<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiTypeCache extends Model
{
    use HasFactory;

    protected $table = 'api_type_cache';

    protected $fillable = [
        'query_id',
        'fastest_api_type',
        'rest_avg_time',
        'graphql_avg_time',
        'test_count',
        'last_updated'
    ];

    protected $casts = [
        'rest_avg_time' => 'integer',
        'graphql_avg_time' => 'integer',
        'test_count' => 'integer',
        'last_updated' => 'datetime'
    ];

    /**
     * Get the fastest API type for a query
     */
    public static function getFastestApiType(string $queryId): ?string
    {
        $cache = static::where('query_id', $queryId)->first();
        return $cache ? $cache->fastest_api_type : null;
    }

    /**
     * Update or create cache entry with new performance data
     */
    public static function updateCache(string $queryId, int $restTime, int $graphqlTime): void
    {
        $cache = static::firstOrNew(['query_id' => $queryId]);
        
        if ($cache->exists) {
            // Calculate new averages
            $cache->rest_avg_time = (($cache->rest_avg_time * $cache->test_count) + $restTime) / ($cache->test_count + 1);
            $cache->graphql_avg_time = (($cache->graphql_avg_time * $cache->test_count) + $graphqlTime) / ($cache->test_count + 1);
            $cache->test_count += 1;
        } else {
            // First time
            $cache->rest_avg_time = $restTime;
            $cache->graphql_avg_time = $graphqlTime;
            $cache->test_count = 1;
        }

        // Determine fastest API type
        $cache->fastest_api_type = $cache->rest_avg_time <= $cache->graphql_avg_time ? 'rest' : 'graphql';
        $cache->last_updated = now();
        $cache->save();
    }
}
