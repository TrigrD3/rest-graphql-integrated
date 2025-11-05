<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\PerformanceMetric;
use App\Services\ApiGatewayService;
use App\Services\SystemMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class RepositoryTestController extends Controller
{
    protected $apiGatewayService;
    protected $systemMetricsService;
    
    public function __construct(ApiGatewayService $apiGatewayService, SystemMetricsService $systemMetricsService)
    {
        $this->apiGatewayService = $apiGatewayService;
        $this->systemMetricsService = $systemMetricsService;
    }
    
    /**
     * Menampilkan halaman pengujian repository
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $repositories = $this->getRepositories();
        $testResults = PerformanceMetric::where('test_type', 'repository')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('repositories', compact('repositories', 'testResults'));
    }
    
    /**
     * Menjalankan pengujian pada repository tertentu
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function runRepositoryTest(Request $request)
    {
        try {
            $request->validate([
                'repository_id' => 'required|string',
                'api_type' => 'required|in:rest,graphql,integrated',
                'request_count' => 'integer|min:1|max:100'
            ]);
            
            $repositoryId = $request->input('repository_id');
            $apiType = $request->input('api_type');
            $requestCount = $request->input('request_count', 10);
            
            // Dapatkan informasi repository
            $repository = $this->getRepositoryInfo($repositoryId);
            
            if (!$repository) {
                return response()->json([
                    'error' => true,
                    'message' => 'Repository tidak ditemukan'
                ], 404);
            }
            
            Log::info('Menjalankan pengujian repository', [
                'repository' => $repository['name'],
                'api_type' => $apiType,
                'request_count' => $requestCount
            ]);
            
            // Mulai pengukuran CPU dan memory
            $startCpuUsage = $this->systemMetricsService->getCpuUsage();
            $startMemoryUsage = $this->systemMetricsService->getMemoryUsage();
            
            // Jalankan pengujian
            $startTime = microtime(true);
            $responseTimes = [];
            $responses = [];
            
            for ($i = 0; $i < $requestCount; $i++) {
                $requestStartTime = microtime(true);
                
                // Lakukan request ke API berdasarkan tipe dan repository
                $response = $this->executeRepositoryRequest($repositoryId, $apiType);
                $responses[] = $response;
                
                $requestEndTime = microtime(true);
                $responseTimes[] = ($requestEndTime - $requestStartTime) * 1000; // Konversi ke ms
            }
            
            $endTime = microtime(true);
            
            // Akhiri pengukuran CPU dan memory
            $endCpuUsage = $this->systemMetricsService->getCpuUsage();
            $endMemoryUsage = $this->systemMetricsService->getMemoryUsage();
            
            // Hitung rata-rata waktu respons
            $avgResponseTime = array_sum($responseTimes) / count($responseTimes);
            
            // Hitung penggunaan CPU dan memory
            $cpuUsage = ($endCpuUsage + $startCpuUsage) / 2;
            $memoryUsage = ($endMemoryUsage + $startMemoryUsage) / 2;
            
            // Simpan hasil pengukuran
            $metric = PerformanceMetric::create([
                'query_id' => $repositoryId,
                'api_type' => $apiType,
                'cpu_usage_percent' => $cpuUsage,
                'memory_usage_percent' => $memoryUsage,
                'request_count' => $requestCount,
                'avg_response_time_ms' => $avgResponseTime,
                'test_type' => 'repository'
            ]);
            
            // Catat log permintaan terakhir
            if (!empty($responses)) {
                $lastResponse = end($responses);
                
                // Simpan log untuk permintaan terakhir
                $this->logRepositoryRequest($repositoryId, $apiType, $lastResponse);
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'repository' => $repository,
                    'api_type' => $apiType,
                    'request_count' => $requestCount,
                    'avg_response_time_ms' => $avgResponseTime,
                    'cpu_usage_percent' => $cpuUsage,
                    'memory_usage_percent' => $memoryUsage,
                    'total_time_ms' => ($endTime - $startTime) * 1000,
                    'last_response' => !empty($responses) ? end($responses) : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in repository test: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Mendapatkan daftar repository studi kasus
     * 
     * @return array
     */
    private function getRepositories()
    {
        return [
            'viz' => [
                'id' => 'viz',
                'name' => 'donnemartin/viz',
                'description' => 'Visualisasi repositori GitHub',
                'github_url' => 'https://github.com/donnemartin/viz',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/donnemartin/viz',
                    'graphql' => 'repository(owner: "donnemartin", name: "viz")'
                ]
            ],
            'gitsome' => [
                'id' => 'gitsome',
                'name' => 'donnemartin/gitsome',
                'description' => 'Command line interface untuk GitHub',
                'github_url' => 'https://github.com/donnemartin/gitsome',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/donnemartin/gitsome',
                    'graphql' => 'repository(owner: "donnemartin", name: "gitsome")'
                ]
            ],
            'gitsuggest' => [
                'id' => 'gitsuggest',
                'name' => 'csurfer/gitsuggest',
                'description' => 'Alat untuk menyarankan repositori GitHub',
                'github_url' => 'https://github.com/csurfer/gitsuggest',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/csurfer/gitsuggest',
                    'graphql' => 'repository(owner: "csurfer", name: "gitsuggest")'
                ]
            ],
            'git-repo' => [
                'id' => 'git-repo',
                'name' => 'guyzmo/git-repo',
                'description' => 'Command line interface untuk mengelola layanan Git',
                'github_url' => 'https://github.com/guyzmo/git-repo',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/guyzmo/git-repo',
                    'graphql' => 'repository(owner: "guyzmo", name: "git-repo")'
                ]
            ],
            'github-awards' => [
                'id' => 'github-awards',
                'name' => 'vdaubry/github-awards',
                'description' => 'Peringkat repositori GitHub',
                'github_url' => 'https://github.com/vdaubry/github-awards',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/vdaubry/github-awards',
                    'graphql' => 'repository(owner: "vdaubry", name: "github-awards")'
                ]
            ],
            'arxivcheck' => [
                'id' => 'arxivcheck',
                'name' => 'bibcure/arxivcheck',
                'description' => 'Alat untuk menghasilkan BIBTEX dari arXiv preprints',
                'github_url' => 'https://github.com/bibcure/arxivcheck',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/bibcure/arxivcheck',
                    'graphql' => 'repository(owner: "bibcure", name: "arxivcheck")'
                ]
            ],
            'arxiv-sanity-preserver' => [
                'id' => 'arxiv-sanity-preserver',
                'name' => 'karpathy/arxiv-sanity-preserver',
                'description' => 'Web interface untuk mencari submission arXiv',
                'github_url' => 'https://github.com/karpathy/arxiv-sanity-preserver',
                'api_endpoints' => [
                    'rest' => 'https://api.github.com/repos/karpathy/arxiv-sanity-preserver',
                    'graphql' => 'repository(owner: "karpathy", name: "arxiv-sanity-preserver")'
                ]
            ]
        ];
    }
    
    /**
     * Mendapatkan informasi repository berdasarkan ID
     * 
     * @param string $repositoryId
     * @return array|null
     */
    private function getRepositoryInfo($repositoryId)
    {
        $repositories = $this->getRepositories();
        return $repositories[$repositoryId] ?? null;
    }
    
    /**
     * Eksekusi request ke API untuk repository tertentu
     * 
     * @param string $repositoryId
     * @param string $apiType
     * @return array
     */
    private function executeRepositoryRequest($repositoryId, $apiType)
    {
        $repository = $this->getRepositoryInfo($repositoryId);
        
        if (!$repository) {
            throw new \Exception("Repository tidak valid: $repositoryId");
        }
        
        switch ($apiType) {
            case 'rest':
                return $this->executeRestRequest($repository);
            case 'graphql':
                return $this->executeGraphqlRequest($repository);
            case 'integrated':
                // Untuk API terintegrasi, jalankan keduanya dan gabungkan hasilnya
                $restResult = $this->executeRestRequest($repository);
                $graphqlResult = $this->executeGraphqlRequest($repository);
                
                return [
                    'rest' => $restResult,
                    'graphql' => $graphqlResult,
                    'response_time_ms' => ($restResult['response_time_ms'] + $graphqlResult['response_time_ms']) / 2,
                    'succeeded' => $restResult['succeeded'] && $graphqlResult['succeeded']
                ];
            default:
                throw new \Exception("Tipe API tidak valid: $apiType");
        }
    }
    
    /**
     * Eksekusi request ke REST API
     * 
     * @param array $repository
     * @return array
     */
    private function executeRestRequest($repository)
    {
        $url = $repository['api_endpoints']['rest'];
        $startTime = microtime(true);
        $response = null;
        $error = null;
        $succeeded = false;
        
        try {
            $httpResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GITHUB_TOKEN'),
                'Accept' => 'application/vnd.github.v3+json'
            ])->get($url);
            
            $response = $httpResponse->json();
            $succeeded = $httpResponse->successful();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("REST API Error: {$e->getMessage()}", ['repository' => $repository['name']]);
        }
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // konversi ke ms
        
        return [
            'response' => $response,
            'error' => $error,
            'response_time_ms' => $responseTime,
            'succeeded' => $succeeded
        ];
    }
    
    /**
     * Eksekusi request ke GraphQL API
     * 
     * @param array $repository
     * @return array
     */
    private function executeGraphqlRequest($repository)
    {
        $graphqlEndpoint = config('api.graphql_base_url');
        $query = "query { " . $repository['api_endpoints']['graphql'] . " { name url description stargazerCount forkCount } }";
        
        $startTime = microtime(true);
        $response = null;
        $error = null;
        $succeeded = false;
        
        try {
            $httpResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . env('GITHUB_TOKEN'),
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post($graphqlEndpoint, [
                'query' => $query
            ]);
            
            $responseData = $httpResponse->json();
            $response = $responseData;
            $succeeded = !isset($responseData['errors']) && $httpResponse->successful();
        } catch (\Exception $e) {
            $error = $e->getMessage();
            Log::error("GraphQL API Error: {$e->getMessage()}", ['repository' => $repository['name']]);
        }
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // konversi ke ms
        
        return [
            'response' => $response,
            'error' => $error,
            'response_time_ms' => $responseTime,
            'succeeded' => $succeeded
        ];
    }
    
    /**
     * Mencatat log permintaan repository
     * 
     * @param string $repositoryId
     * @param string $apiType
     * @param array $response
     * @return void
     */
    private function logRepositoryRequest($repositoryId, $apiType, $response)
    {
        try {
            $repository = $this->getRepositoryInfo($repositoryId);
            
            // Ekstrak data berdasarkan tipe API
            if ($apiType === 'integrated') {
                $restResponse = $response['rest'] ?? [];
                $graphqlResponse = $response['graphql'] ?? [];
                
                $restResponseTime = $restResponse['response_time_ms'] ?? null;
                $graphqlResponseTime = $graphqlResponse['response_time_ms'] ?? null;
                $restSucceeded = $restResponse['succeeded'] ?? false;
                $graphqlSucceeded = $graphqlResponse['succeeded'] ?? false;
                
                // Tentukan pemenang
                $winnerApi = 'none';
                if ($restSucceeded && $graphqlSucceeded) {
                    $winnerApi = ($restResponseTime < $graphqlResponseTime) ? 'rest' : 'graphql';
                } elseif ($restSucceeded) {
                    $winnerApi = 'rest';
                } elseif ($graphqlSucceeded) {
                    $winnerApi = 'graphql';
                }
                
                RequestLog::create([
                    'query_id' => $repositoryId,
                    'endpoint' => 'Repository Test: ' . $repository['name'],
                    'cache_status' => 'MISS', // Asumsikan tidak ada cache untuk pengujian repository
                    'winner_api' => $winnerApi,
                    'rest_response_time_ms' => $restResponseTime,
                    'graphql_response_time_ms' => $graphqlResponseTime,
                    'rest_succeeded' => $restSucceeded,
                    'graphql_succeeded' => $graphqlSucceeded,
                    'response_body' => json_encode([
                        'rest' => $restResponse['response'] ?? null,
                        'graphql' => $graphqlResponse['response'] ?? null
                    ])
                ]);
            } else {
                // Untuk REST atau GraphQL saja
                $responseTime = $response['response_time_ms'] ?? null;
                $succeeded = $response['succeeded'] ?? false;
                
                RequestLog::create([
                    'query_id' => $repositoryId,
                    'endpoint' => 'Repository Test: ' . $repository['name'] . ' (' . strtoupper($apiType) . ')',
                    'cache_status' => 'MISS', // Asumsikan tidak ada cache untuk pengujian repository
                    'winner_api' => $succeeded ? $apiType : 'none',
                    'rest_response_time_ms' => $apiType === 'rest' ? $responseTime : null,
                    'graphql_response_time_ms' => $apiType === 'graphql' ? $responseTime : null,
                    'rest_succeeded' => $apiType === 'rest' ? $succeeded : false,
                    'graphql_succeeded' => $apiType === 'graphql' ? $succeeded : false,
                    'response_body' => json_encode($response['response'] ?? null)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error logging repository request: ' . $e->getMessage());
        }
    }
} 