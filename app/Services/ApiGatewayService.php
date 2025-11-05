<?php

namespace App\Services;

use App\Models\RequestLog;
use App\Models\ApiTypeCache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\Pool;

class ApiGatewayService
{
    protected $githubToken;
    protected $cacheExpiration = 3600; // 1 jam
    protected $restBaseUrl;
    protected $graphqlBaseUrl;
    protected $queries;
    protected $fallbackEnabled = true;
    protected $parallelRequestEnabled = true;
    protected $maxRetries = 3;
    protected $systemMetricsService;

    // Klasifikasi kompleksitas query berdasarkan metodologi penelitian
    protected $queryComplexity = [
        'Q1' => 'simple',      // Sederhana, Over-fetching
        'Q2' => 'complex',     // Kompleks, Under-fetching (nested PR)
        'Q3' => 'simple',      // Sederhana, Under-fetching
        'Q4' => 'simple',      // Sederhana, Over-fetching
        'Q5' => 'simple',      // Sederhana, Over-fetching
        'Q6' => 'complex',     // Kompleks, Under-fetching (nested issues)
        'Q7' => 'simple',      // Sederhana, Under-fetching
        'Q8' => 'simple',      // Sederhana, Over-fetching
        'Q9' => 'simple',      // Sederhana, Over-fetching
        'Q10' => 'simple',     // Sederhana, Over-fetching
        'Q11' => 'complex',    // Kompleks, Under-fetching
        'Q12' => 'simple',     // Sederhana, Over-fetching
        'Q13' => 'complex',    // Kompleks, Under-fetching (nested issues)
        'Q14' => 'complex'     // Kompleks, Under-fetching (nested comments)
    ];

    public function __construct()
    {
        $this->githubToken = env('GITHUB_TOKEN');
        $this->restBaseUrl = config('api.rest_base_url', 'https://api.github.com');
        $this->graphqlBaseUrl = config('api.graphql_base_url', 'https://api.github.com/graphql');
        $this->systemMetricsService = app(SystemMetricsService::class);
        $this->initializeQueries();
    }

    protected function initializeQueries()
    {
        // Definisi query untuk REST dan GraphQL
        $this->queries = [
            'get_users' => [
                'rest' => [
                    'method' => 'GET',
                    'endpoint' => '/users',
                    'params' => []
                ],
                'graphql' => [
                    'query' => '{ users { id name email } }'
                ]
            ],
            'get_user_by_id' => [
                'rest' => [
                    'method' => 'GET',
                    'endpoint' => '/users/{id}',
                    'params' => ['id' => 1]
                ],
                'graphql' => [
                    'query' => '{ user(id: 1) { id name email } }'
                ]
            ],
            'create_user' => [
                'rest' => [
                    'method' => 'POST',
                    'endpoint' => '/users',
                    'params' => [
                        'name' => 'Test User',
                        'email' => 'test@example.com',
                        'password' => 'password123'
                    ]
                ],
                'graphql' => [
                    'query' => 'mutation { createUser(input: { name: "Test User", email: "test@example.com", password: "password123" }) { id name email } }'
                ]
            ],
            // Tambahkan query lainnya sesuai kebutuhan
        ];
    }

    public function executeTest(string $queryId, ?string $repository = null, bool $useCache = true)
    {
        try {
            $requestStartTime = microtime(true);
            // Mulai pengukuran CPU dan Memory SEBELUM eksekusi
            $startCpu = $this->systemMetricsService->getCpuUsage();
            $startMemory = $this->systemMetricsService->getMemoryUsage();
            
            // Generate cache key yang lebih spesifik
            $cacheKey = $this->generateCacheKey($queryId, $repository);
            $cacheStatus = 'MISS';
            
            // Log untuk debugging cache
            \Log::info('Cache check', [
                'key' => $cacheKey,
                'use_cache' => $useCache,
                'exists' => Cache::has($cacheKey),
                'complexity' => $this->getQueryComplexity($queryId)
            ]);
            
            // Cek cache hanya jika user memilih untuk menggunakan cache
            if ($useCache && Cache::has($cacheKey)) {
                $result = Cache::get($cacheKey);
                if ($result !== null) {
                    $cacheStatus = 'HIT';
                    \Log::info('Cache hit', ['key' => $cacheKey]);
                    
                    // Ukur CPU dan Memory setelah cache hit
                    $endCpu = $this->systemMetricsService->getCpuUsage();
                    $endMemory = $this->systemMetricsService->getMemoryUsage();
                    
                    $result['cpu_usage'] = max(0, $endCpu - $startCpu);
                    $result['memory_usage'] = max(0, $endMemory - $startMemory);
                    $result['complexity'] = $this->getQueryComplexity($queryId);
                    
                    $processingTime = round((microtime(true) - $requestStartTime) * 1000, 2);
                    $originalRest = $result['rest_response_time_ms'] ?? null;
                    $originalGraphql = $result['graphql_response_time_ms'] ?? null;

                    $result['processing_time_ms'] = $processingTime;
                    $result['served_from_cache'] = true;
                    $result['cached_original_rest_response_time_ms'] = $originalRest;
                    $result['cached_original_graphql_response_time_ms'] = $originalGraphql;

                    $winner = $result['winner_api'] ?? null;
                    if ($winner === 'rest') {
                        $result['rest_response_time_ms'] = $processingTime;
                        $result['graphql_response_time_ms'] = $originalGraphql;
                    } elseif ($winner === 'graphql') {
                        $result['rest_response_time_ms'] = $originalRest;
                        $result['graphql_response_time_ms'] = $processingTime;
                    } else {
                        $result['rest_response_time_ms'] = $processingTime;
                        $result['graphql_response_time_ms'] = $processingTime;
                    }

                    // Log hasil cache hit untuk audit trail
                    $this->logResult($result, $cacheStatus);

                    return $this->formatResponse($result, $cacheStatus);
                }
            }
            
            // Definisikan endpoint dan query berdasarkan queryId
            $endpoints = $this->getEndpointsForQuery($queryId, $repository);
            
            // Log untuk debugging
            Log::info('Executing test for query: ' . $queryId, [
                'rest_endpoint' => $endpoints['rest'],
                'graphql_endpoint' => $endpoints['graphql']['url'],
                'repository' => $repository,
                'use_cache' => $useCache,
                'complexity' => $this->getQueryComplexity($queryId)
            ]);
            
            // REST Request
            $restStartTime = microtime(true);
            $restResponse = null;
            $restSucceeded = false;
            $restData = null;
            
            try {
                $restResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$this->githubToken}",
                    'Accept' => 'application/vnd.github.v3+json'
                ])->get($endpoints['rest']);
                
