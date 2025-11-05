<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\PerformanceMetric;
use App\Services\ApiGatewayService;
use App\Services\SystemMetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller
{
    protected $apiGatewayService;
    protected $systemMetricsService;
    
    public function __construct(ApiGatewayService $apiGatewayService, SystemMetricsService $systemMetricsService)
    {
        $this->apiGatewayService = $apiGatewayService;
        $this->systemMetricsService = $systemMetricsService;
    }
    
    /**
     * Endpoint untuk pengujian API dari JMeter
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function test(Request $request)
    {
        try {
            $request->validate([
                'query_id' => 'required|string',
                'api_type' => 'required|in:rest,graphql,integrated',
                'request_count' => 'integer|min:1|max:1000',
                'jmeter_test_id' => 'string|nullable'
            ]);
            
            $queryId = $request->input('query_id');
            $apiType = $request->input('api_type');
            $requestCount = $request->input('request_count', 1);
            $jmeterTestId = $request->input('jmeter_test_id');
            
            Log::info('JMeter API Test Request', [
                'query_id' => $queryId,
                'api_type' => $apiType,
                'request_count' => $requestCount,
                'jmeter_test_id' => $jmeterTestId
            ]);
            
            // Mulai pengukuran resource khusus proses aplikasi
            $usageSnapshot = $this->systemMetricsService->beginApplicationUsageSampling();
            $usageMetrics = null;
            
            // Jalankan pengujian
            $startTime = microtime(true);
            $responseTimes = [];
            $responses = [];
            
            for ($i = 0; $i < $requestCount; $i++) {
                $requestStartTime = microtime(true);
                
                // Lakukan request ke API berdasarkan tipe
                $response = $this->executeApiRequest($queryId, $apiType);
                $responses[] = $response;
                
                $requestEndTime = microtime(true);
                $responseTimes[] = ($requestEndTime - $requestStartTime) * 1000; // Konversi ke ms
            }
            
            $endTime = microtime(true);
            
            if ($usageSnapshot !== null) {
                $usageMetrics = $this->systemMetricsService->finishApplicationUsageSampling($usageSnapshot);
                $usageSnapshot = null;
            }
            
            // Hitung rata-rata waktu respons
            $avgResponseTime = array_sum($responseTimes) / count($responseTimes);
            
            // Hitung penggunaan CPU dan memory (khusus aplikasi)
            $cpuUsage = $usageMetrics['cpu_percent'] ?? 0;
            $memoryUsage = $usageMetrics['memory_percent'] ?? 0;
            
            // Simpan hasil pengukuran jika ini adalah bagian dari pengujian JMeter
            if ($jmeterTestId) {
                $metric = PerformanceMetric::create([
                    'query_id' => $queryId,
                    'api_type' => $apiType,
                    'cpu_usage_percent' => $cpuUsage,
                    'memory_usage_percent' => $memoryUsage,
                    'request_count' => $requestCount,
                    'avg_response_time_ms' => $avgResponseTime
                ]);
                
                // Catat log permintaan terakhir
                if (!empty($responses)) {
                    $lastResponse = end($responses);
                    
                    // Simpan log untuk permintaan terakhir
                    $this->logApiRequest($queryId, $apiType, $lastResponse, $jmeterTestId);
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'query_id' => $queryId,
                    'api_type' => $apiType,
                    'jmeter_test_id' => $jmeterTestId,
                    'request_count' => $requestCount,
                    'avg_response_time_ms' => $avgResponseTime,
                    'cpu_usage_percent' => $cpuUsage,
                    'memory_usage_percent' => $memoryUsage,
                    'total_time_ms' => ($endTime - $startTime) * 1000,
                    'cpu_time_ms' => isset($usageMetrics['cpu_time_seconds']) ? round($usageMetrics['cpu_time_seconds'] * 1000, 2) : null,
                    'memory_usage_mb' => isset($usageMetrics['memory_megabytes']) ? round($usageMetrics['memory_megabytes'], 2) : null,
                    'memory_usage_delta_mb' => isset($usageMetrics['memory_delta_megabytes']) ? round($usageMetrics['memory_delta_megabytes'], 2) : null,
                    'resource_usage' => $usageMetrics,
                    'last_response' => !empty($responses) ? end($responses) : null
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Exception in JMeter API test: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Eksekusi request ke API
     * 
     * @param string $queryId
     * @param string $apiType
     * @return array
     */
    private function executeApiRequest($queryId, $apiType)
    {
        switch ($apiType) {
            case 'rest':
                return $this->apiGatewayService->executeRestApi($queryId, null, false);
            case 'graphql':
                return $this->apiGatewayService->executeGraphqlApi($queryId, null, false);
            case 'integrated':
                // Untuk API terintegrasi, jalankan keduanya dan gabungkan hasilnya
                $restResult = $this->apiGatewayService->executeRestApi($queryId, null, true);
                $graphqlResult = $this->apiGatewayService->executeGraphqlApi($queryId, null, true);
                
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
     * Mencatat log permintaan API
     * 
     * @param string $queryId
     * @param string $apiType
     * @param array $response
     * @param string $jmeterTestId
     * @return void
     */
    private function logApiRequest($queryId, $apiType, $response, $jmeterTestId)
    {
        try {
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
                    'query_id' => $queryId,
                    'endpoint' => 'JMeter Test: ' . $jmeterTestId,
                    'cache_status' => 'MISS', // Asumsikan tidak ada cache untuk pengujian JMeter
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
                    'query_id' => $queryId,
                    'endpoint' => 'JMeter Test: ' . $jmeterTestId . ' (' . strtoupper($apiType) . ')',
                    'cache_status' => 'MISS', // Asumsikan tidak ada cache untuk pengujian JMeter
                    'winner_api' => $succeeded ? $apiType : 'none',
                    'rest_response_time_ms' => $apiType === 'rest' ? $responseTime : null,
                    'graphql_response_time_ms' => $apiType === 'graphql' ? $responseTime : null,
                    'rest_succeeded' => $apiType === 'rest' ? $succeeded : false,
                    'graphql_succeeded' => $apiType === 'graphql' ? $succeeded : false,
                    'response_body' => json_encode($response['response'] ?? null)
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error logging API request: ' . $e->getMessage());
        }
    }
} 
