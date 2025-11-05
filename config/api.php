<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration settings for the API Gateway service.
    |
    */

    // Base URL for REST API
    'rest_base_url' => env('REST_API_URL', 'https://jsonplaceholder.typicode.com'),

    // Base URL for GraphQL API - Updated to use a public GraphQL API instead of localhost
    'graphql_base_url' => env('GRAPHQL_API_URL', 'https://api.github.com/graphql'),

    // API request timeout in seconds
    'timeout' => env('API_TIMEOUT', 10),
]; 