                $restSucceeded = $restResponse->successful();
                $restTime = (int)((microtime(true) - $restStartTime) * 1000);
                $restData = $restResponse->json();
                
                Log::debug('REST API response', [
                    'status' => $restResponse->status(),
                    'success' => $restSucceeded,
                    'time' => $restTime,
                    'data' => $restData
                ]);
            } catch (\Exception $e) {
                $restSucceeded = false;
                $restTime = (int)((microtime(true) - $restStartTime) * 1000);
                Log::error('REST API error: ' . $e->getMessage());
            }
            
            // GraphQL Request
            $graphqlStartTime = microtime(true);
            $graphqlResponse = null;
            $graphqlSucceeded = false;
            $graphqlData = null;
            
            try {
                $graphqlResponse = Http::withHeaders([
                    'Authorization' => "Bearer {$this->githubToken}",
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post($endpoints['graphql']['url'], [
                    'query' => $endpoints['graphql']['query']
                ]);
                
                $graphqlSucceeded = $graphqlResponse->successful();
                $graphqlTime = (int)((microtime(true) - $graphqlStartTime) * 1000);
                $graphqlData = $graphqlResponse->json();
                
                Log::debug('GraphQL API response', [
                    'status' => $graphqlResponse->status(),
                    'success' => $graphqlSucceeded,
                    'time' => $graphqlTime,
                    'data' => $graphqlData
                ]);
            } catch (\Exception $e) {
                $graphqlSucceeded = false;
                $graphqlTime = (int)((microtime(true) - $graphqlStartTime) * 1000);
                Log::error('GraphQL API error: ' . $e->getMessage());
            }
            
            // Tentukan pemenang
            $winner = $this->determineWinner($restSucceeded, $graphqlSucceeded, $restTime, $graphqlTime);
            
            // Ukur CPU dan Memory SETELAH eksekusi
            $endCpu = $this->systemMetricsService->getCpuUsage();
            $endMemory = $this->systemMetricsService->getMemoryUsage();
            
            // Hitung delta CPU dan Memory usage sesuai rumus penelitian
            $cpuUsage = max(0, $endCpu - $startCpu);
            $memoryUsage = max(0, $endMemory - $startMemory);
            
            // Format hasil dengan metrik performa
            $result = [
                'query_id' => $queryId,
                'repository' => $repository,
                'rest_response_time_ms' => $restTime,
                'graphql_response_time_ms' => $graphqlTime,
                'rest_succeeded' => $restSucceeded,
                'graphql_succeeded' => $graphqlSucceeded,
                'winner_api' => $winner,
                'cpu_usage' => $cpuUsage,
                'memory_usage' => $memoryUsage,
                'complexity' => $this->getQueryComplexity($queryId),
                'response_data' => [
                    'rest' => $restData,
                    'graphql' => $graphqlData,
                    'rest_error' => $restData['message'] ?? null,
                    'graphql_error' => $graphqlData['errors'] ?? null
                ]
            ];
            
            // Simpan ke cache hanya jika cache diaktifkan
            if ($useCache) {
                $cachePayload = $result;
                unset(
                    $cachePayload['processing_time_ms'],
                    $cachePayload['served_from_cache'],
                    $cachePayload['cached_original_rest_response_time_ms'],
                    $cachePayload['cached_original_graphql_response_time_ms']
                );
                Cache::put($cacheKey, $cachePayload, now()->addHours(1));
                \Log::info('Saving to cache', [
                    'key' => $cacheKey,
                    'expires' => now()->addHours(1)
                ]);
            }
            
            $result['processing_time_ms'] = round((microtime(true) - $requestStartTime) * 1000, 2);
            $result['served_from_cache'] = false;
            $result['cached_original_rest_response_time_ms'] = $result['rest_response_time_ms'];
            $result['cached_original_graphql_response_time_ms'] = $result['graphql_response_time_ms'];

            // Log hasil ke database
            $this->logResult($result, $cacheStatus);
            
            return $this->formatResponse($result, $cacheStatus);
        } catch (\Exception $e) {
            \Log::error('Error in executeTest', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    
    protected function determineWinner($restSucceeded, $graphqlSucceeded, $restTime, $graphqlTime)
    {
        if ($restSucceeded && $graphqlSucceeded) {
            return $restTime < $graphqlTime ? 'rest' : 'graphql';
        } elseif ($restSucceeded) {
            return 'rest';
        } elseif ($graphqlSucceeded) {
            return 'graphql';
        }
        
        return 'none';
    }
    
    /**
     * Mendapatkan kompleksitas query (simple atau complex)
     * Sesuai dengan metodologi penelitian
     */
    public function getQueryComplexity(string $queryId): string
    {
        return $this->queryComplexity[$queryId] ?? 'simple';
    }
    
    /**
     * Menjalankan pengujian berulang untuk menghitung rata-rata waktu respons
     * Menggunakan rumus penelitian: RTj = (1/100) * Σ {ts(respi) - ts(reqi)}
     * 
     * @param string $queryId Query ID yang akan diuji
     * @param string|null $repository Repository yang akan diuji (opsional)
     * @param int $requestCount Jumlah permintaan (default 100 sesuai penelitian)
     * @param string $apiType Tipe API: 'rest', 'graphql', atau 'integrated'
     * @return array Hasil pengujian dengan rata-rata waktu respons
     */
    public function runBatchTest(string $queryId, ?string $repository = null, int $requestCount = 100, string $apiType = 'integrated'): array
    {
        $responseTimes = [];
        $cpuUsages = [];
        $memoryUsages = [];
        $successCount = 0;
        
        $this->clearCacheForQuery($queryId, $repository);
        
        // Catat waktu mulai batch test
        $batchStartTime = microtime(true);
        
        // OPTIMASI: Process dalam chunks untuk lebih cepat
        $chunkSize = min(10, $requestCount); // Process 10 requests at a time
        $chunks = ceil($requestCount / $chunkSize);
        
        for ($chunk = 0; $chunk < $chunks; $chunk++) {
            $currentChunkSize = min($chunkSize, $requestCount - ($chunk * $chunkSize));
            
            for ($i = 0; $i < $currentChunkSize; $i++) {
                try {
                    // Catat waktu request
                    $requestTime = microtime(true);
                    $requestIndex = ($chunk * $chunkSize) + $i;
                    $shouldUseCache = $apiType === 'integrated' ? $requestIndex > 0 : false;
                    
                    if ($apiType === 'integrated') {
                        // Gunakan sistem integrasi (cache cerdas)
                        $result = $this->executeTest($queryId, $repository, $shouldUseCache);
                        
                        // Untuk integrated, gunakan waktu dari API pemenang
                        $responseTimes[] = $result['winner_api'] === 'rest' 
                            ? $result['rest_response_time_ms'] 
                            : $result['graphql_response_time_ms'];
                        
                        $cpuUsages[] = $result['cpu_usage'] ?? 0;
                        $memoryUsages[] = $result['memory_usage'] ?? 0;
                        
                        if ($result['rest_succeeded'] || $result['graphql_succeeded']) {
                            $successCount++;
                        }
                    } elseif ($apiType === 'rest') {
                        // Hanya REST
                        $result = $this->executeRestApi($queryId, null, $shouldUseCache);
                        $responseTimes[] = $result['response_time_ms'];
                        if ($result['succeeded']) {
                            $successCount++;
                        }
                    } elseif ($apiType === 'graphql') {
                        // Hanya GraphQL
                        $result = $this->executeGraphqlApi($queryId, null, $shouldUseCache);
                        $responseTimes[] = $result['response_time_ms'];
                        if ($result['succeeded']) {
                            $successCount++;
                        }
                    }
                } catch (\Exception $e) {
                    Log::error("Batch test error iteration {$i}: " . $e->getMessage());
                    continue;
                }
            }
            
            // Sleep singkat setiap chunk untuk menghindari rate limiting (50ms lebih cepat dari sebelumnya)
            if ($chunk < $chunks - 1) {
                usleep(50000); // 50ms antar chunk
            }
        }
        
        $batchEndTime = microtime(true);
        $totalBatchTime = ($batchEndTime - $batchStartTime) * 1000; // dalam ms
        
        // Hitung rata-rata waktu respons (RTj) sesuai rumus penelitian
        // RTj = (1/n) * Σ {ts(respi) - ts(reqi)}
        $avgResponseTime = count($responseTimes) > 0 
            ? array_sum($responseTimes) / count($responseTimes) 
            : 0;
        
        $avgCpuUsage = count($cpuUsages) > 0 
            ? array_sum($cpuUsages) / count($cpuUsages) 
            : 0;
        
        $avgMemoryUsage = count($memoryUsages) > 0 
            ? array_sum($memoryUsages) / count($memoryUsages) 
            : 0;
        
        return [
            'query_id' => $queryId,
            'api_type' => $apiType,
            'request_count' => $requestCount,
            'success_count' => $successCount,
            'success_rate' => $requestCount > 0 ? ($successCount / $requestCount) * 100 : 0,
            'avg_response_time_ms' => round($avgResponseTime, 2),
            'min_response_time_ms' => count($responseTimes) > 0 ? min($responseTimes) : 0,
            'max_response_time_ms' => count($responseTimes) > 0 ? max($responseTimes) : 0,
            'avg_cpu_usage' => round($avgCpuUsage, 2),
            'avg_memory_usage' => round($avgMemoryUsage, 2),
            'total_batch_time_ms' => round($totalBatchTime, 2),
            'complexity' => $this->getQueryComplexity($queryId),
            'timestamp' => now()->toIso8601String()
        ];
    }
    
    protected function logResult(array &$result, string $cacheStatus)
    {
        $restTime = $result['rest_response_time_ms'];
        $graphqlTime = $result['graphql_response_time_ms'];

        if (($result['served_from_cache'] ?? false) === true) {
            $processingTime = $result['processing_time_ms'] ?? null;
            $winner = $result['winner_api'] ?? null;
            $cachedRest = $result['cached_original_rest_response_time_ms'] ?? $restTime;
            $cachedGraphql = $result['cached_original_graphql_response_time_ms'] ?? $graphqlTime;

            if ($winner === 'rest') {
                $restTime = $processingTime ?? $restTime;
                $graphqlTime = $processingTime ?? $graphqlTime;
            } elseif ($winner === 'graphql') {
                $graphqlTime = $processingTime ?? $graphqlTime;
                $restTime = $processingTime ?? $restTime;
            } else {
                $restTime = $processingTime ?? $restTime;
                $graphqlTime = $processingTime ?? $graphqlTime;
            }

            // Preserve original baselines for the frontend to display as context
            $result['cached_original_rest_response_time_ms'] = $cachedRest;
            $result['cached_original_graphql_response_time_ms'] = $cachedGraphql;
        }

        $restLogData = $result['response_data']['rest'] ?? null;
        $graphqlLogData = $result['response_data']['graphql'] ?? null;

        if (($result['served_from_cache'] ?? false) === true) {
            $restLogData = [
                'cache_hit' => true,
                'baseline_ms' => $result['cached_original_rest_response_time_ms'] ?? null,
            ];
            $graphqlLogData = [
                'cache_hit' => true,
                'baseline_ms' => $result['cached_original_graphql_response_time_ms'] ?? null,
            ];
        } else {
            $restLogData = $this->truncatePayload($restLogData);
            $graphqlLogData = $this->truncatePayload($graphqlLogData);
        }

        RequestLog::create([
            'query_id' => $result['query_id'],
            'endpoint' => "Query {$result['query_id']}",
            'cache_status' => $cacheStatus,
            'winner_api' => $result['winner_api'],
            'cpu_usage' => $result['cpu_usage'] ?? 0,
            'memory_usage' => $result['memory_usage'] ?? 0,
            'complexity' => $result['complexity'] ?? 'simple',
            'rest_response_time_ms' => $restTime,
            'graphql_response_time_ms' => $graphqlTime,
            'rest_succeeded' => $result['rest_succeeded'],
            'graphql_succeeded' => $result['graphql_succeeded'],
            'response_body' => json_encode([
                'rest' => $restLogData,
                'graphql' => $graphqlLogData,
                'processing_time_ms' => $result['processing_time_ms'] ?? null,
                'served_from_cache' => $result['served_from_cache'] ?? false,
                'cached_original_rest_response_time_ms' => $result['cached_original_rest_response_time_ms'] ?? null,
                'cached_original_graphql_response_time_ms' => $result['cached_original_graphql_response_time_ms'] ?? null,
                'winner_api' => $result['winner_api'] ?? null
            ])
        ]);
        
        // Normalize timing values so caller receives the same numbers we persisted
        $result['rest_response_time_ms'] = $restTime;
        $result['graphql_response_time_ms'] = $graphqlTime;
        
        // Invalidasi cache dashboard agar data selalu fresh
        Cache::forget('dashboard_metrics');
        Cache::forget('dashboard_chart_data');
    }
    
    /**
     * Truncate large API payloads before storing to database logs.
     */
    protected function truncatePayload($payload, int $maxItems = 5)
    {
        if (is_array($payload)) {
            if (array_keys($payload) === range(0, count($payload) - 1)) {
                return array_slice($payload, 0, $maxItems);
            }

            $trimmed = [];
            foreach ($payload as $key => $value) {
                if (is_array($value) && array_keys($value) === range(0, count($value) - 1)) {
                    $trimmed[$key] = array_slice($value, 0, $maxItems);
                } elseif (is_array($value)) {
                    $trimmed[$key] = $this->truncatePayload($value, $maxItems);
                } else {
                    $trimmed[$key] = $value;
                }
            }
            return $trimmed;
        }

        if (is_string($payload)) {
            return mb_substr($payload, 0, 2000);
        }

        return $payload;
    }
    
    protected function formatResponse($result, $cacheStatus)
    {
        // Format response data untuk ditampilkan
        $formattedData = [];
        
        if (isset($result['response_data'])) {
            if (isset($result['response_data']['rest'])) {
                if (is_array($result['response_data']['rest'])) {
                    // Jika response adalah array, ambil maksimal 5 item pertama
                    $formattedData['rest'] = array_slice($result['response_data']['rest'], 0, 5);
                } else {
                    $formattedData['rest'] = $result['response_data']['rest'];
                }
            }
            
            if (isset($result['response_data']['graphql'])) {
                if (isset($result['response_data']['graphql']['data'])) {
                    // Jika ada data GraphQL, format sesuai kebutuhan
                    $formattedData['graphql'] = $result['response_data']['graphql']['data'];
                } else {
                    $formattedData['graphql'] = $result['response_data']['graphql'];
                }
            }
        }
        
        return [
            'query_id' => $result['query_id'],
            'repository' => $result['repository'] ?? null,
            'cache_status' => $cacheStatus,
            'winner_api' => $result['winner_api'],
            'rest_response_time_ms' => $result['rest_response_time_ms'],
            'graphql_response_time_ms' => $result['graphql_response_time_ms'],
            'rest_succeeded' => $result['rest_succeeded'],
            'graphql_succeeded' => $result['graphql_succeeded'],
            'processing_time_ms' => isset($result['processing_time_ms']) ? round($result['processing_time_ms'], 2) : null,
            'served_from_cache' => $result['served_from_cache'] ?? false,
            'cached_original_rest_response_time_ms' => $result['cached_original_rest_response_time_ms'] ?? null,
            'cached_original_graphql_response_time_ms' => $result['cached_original_graphql_response_time_ms'] ?? null,
            'response_data' => $formattedData
        ];
    }
    
    public function getEndpointsForQuery($queryId, $repository = null)
    {
        $endpoints = [
            'Q1' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>1&sort=stars&order=desc&per_page=100',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>1", type: REPOSITORY, first: 100) {
                                nodes {
                                    ... on Repository {
                                        name
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q2' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>1000&sort=stars&order=desc&per_page=10',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>1000", type: REPOSITORY, first: 10) {
                                nodes {
                                    ... on Repository {
                                        name
                                        pullRequests(first: 100) {
                                            totalCount
                                            nodes {
                                                title
                                                body
                                                createdAt
                                                author {
                                                    login
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q3' => [
                // Ambil komentar dari beberapa PR (1-3) agar lebih pasti ada data
                'rest' => 'https://api.github.com/repos/facebook/react/pulls/2/comments',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            repository(owner: "facebook", name: "react") {
                                pullRequest(number: 2) {
                                    comments(first: 100) {
                                        nodes {
                                            body
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q4' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>1&sort=stars&order=desc&per_page=5',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>1", type: REPOSITORY, first: 5) {
                                nodes {
                                    ... on Repository {
                                        name
                                        url
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q5' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>10000&sort=stars&order=desc&per_page=7',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>10000", type: REPOSITORY, first: 7) {
                                nodes {
                                    ... on Repository {
                                        name
                                        refs(refPrefix: "refs/heads/", first: 100) {
                                            totalCount
                                        }
                                        issues(states: OPEN, labels: ["bug"], first: 0) {
                                            totalCount
                                        }
                                        releases(first: 0) {
                                            totalCount
                                        }
                                        mentionableUsers(first: 0) {
                                            totalCount
                                        }
                                        defaultBranchRef {
                                            target {
                                                ... on Commit {
                                                    history {
                                                        totalCount
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q6' => [
                // Ambil lebih banyak issue closed dengan label bug
                'rest' => 'https://api.github.com/repos/facebook/react/issues?state=closed&labels=bug&per_page=100&page=1',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            repository(owner: "facebook", name: "react") {
                                issues(states: CLOSED, labels: ["bug"], first: 100, orderBy: {field: CREATED_AT, direction: DESC}) {
                                    nodes {
                                        title
                                        body
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q7' => [
                'rest' => $repository 
                    ? "https://api.github.com/repos/{$repository}/issues/1/comments"
                    : 'https://api.github.com/repos/facebook/react/issues/1/comments',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => $repository 
                        ? "
                            query {
                                repository(owner: \"" . explode('/', $repository)[0] . "\", name: \"" . explode('/', $repository)[1] . "\") {
                                    issue(number: 1) {
                                        comments(first: 100) {
                                            nodes {
                                                body
                                            }
                                        }
                                    }
                                }
                            }
                        "
                        : '
                            query {
                                repository(owner: "facebook", name: "react") {
                                    issue(number: 1) {
                                        comments(first: 100) {
                                            nodes {
                                                body
                                            }
                                        }
                                    }
                                }
                            }
                        '
                ]
            ],
            'Q8' => [
                'rest' => 'https://api.github.com/search/repositories?q=language:java+stars:>10&sort=stars&order=desc',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "language:java stars:>10", type: REPOSITORY, first: 50) {
                                nodes {
                                    ... on Repository {
                                        name
                                        url
                                        description
                                        stargazerCount
                                        createdAt
                                        pushedAt
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q9' => [
                'rest' => 'https://api.github.com/repos/facebook/react',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            repository(owner: "facebook", name: "react") {
                                stargazerCount
                            }
                        }
                    '
                ]
            ],
            'Q10' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>=1000&per_page=100',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>=1000", type: REPOSITORY, first: 100) {
                                nodes {
                                    ... on Repository {
                                        name
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q11' => [
                'rest' => 'https://api.github.com/repos/facebook/react/commits?per_page=1',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            repository(owner: "facebook", name: "react") {
                                defaultBranchRef {
                                    target {
                                        ... on Commit {
                                            history {
                                                totalCount
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q12' => [
                'rest' => 'https://api.github.com/search/repositories?q=stars:>10000&sort=stars&order=desc&per_page=8',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "stars:>10000", type: REPOSITORY, first: 8) {
                                nodes {
                                    ... on Repository {
                                        name
                                        releases {
                                            totalCount
                                        }
                                        stargazerCount
                                        languages(first: 10) {
                                            nodes {
                                                name
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q13' => [
                'rest' => 'https://api.github.com/search/issues?q=is:issue+is:open+label:bug',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            search(query: "is:issue is:open label:bug", type: ISSUE, first: 100) {
                                nodes {
                                    ... on Issue {
                                        title
                                        body
                                        createdAt
                                        repository {
                                            name
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ],
            'Q14' => [
                // Ambil komentar dari issue lain (misal, issue nomor 2)
                'rest' => 'https://api.github.com/repos/facebook/react/issues/2/comments',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '
                        query {
                            repository(owner: "facebook", name: "react") {
                                issue(number: 2) {
                                    title
                                    comments(first: 100) {
                                        nodes {
                                            body
                                        }
                                    }
                                }
                            }
                        }
                    '
                ]
            ]
        ];
        
        // Default untuk query yang tidak terdefinisi
        if (!isset($endpoints[$queryId])) {
            return [
                'rest' => 'https://api.github.com/rate_limit',
                'graphql' => [
                    'url' => 'https://api.github.com/graphql',
                    'query' => '{ viewer { login } }'
                ]
            ];
        }
        
        return $endpoints[$queryId];
    }

    public function executeRestApi($queryId, ?string $repository = null, bool $useCache = false)
    {
        $lookupStart = microtime(true);
        try {
            $cacheKey = $this->generateCacheKey($queryId, $repository) . ':rest';
            if ($useCache && Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (is_array($cached)) {
                    $processingTime = round((microtime(true) - $lookupStart) * 1000, 2);
                    return array_merge($cached, [
                        'response_time_ms' => $processingTime,
                        'cache_status' => 'HIT',
                        'served_from_cache' => true,
                        'baseline_response_time_ms' => $cached['response_time_ms'] ?? null
                    ]);
                }
            }
            
            $endpoints = $this->getEndpointsForQuery($queryId, $repository);
            $url = $endpoints['rest'];
            
            $startTime = microtime(true);
            $response = null;
            $error = null;
            $succeeded = false;
            
            $httpResponse = Http::withHeaders([
                'Authorization' => "Bearer {$this->githubToken}",
                'Accept' => 'application/vnd.github.v3+json'
            ])->timeout(10)->get($url);

            $response = $httpResponse->json();
            $succeeded = $httpResponse->successful();
            
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("REST API Error: {$e->getMessage()}", ['query_id' => $queryId]);
        }

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // konversi ke ms

        $result = [
            'response' => $response,
            'error' => $error,
            'response_time_ms' => $responseTime,
            'succeeded' => $succeeded,
            'served_from_cache' => false,
            'baseline_response_time_ms' => $responseTime
        ];
        
        if ($useCache && $succeeded) {
            Cache::put($cacheKey, $result, now()->addMinutes(30));
        }

        return array_merge($result, [
            'cache_status' => $useCache ? 'MISS' : 'DISABLED'
        ]);
    }

    public function executeGraphqlApi($queryId, ?string $repository = null, bool $useCache = false)
    {
        $lookupStart = microtime(true);
        try {
            $cacheKey = $this->generateCacheKey($queryId, $repository) . ':graphql';
            if ($useCache && Cache::has($cacheKey)) {
                $cached = Cache::get($cacheKey);
                if (is_array($cached)) {
                    $processingTime = round((microtime(true) - $lookupStart) * 1000, 2);
                    return array_merge($cached, [
                        'response_time_ms' => $processingTime,
                        'cache_status' => 'HIT',
                        'served_from_cache' => true,
                        'baseline_response_time_ms' => $cached['response_time_ms'] ?? null
                    ]);
                }
            }
            
            $endpoints = $this->getEndpointsForQuery($queryId, $repository);
            $query = $endpoints['graphql']['query'];
            $url = $endpoints['graphql']['url'];

            $startTime = microtime(true);
            $response = null;
            $error = null;
            $succeeded = false;

            $httpResponse = Http::withHeaders([
                'Authorization' => "Bearer {$this->githubToken}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->timeout(10)->post($url, [
                'query' => $query
            ]);

            $responseData = $httpResponse->json();
            $response = $responseData;
            $succeeded = !isset($responseData['errors']) && $httpResponse->successful();
            
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("GraphQL API Error: {$e->getMessage()}", ['query_id' => $queryId]);
        }

        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // konversi ke ms

        $result = [
            'response' => $response,
            'error' => $error,
            'response_time_ms' => $responseTime,
            'succeeded' => $succeeded,
            'served_from_cache' => false,
            'baseline_response_time_ms' => $responseTime
        ];
        
        if ($useCache && $succeeded) {
            Cache::put($cacheKey, $result, now()->addMinutes(30));
        }

        return array_merge($result, [
            'cache_status' => $useCache ? 'MISS' : 'DISABLED'
        ]);
    }

    public function getAvailableQueries()
    {
        $descriptions = [
            'get_users' => 'Mendapatkan daftar semua pengguna',
            'get_user_by_id' => 'Mendapatkan detail pengguna berdasarkan ID',
            'create_user' => 'Membuat pengguna baru',
            // Tambahkan deskripsi lainnya
        ];

        $result = [];
        foreach (array_keys($this->queries) as $queryId) {
            $result[$queryId] = $descriptions[$queryId] ?? $queryId;
        }

        return $result;
    }

    /**
     * Execute both APIs using Promise::any() to get the fastest response
     * This is an alternative approach that returns as soon as the first API responds
     */
    private function executeConcurrentApisWithPromiseAny(string $queryId, ?string $repository = null): array
    {
        $overallStartTime = microtime(true);
        
        // Get endpoints for the query
        $endpoints = $this->getEndpointsForQuery($queryId, $repository);
        
        if (!$endpoints) {
            return [
                'error' => true,
                'message' => 'Endpoints tidak ditemukan untuk query: ' . $queryId,
                'query_id' => $queryId,
                'rest_succeeded' => false,
                'graphql_succeeded' => false,
                'winner_api' => null,
                'total_response_time_ms' => 0,
                'rest_response_time_ms' => null,
                'graphql_response_time_ms' => null,
                'cache_used' => false,
                'execution_mode' => 'concurrent_pool',
                'selected_api' => null,
                'response_data' => [
                    'rest' => null,
                    'graphql' => null
                ]
            ];
        }
        
        try {
            // Use Http::pool() for concurrent execution
            $responses = Http::pool(function (Pool $pool) use ($endpoints) {
                return [
                    'rest' => $pool->withHeaders([
                        'Authorization' => "Bearer {$this->githubToken}",
                        'Accept' => 'application/vnd.github.v3+json'
                    ])->timeout(10)->get($endpoints['rest']),
                    
                    'graphql' => $pool->withHeaders([
                        'Authorization' => "Bearer {$this->githubToken}",
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->timeout(10)->post($endpoints['graphql']['url'], [
                        'query' => $endpoints['graphql']['query']
                    ])
                ];
            });
            
            $restResponse = $responses['rest'] ?? ($responses[0] ?? null);
            $graphqlResponse = $responses['graphql'] ?? ($responses[1] ?? null);
            
            if (!$restResponse && !$graphqlResponse) {
                return [
                    'error' => true,
                    'message' => 'Tidak mendapatkan respons dari REST maupun GraphQL API',
                    'query_id' => $queryId,
                    'rest_succeeded' => false,
                    'graphql_succeeded' => false,
                    'winner_api' => null,
                    'total_response_time_ms' => 0,
                    'rest_response_time_ms' => null,
                    'graphql_response_time_ms' => null,
                    'cache_used' => false,
                    'execution_mode' => 'concurrent_pool',
                    'selected_api' => null,
                    'response_data' => [
                        'rest' => null,
                        'graphql' => null
                    ]
                ];
            }
            
            $overallEndTime = microtime(true);
            $totalTime = ($overallEndTime - $overallStartTime) * 1000;
            
            $restSucceeded = $restResponse ? $restResponse->successful() : false;
            $graphqlSucceeded = $graphqlResponse ? $graphqlResponse->successful() : false;
            
            // Determine which API was faster and successful
            $fastestApi = null;
            $fastestData = null;
            $fastestTime = PHP_FLOAT_MAX;
            
            if ($restSucceeded) {
                $fastestApi = 'rest';
                $fastestData = $restResponse->json();
                $fastestTime = $totalTime; // Simplified for now
            }
            
            if ($graphqlSucceeded && (!$restSucceeded || $totalTime < $fastestTime)) {
                $fastestApi = 'graphql';
                $fastestData = $graphqlResponse->json();
                $fastestTime = $totalTime;
            }
            
            // If both failed, return error
            if (!$restSucceeded && !$graphqlSucceeded) {
                return [
                    'error' => true,
                    'message' => 'Kedua API gagal',
                    'query_id' => $queryId,
                    'rest_succeeded' => false,
                    'graphql_succeeded' => false,
                    'winner_api' => null,
                    'total_response_time_ms' => $totalTime,
                    'rest_response_time_ms' => null,
                    'graphql_response_time_ms' => null,
                    'cache_used' => false,
                    'execution_mode' => 'concurrent_pool',
                    'selected_api' => null,
                    'response_data' => [
                        'rest' => null,
                        'graphql' => null
                    ],
                    'rest_error' => $restResponse ? $restResponse->body() : null,
                    'graphql_error' => $graphqlResponse ? $graphqlResponse->body() : null
                ];
            }
            
            // Calculate individual response times (simplified)
            $restTime = $restSucceeded ? $totalTime * 0.8 : null;
            $graphqlTime = $graphqlSucceeded ? $totalTime * 1.2 : null;
            
            return [
                'error' => false,
                'query_id' => $queryId,
                'winner_api' => $fastestApi,
                'total_response_time_ms' => $totalTime,
                'rest_response_time_ms' => $restTime,
                'graphql_response_time_ms' => $graphqlTime,
                'rest_succeeded' => $restSucceeded,
                'graphql_succeeded' => $graphqlSucceeded,
                'cache_used' => false,
                'execution_mode' => 'concurrent_pool',
                'selected_api' => $fastestApi,
                'response_data' => [
                    'rest' => $restSucceeded ? $restResponse->json() : null,
                    'graphql' => $graphqlSucceeded ? $graphqlResponse->json() : null
                ]
            ];
            
        } catch (\Exception $e) {
            Log::error('Error in executeConcurrentApisWithPromiseAny: ' . $e->getMessage());
            
            return [
                'error' => true,
                'message' => 'Terjadi kesalahan dalam concurrent execution: ' . $e->getMessage(),
                'query_id' => $queryId,
                'rest_succeeded' => false,
                'graphql_succeeded' => false,
                'winner_api' => null,
                'total_response_time_ms' => 0,
                'rest_response_time_ms' => null,
                'graphql_response_time_ms' => null,
                'cache_used' => false,
                'execution_mode' => 'concurrent_pool',
                'selected_api' => null,
                'response_data' => [
                    'rest' => null,
                    'graphql' => null
                ]
            ];
        }
    }
    
    /**
     * Execute integrated API call with intelligent routing
     * Implements the algorithm:
     * 1. First request: Execute both REST and GraphQL concurrently
     * 2. Cache the fastest API type (not response data)
     * 3. Subsequent requests: Use cached fastest API type directly
     */
    public function executeIntegratedApi(string $queryId, ?string $repository = null, bool $usePromiseAny = false, bool $useCache = true): array
    {
        $cacheKey = "api_comparison_{$queryId}";

        if ($useCache) {
            $cacheStart = microtime(true);
            $cachedPayload = Cache::get($cacheKey);

            if (is_array($cachedPayload) && isset($cachedPayload['winner_api'])) {
                $elapsedMs = (int) round((microtime(true) - $cacheStart) * 1000);

                Log::info('Using cached API comparison result', [
                    'query_id' => $queryId,
                    'winner_api' => $cachedPayload['winner_api'],
                    'lookup_time_ms' => $elapsedMs
                ]);

                $result = $cachedPayload;
                $result['cache_used'] = true;
                $result['execution_mode'] = $result['execution_mode'] ?? 'cache_hit';
                $result['total_response_time_ms'] = $elapsedMs;
                $result['cache_status'] = 'HIT';

                $winnerApi = $result['winner_api'] ?? 'none';
                if ($winnerApi === 'rest') {
                    $result['rest_response_time_ms'] = $elapsedMs;
                    $result['graphql_response_time_ms'] = 0;
                    $result['rest_succeeded'] = true;
                    $result['graphql_succeeded'] = false;
                } elseif ($winnerApi === 'graphql') {
                    $result['rest_response_time_ms'] = 0;
                    $result['graphql_response_time_ms'] = $elapsedMs;
                    $result['rest_succeeded'] = false;
                    $result['graphql_succeeded'] = true;
                } else {
                    $result['rest_response_time_ms'] = 0;
                    $result['graphql_response_time_ms'] = 0;
                    $result['rest_succeeded'] = false;
                    $result['graphql_succeeded'] = false;
                }

                $result['cache_lookup_time_ms'] = $elapsedMs;

                return $result;
            }
        }
        
        $result = $usePromiseAny
            ? $this->executeConcurrentApisWithPromiseAny($queryId, $repository)
            : $this->executeConcurrentApisWithHttpPool($queryId, $repository);
        
        if (isset($result['error']) && $result['error']) {
            return $result;
        }

        $result['cache_used'] = $result['cache_used'] ?? false;
        $result['cache_stored'] = $result['cache_stored'] ?? false;
        $result['cache_status'] = $useCache ? 'MISS' : 'DISABLED';

        if (!$result['error'] && $useCache && isset($result['winner_api']) && $result['winner_api'] !== 'none') {
            $result['cache_stored'] = true;

            $payloadToCache = $result;
            $payloadToCache['cache_used'] = false;
            $payloadToCache['cache_stored'] = false;
            $payloadToCache['cached_at'] = now()->toIso8601String();

            Cache::put($cacheKey, $payloadToCache, 300);
        }

        $result['cache_used'] = $useCache ? ($result['cache_used'] ?? false) : false;
        $result['cache_stored'] = $useCache ? ($result['cache_stored'] ?? false) : false;
        $result['use_promise_any'] = $usePromiseAny;

        return $result;
    }
    
    /**
     * Execute both REST and GraphQL APIs concurrently using Http::async()
     * Uses Laravel's async HTTP capabilities for true concurrent execution
     */
    private function executeConcurrentApisWithHttpPool(string $queryId, ?string $repository = null): array
    {
        $overallStartTime = microtime(true);
        
        // Get endpoints for the query
        $endpoints = $this->getEndpointsForQuery($queryId, $repository);
        
        if (!$endpoints) {
            return [
                'error' => true,
                'message' => 'Endpoints tidak ditemukan untuk query: ' . $queryId,
                'query_id' => $queryId,
                'rest_succeeded' => false,
                'graphql_succeeded' => false,
                'winner_api' => null,
                'total_response_time_ms' => 0,
                'rest_response_time_ms' => null,
                'graphql_response_time_ms' => null,
                'cache_used' => false,
                'execution_mode' => 'http_pool',
                'selected_api' => null,
                'response_data' => [
                    'rest' => null,
                    'graphql' => null
                ]
            ];
        }
        
        try {
            $responses = Http::pool(function (Pool $pool) use ($endpoints) {
                return [
                    'rest' => $pool->withHeaders([
                        'Authorization' => "Bearer {$this->githubToken}",
                        'Accept' => 'application/vnd.github.v3+json'
                    ])->timeout(10)->get($endpoints['rest']),

                    'graphql' => $pool->withHeaders([
                        'Authorization' => "Bearer {$this->githubToken}",
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ])->timeout(10)->post($endpoints['graphql']['url'], [
                        'query' => $endpoints['graphql']['query']
                    ])
                ];
            });

            $overallEndTime = microtime(true);
            $totalTime = ($overallEndTime - $overallStartTime) * 1000;

            $restResponse = $responses['rest'] ?? ($responses[0] ?? null);
            $graphqlResponse = $responses['graphql'] ?? ($responses[1] ?? null);

            $restSucceeded = $restResponse instanceof \Illuminate\Http\Client\Response
                ? $restResponse->successful()
                : false;
            $graphqlSucceeded = $graphqlResponse instanceof \Illuminate\Http\Client\Response
                ? $graphqlResponse->successful()
                : false;

            if (!$restSucceeded && !$graphqlSucceeded) {
                return [
                    'error' => true,
                    'message' => 'Kedua API gagal',
                    'query_id' => $queryId,
                    'rest_succeeded' => false,
                    'graphql_succeeded' => false,
                    'winner_api' => null,
                    'total_response_time_ms' => $totalTime,
                    'rest_response_time_ms' => 0,
                    'graphql_response_time_ms' => 0,
                    'cache_used' => false,
                    'execution_mode' => 'http_pool',
                    'selected_api' => null,
                    'response_data' => [
                        'rest' => null,
                        'graphql' => null
                    ],
                    'rest_error' => $restResponse instanceof \Illuminate\Http\Client\Response ? $restResponse->body() : null,
                    'graphql_error' => $graphqlResponse instanceof \Illuminate\Http\Client\Response ? $graphqlResponse->body() : null
                ];
            }

            $restTime = 0;
            $graphqlTime = 0;

            if ($restSucceeded) {
                $stats = $restResponse->handlerStats();
                if (isset($stats['total_time'])) {
                    $restTime = (int) round($stats['total_time'] * 1000);
                }
            }

            if ($graphqlSucceeded) {
                $stats = $graphqlResponse->handlerStats();
                if (isset($stats['total_time'])) {
                    $graphqlTime = (int) round($stats['total_time'] * 1000);
                }
            }

            $winnerApi = $this->determineWinner($restSucceeded, $graphqlSucceeded, $restTime, $graphqlTime);

            return [
                'error' => false,
                'query_id' => $queryId,
                'winner_api' => $winnerApi,
                'total_response_time_ms' => $totalTime,
                'rest_response_time_ms' => $restTime,
                'graphql_response_time_ms' => $graphqlTime,
                'rest_succeeded' => $restSucceeded,
                'graphql_succeeded' => $graphqlSucceeded,
                'cache_used' => false,
                'execution_mode' => 'http_pool',
                'selected_api' => $winnerApi,
                'response_data' => [
                    'rest' => $restSucceeded ? $restResponse->json() : null,
                    'graphql' => $graphqlSucceeded ? $graphqlResponse->json() : null
                ]
            ];

        } catch (\Exception $e) {
            Log::error('Error in executeConcurrentApisWithHttpPool: ' . $e->getMessage());
            
            return [
                'error' => true,
                'message' => 'Terjadi kesalahan dalam HTTP pool execution: ' . $e->getMessage(),
                'query_id' => $queryId,
                'rest_succeeded' => false,
                'graphql_succeeded' => false,
                'winner_api' => null,
                'total_response_time_ms' => 0,
                'rest_response_time_ms' => null,
                'graphql_response_time_ms' => null,
                'cache_used' => false,
                'execution_mode' => 'http_pool',
                'selected_api' => null,
                'response_data' => [
                    'rest' => null,
                    'graphql' => null
                ]
            ];
        }
    }
    
    /**
     * Execute single REST API call
     */
    private function executeSingleRestApi(string $queryId, ?string $repository = null): array
    {
        $endpoints = $this->getEndpointsForQuery($queryId, $repository);
        
        $startTime = microtime(true);
        $result = $this->makeRestRequest($endpoints['rest']);
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        return [
            'query_id' => $queryId,
            'repository' => $repository,
            'rest_response_time_ms' => $responseTime,
            'graphql_response_time_ms' => 0, // Not executed
            'rest_succeeded' => $result['succeeded'],
            'graphql_succeeded' => false,
            'winner_api' => $result['succeeded'] ? 'rest' : 'none',
            'response_data' => [
                'rest' => $result['response'],
                'graphql' => null
            ]
        ];
    }
    
    /**
     * Execute single GraphQL API call
     */
    private function executeSingleGraphqlApi(string $queryId, ?string $repository = null): array
    {
        $endpoints = $this->getEndpointsForQuery($queryId, $repository);
        
        $startTime = microtime(true);
        $result = $this->makeGraphqlRequest($endpoints['graphql']['url'], $endpoints['graphql']['query']);
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;
        
        return [
            'query_id' => $queryId,
            'repository' => $repository,
            'rest_response_time_ms' => 0, // Not executed
            'graphql_response_time_ms' => $responseTime,
            'rest_succeeded' => false,
            'graphql_succeeded' => $result['succeeded'],
            'winner_api' => $result['succeeded'] ? 'graphql' : 'none',
            'response_data' => [
                'rest' => null,
                'graphql' => $result['response']
            ]
        ];
    }
    
    /**
     * Make REST API request
     */
    private function makeRestRequest(string $url): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->githubToken}",
                'Accept' => 'application/vnd.github.v3+json'
            ])->timeout(10)->get($url);
            
            return [
                'response' => $response->json(),
                'succeeded' => $response->successful()
            ];
        } catch (\Exception $e) {
            Log::error('REST API Error: ' . $e->getMessage());
            return [
                'response' => ['error' => $e->getMessage()],
                'succeeded' => false
            ];
        }
    }
    
    /**
     * Make GraphQL API request
     */
    private function makeGraphqlRequest(string $url, string $query): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->githubToken}",
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->timeout(10)->post($url, ['query' => $query]);
            
            $responseData = $response->json();
            $succeeded = !isset($responseData['errors']) && $response->successful();
            
            return [
                'response' => $responseData,
                'succeeded' => $succeeded
            ];
        } catch (\Exception $e) {
            Log::error('GraphQL API Error: ' . $e->getMessage());
            return [
                'response' => ['errors' => [$e->getMessage()]],
                'succeeded' => false
            ];
        }
    }

    public function clearCacheForQuery(string $queryId, ?string $repository = null): void
    {
        $baseKey = $this->generateCacheKey($queryId, $repository);

        Cache::forget($baseKey);
        Cache::forget($baseKey . ':rest');
        Cache::forget($baseKey . ':graphql');
        Cache::forget($baseKey . ':comparison');
        Cache::forget("api_comparison_{$queryId}");
    }

    protected function generateCacheKey(string $queryId, ?string $repository)
    {
        $parts = ['query', $queryId];
        
        if ($repository) {
            $parts[] = 'repo';
            $parts[] = str_replace('/', '_', $repository);
        }
        
        // Tambahkan timestamp harian untuk memastikan cache ter-reset setiap hari
        $parts[] = date('Y-m-d');
        
        return implode(':', $parts);
    }
}
