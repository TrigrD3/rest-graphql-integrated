<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'query_id',
        'endpoint',
        'cache_status',
        'winner_api',
        'cpu_usage',
        'memory_usage',
        'complexity',
        'rest_response_time_ms',
        'graphql_response_time_ms',
        'rest_succeeded',
        'graphql_succeeded',
        'response_body'
    ];

    protected $casts = [
        'rest_succeeded' => 'boolean',
        'graphql_succeeded' => 'boolean',
    ];
}
