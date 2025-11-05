<?php

namespace App\Http\Controllers;

use App\Models\PerformanceMetric;
use App\Services\SystemMetricsService;
use App\Services\ApiGatewayService;
use Illuminate\Http\Request;

class PerformanceMetricController extends Controller
{
    protected $systemMetricsService;
    protected $apiGatewayService;
    
    public function __construct(SystemMetricsService $systemMetricsService, ApiGatewayService $apiGatewayService)
    {
        $this->systemMetricsService = $systemMetricsService;
        $this->apiGatewayService = $apiGatewayService;
    }
    
    /**
     * Menampilkan halaman dashboard metrik performa
     */
    public function index()
    {
        $metrics = PerformanceMetric::orderBy('created_at', 'desc')
            ->paginate(20);
            
        $queries = $this->apiGatewayService->getAvailableQueries();
            
        return view('metrics', compact('metrics', 'queries'));
    }
    
    /**
     * Menjalankan pengujian performa untuk query tertentu
     */
    public function runPerformanceTest(Request $request)
    {
        $request->validate([
            'query_id' => 'required|string',
            'api_type' => 'required|in:rest,graphql,integrated',
            'request_count' => 'required|integer|min:1|max:1000'
        ]);
        
        $queryId = $request->input('query_id');
        $apiType = $request->input('api_type');
        $requestCount = $request->input('request_count');
        
        // Mulai pengukuran CPU dan memory
        $startCpuUsage = $this->systemMetricsService->getCpuUsage();
        $startMemoryUsage = $this->systemMetricsService->getMemoryUsage();
        
        // Jalankan pengujian
        $startTime = microtime(true);
        $responseTimes = [];
        
        for ($i = 0; $i < $requestCount; $i++) {
            $requestStartTime = microtime(true);
            
            // Lakukan request ke API
            $response = $this->executeApiRequest($queryId, $apiType);
            
            $requestEndTime = microtime(true);
            $responseTimes[] = ($requestEndTime - $requestStartTime) * 1000; // Konversi ke ms
        }
        
        $endTime = microtime(true);
        
        // Akhiri pengukuran CPU dan memory
        $endCpuUsage = $this->systemMetricsService->getCpuUsage();
        $endMemoryUsage = $this->systemMetricsService->getMemoryUsage();
        
        // Hitung rata-rata waktu respons menggunakan rumus (1)
        $avgResponseTime = array_sum($responseTimes) / count($responseTimes);
        
        // Hitung penggunaan CPU dan memory
        $cpuUsage = ($endCpuUsage + $startCpuUsage) / 2;
        $memoryUsage = ($endMemoryUsage + $startMemoryUsage) / 2;
        
        // Simpan hasil pengukuran
        $metric = PerformanceMetric::create([
            'query_id' => $queryId,
            'api_type' => $apiType,
            'cpu_usage_percent' => $cpuUsage,
            'memory_usage_percent' => $memoryUsage,
            'request_count' => $requestCount,
            'avg_response_time_ms' => $avgResponseTime
        ]);
        
        // Pastikan data metrik memiliki semua properti yang diperlukan untuk tampilan
        $metricData = $metric->toArray();
        
        return response()->json([
            'success' => true,
            'data' => array_merge($metricData, [
                'details' => [
                    'total_time' => ($endTime - $startTime) * 1000,
                    'response_times' => $responseTimes
                ]
            ])
        ]);
    }
    
    /**
     * Mendapatkan data metrik untuk visualisasi
     */
    public function getMetricsData(Request $request)
    {
        $queryId = $request->input('query_id');
        
        $metrics = PerformanceMetric::where('query_id', $queryId)
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('api_type');
            
        $result = [
            'labels' => ['REST', 'GraphQL', 'Integrated'],
            'response_time' => [
                $metrics->get('rest', collect())->avg('avg_response_time_ms') ?? 0,
                $metrics->get('graphql', collect())->avg('avg_response_time_ms') ?? 0,
                $metrics->get('integrated', collect())->avg('avg_response_time_ms') ?? 0
            ],
            'cpu_usage' => [
                $metrics->get('rest', collect())->avg('cpu_usage_percent') ?? 0,
                $metrics->get('graphql', collect())->avg('cpu_usage_percent') ?? 0,
                $metrics->get('integrated', collect())->avg('cpu_usage_percent') ?? 0
            ],
            'memory_usage' => [
                $metrics->get('rest', collect())->avg('memory_usage_percent') ?? 0,
                $metrics->get('graphql', collect())->avg('memory_usage_percent') ?? 0,
                $metrics->get('integrated', collect())->avg('memory_usage_percent') ?? 0
            ]
        ];
        
        return response()->json($result);
    }
    
    /**
     * Eksekusi request ke API
     */
    private function executeApiRequest($queryId, $apiType)
    {
        switch ($apiType) {
            case 'rest':
                $result = $this->apiGatewayService->executeRestApi($queryId, null, false);
                return [
                    'response_time_ms' => $result['response_time_ms'],
                    'succeeded' => $result['succeeded']
                ];
            case 'graphql':
                $result = $this->apiGatewayService->executeGraphqlApi($queryId, null, false);
                return [
                    'response_time_ms' => $result['response_time_ms'],
                    'succeeded' => $result['succeeded']
                ];
            case 'integrated':
                // Gunakan integrated API dengan caching cerdas
                $result = $this->apiGatewayService->executeIntegratedApi($queryId);
                return [
                    'response_time_ms' => $result['total_response_time_ms'] ?? 
                                         max($result['rest_response_time_ms'], $result['graphql_response_time_ms']),
                    'succeeded' => $result['rest_succeeded'] || $result['graphql_succeeded'],
                    'cache_used' => $result['cache_used'] ?? false,
                    'selected_api' => $result['selected_api'] ?? 'both'
                ];
            default:
                throw new \Exception("Tipe API tidak valid: $apiType");
        }
    }
} 
