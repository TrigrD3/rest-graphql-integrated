<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'api_type',
        'cpu_usage_percent',
        'memory_usage_percent',
        'request_count',
        'avg_response_time_ms',
        'test_type'
    ];

    protected $casts = [
        'cpu_usage_percent' => 'float',
        'memory_usage_percent' => 'float',
        'request_count' => 'integer',
        'avg_response_time_ms' => 'float'
    ];
}
