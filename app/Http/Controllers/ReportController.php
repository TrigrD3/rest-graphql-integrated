<?php

namespace App\Http\Controllers;

use App\Models\RequestLog;
use App\Models\PerformanceMetric;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function summary()
    {
        // Statistik umum
        $metrics = $this->calculateMetrics();
        // Statistik lanjutan
        $advancedStats = $this->calculateAdvancedStats();
        // Data grafik
        $chart_data = $this->prepareChartData();
        // Perbandingan per query
        $queryComparisons = $this->prepareQueryComparisons();
        // Analisis cache
        $cacheAnalysis = $this->analyzeCache();
        // Analisis per repository
        $repoAnalysis = $this->analyzeRepositories();
        // Insight otomatis
        $insight = $this->generateInsight($metrics, $advancedStats, $queryComparisons, $cacheAnalysis, $repoAnalysis);
        // Data untuk export
        $exportData = $this->prepareExportData($metrics, $advancedStats, $queryComparisons, $cacheAnalysis, $repoAnalysis);

        return view('reports.summary', compact(
            'metrics', 'advancedStats', 'chart_data', 'queryComparisons',
            'cacheAnalysis', 'repoAnalysis', 'insight', 'exportData'
        ));
    }
    
    private function calculateMetrics()
    {
        $totalTests = RequestLog::count();
        $successfulTests = RequestLog::where('rest_succeeded', true)
            ->where('graphql_succeeded', true)
            ->count();
            
        $restWins = RequestLog::where('winner_api', 'rest')->count();
        $graphqlWins = RequestLog::where('winner_api', 'graphql')->count();
        
        $cacheHits = RequestLog::where('cache_status', 'HIT')->count();
        $cacheMisses = RequestLog::where('cache_status', 'MISS')->count();
        
        // Menghitung rata-rata penggunaan CPU dan Memory
        $avgMetrics = PerformanceMetric::select(
            DB::raw('AVG(cpu_usage_percent) as avg_cpu'),
            DB::raw('AVG(memory_usage_percent) as avg_memory')
        )->first();
        
        return [
            'total_tests' => $totalTests,
            'success_rate' => $totalTests > 0 ? ($successfulTests / $totalTests) * 100 : 0,
            'rest_wins' => $totalTests > 0 ? ($restWins / $totalTests) * 100 : 0,
            'graphql_wins' => $totalTests > 0 ? ($graphqlWins / $totalTests) * 100 : 0,
            'cache_hit_rate' => $totalTests > 0 ? ($cacheHits / $totalTests) * 100 : 0,
            'cache_miss_rate' => $totalTests > 0 ? ($cacheMisses / $totalTests) * 100 : 0,
            'avg_cpu_usage' => round($avgMetrics->avg_cpu ?? 0, 2),
            'avg_memory_usage' => round($avgMetrics->avg_memory ?? 0, 2)
        ];
    }
    
    private function calculateAdvancedStats()
    {
        // Statistik lanjutan untuk response time, CPU, memory
        $restTimes = RequestLog::whereNotNull('rest_response_time_ms')->pluck('rest_response_time_ms')->toArray();
        $graphqlTimes = RequestLog::whereNotNull('graphql_response_time_ms')->pluck('graphql_response_time_ms')->toArray();
        $cpuUsages = PerformanceMetric::pluck('cpu_usage_percent')->toArray();
        $memUsages = PerformanceMetric::pluck('memory_usage_percent')->toArray();
        return [
            'rest' => $this->getStats($restTimes),
            'graphql' => $this->getStats($graphqlTimes),
            'cpu' => $this->getStats($cpuUsages),
            'memory' => $this->getStats($memUsages)
        ];
    }
    
    private function getStats($arr)
    {
        if (empty($arr)) return null;
        sort($arr);
        $count = count($arr);
        $min = $arr[0];
        $max = $arr[$count-1];
        $mean = array_sum($arr) / $count;
        $median = $arr[(int)floor($count/2)];
        $stddev = $count > 1 ? sqrt(array_sum(array_map(fn($x) => pow($x-$mean,2), $arr)) / ($count-1)) : 0;
        return compact('min','max','mean','median','stddev','count');
    }
    
    private function prepareChartData()
    {
        $queryStats = RequestLog::select(
            'query_id',
            DB::raw('AVG(rest_response_time_ms) as avg_rest_time'),
            DB::raw('AVG(graphql_response_time_ms) as avg_graphql_time')
        )
        ->groupBy('query_id')
        ->orderBy('query_id')
        ->get();
        
        return [
            'labels' => $queryStats->pluck('query_id')->toArray(),
            'rest_times' => $queryStats->pluck('avg_rest_time')->toArray(),
            'graphql_times' => $queryStats->pluck('avg_graphql_time')->toArray()
        ];
    }
    
    private function prepareQueryComparisons()
    {
        $queryDescriptions = [
            'Q1' => 'Mengambil nama dari 100 project teratas berdasarkan jumlah stars',
            'Q2' => 'Mengambil jumlah total pull request dan isi dari 1.000 pull request terbaru',
            'Q3' => 'Mengambil isi dari komentar untuk setiap pull request',
            'Q4' => 'Mengambil nama dan URL dari 5 project teratas berdasarkan jumlah stars',
            'Q5' => 'Mengambil jumlah commit, branch, bug, release, dan kontributor untuk tujuh project',
            'Q6' => 'Mengambil judul dan isi dari bug yang sudah ditutup untuk setiap project',
            'Q7' => 'Mengambil isi dari komentar untuk setiap bug yang sudah ditutup',
            'Q8' => 'Mengambil nama dan URL dari project Java dengan kriteria tertentu',
            'Q9' => 'Mengambil jumlah stars dari project tertentu',
            'Q10' => 'Mengambil nama repository dengan setidaknya 1.000 stars',
            'Q11' => 'Mengambil jumlah commit dalam sebuah repository',
            'Q12' => 'Mengambil jumlah release, stars, dan bahasa pemrograman untuk delapan project',
            'Q13' => 'Mengambil detail dari open issue dengan tag "bug"',
            'Q14' => 'Mengambil isi dari komentar untuk setiap issue'
        ];
        
        $comparisons = RequestLog::select(
            'query_id',
            DB::raw('AVG(rest_response_time_ms) as avg_rest_time'),
            DB::raw('AVG(graphql_response_time_ms) as avg_graphql_time'),
            DB::raw('COUNT(CASE WHEN winner_api = "rest" THEN 1 END) as rest_wins'),
            DB::raw('COUNT(CASE WHEN winner_api = "graphql" THEN 1 END) as graphql_wins')
        )
        ->groupBy('query_id')
        ->get()
        ->map(function ($item) use ($queryDescriptions) {
            $item->description = $queryDescriptions[$item->query_id] ?? 'Deskripsi tidak tersedia';
            $item->winner = $item->rest_wins > $item->graphql_wins ? 'rest' : 'graphql';
            return $item;
        });
        
        return $comparisons;
    }
    
    private function analyzeCache()
    {
        $cacheStats = RequestLog::select(
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN cache_status = "HIT" THEN 1 ELSE 0 END) as hit'),
            DB::raw('SUM(CASE WHEN cache_status = "MISS" THEN 1 ELSE 0 END) as miss')
        )->first();
        $avgTimeWithCache = RequestLog::where('cache_status', 'HIT')->avg('rest_response_time_ms');
        $avgTimeWithoutCache = RequestLog::where('cache_status', 'MISS')->avg('rest_response_time_ms');
        return [
            'total' => $cacheStats->total ?? 0,
            'hit' => $cacheStats->hit ?? 0,
            'miss' => $cacheStats->miss ?? 0,
            'avg_time_with_cache' => $avgTimeWithCache,
            'avg_time_without_cache' => $avgTimeWithoutCache,
            'saving' => $avgTimeWithoutCache && $avgTimeWithCache ? $avgTimeWithoutCache - $avgTimeWithCache : null
        ];
    }
    
    private function analyzeRepositories()
    {
        // Analisis per repository studi kasus
        $repos = PerformanceMetric::whereNotNull('test_type')->where('test_type', 'repository')
            ->select('query_id', 'api_type',
                DB::raw('AVG(cpu_usage_percent) as avg_cpu'),
                DB::raw('AVG(memory_usage_percent) as avg_mem'),
                DB::raw('AVG(avg_response_time_ms) as avg_time'),
                DB::raw('COUNT(*) as total_test')
            )
            ->groupBy('query_id', 'api_type')
            ->get()
            ->groupBy('query_id');
        return $repos;
    }
    
    private function generateInsight($metrics, $advancedStats, $queryComparisons, $cacheAnalysis, $repoAnalysis)
    {
        // Narasi otomatis dengan variasi kalimat dan sinonim
        $total = $metrics['total_tests'];
        $restWin = round($metrics['rest_wins'],1);
        $graphqlWin = round($metrics['graphql_wins'],1);
        $cacheHit = round($metrics['cache_hit_rate'],1);
        $saving = $cacheAnalysis['saving'] ? round($cacheAnalysis['saving'],2) : null;
        $restMean = $advancedStats['rest']['mean'] ?? null;
        $graphqlMean = $advancedStats['graphql']['mean'] ?? null;
        $restStd = $advancedStats['rest']['stddev'] ?? null;
        $graphqlStd = $advancedStats['graphql']['stddev'] ?? null;
        $restMedian = $advancedStats['rest']['median'] ?? null;
        $graphqlMedian = $advancedStats['graphql']['median'] ?? null;

        $narasi = [];
        // Paragraf 1: Ringkasan umum (variasi)
        $opsi1 = "Selama proses pengujian, tercatat sebanyak <b>{$total}</b> pengujian pada 14 skenario query. Tingkat keberhasilan mencapai <b>".number_format($metrics['success_rate'],1)."%</b>.";
        $opsi2 = "Sebanyak <b>{$total}</b> pengujian telah dilakukan pada 14 skenario berbeda, dengan persentase keberhasilan sebesar <b>".number_format($metrics['success_rate'],1)."%</b>.";
        $opsi3 = "Pengujian pada 14 skenario menghasilkan <b>{$total}</b> data, dengan tingkat sukses <b>".number_format($metrics['success_rate'],1)."%</b>.";
        $narasi[] = [$opsi1, $opsi2, $opsi3][rand(0,2)];
        // Paragraf 2: Perbandingan REST vs GraphQL (variasi)
        $opsiA = "REST API mendominasi <b>{$restWin}%</b> pengujian, sedangkan GraphQL unggul pada <b>{$graphqlWin}%</b> kasus. Rata-rata waktu respons REST: <b>".number_format($restMean,2)." ms</b> (median: ".number_format($restMedian,2)." ms), GraphQL: <b>".number_format($graphqlMean,2)." ms</b> (median: ".number_format($graphqlMedian,2)." ms).";
        $opsiB = "REST API memenangkan <b>{$restWin}%</b> skenario, sementara GraphQL unggul di <b>{$graphqlWin}%</b>. Rerata respons REST: <b>".number_format($restMean,2)." ms</b>, GraphQL: <b>".number_format($graphqlMean,2)." ms</b>.";
        $opsiC = "REST API lebih cepat pada <b>{$restWin}%</b> pengujian, GraphQL pada <b>{$graphqlWin}%</b>. REST rata-rata <b>".number_format($restMean,2)." ms</b>, GraphQL <b>".number_format($graphqlMean,2)." ms</b>.";
        $narasi[] = [$opsiA, $opsiB, $opsiC][rand(0,2)];
        // Paragraf 3: Efektivitas cache (variasi)
        $opsiX = "Cache sangat efektif, dengan HIT <b>{$cacheHit}%</b> dan penghematan waktu rata-rata <b>".($saving ? $saving.' ms' : 'N/A')."</b> per request.";
        $opsiY = "Efisiensi cache tercermin dari HIT <b>{$cacheHit}%</b> dan rata-rata penghematan <b>".($saving ? $saving.' ms' : 'N/A')."</b>.";
        $opsiZ = "Cache HIT mencapai <b>{$cacheHit}%</b>, menghemat waktu rata-rata <b>".($saving ? $saving.' ms' : 'N/A')."</b> tiap permintaan.";
        $narasi[] = [$opsiX, $opsiY, $opsiZ][rand(0,2)];
        // Paragraf 4: Highlight insight per query
        $restWinners = [];
        $graphqlWinners = [];
        foreach ($queryComparisons as $qc) {
            if ($qc->winner === 'rest') $restWinners[] = $qc->query_id;
            else $graphqlWinners[] = $qc->query_id;
        }
        $narasi[] = "REST API unggul pada skenario: <b>".implode(', ', $restWinners)."</b>. GraphQL unggul pada skenario: <b>".implode(', ', $graphqlWinners)."</b>.";
        // Paragraf 5: Anomali/outlier (variasi)
        if ($restStd && $restMean && $restStd > 2 * $restMean) {
            $opsiAnom1 = "Terdapat anomali pada response time REST (standar deviasi tinggi). Disarankan meninjau data mentah lebih lanjut.";
            $opsiAnom2 = "Ditemukan outlier pada response time REST (stddev besar). Perlu investigasi lebih lanjut.";
            $narasi[] = [$opsiAnom1, $opsiAnom2][rand(0,1)];
        }
        if ($graphqlStd && $graphqlMean && $graphqlStd > 2 * $graphqlMean) {
            $opsiAnom1 = "Terdapat anomali pada response time GraphQL (standar deviasi tinggi). Disarankan meninjau data mentah lebih lanjut.";
            $opsiAnom2 = "Ditemukan outlier pada response time GraphQL (stddev besar). Perlu investigasi lebih lanjut.";
            $narasi[] = [$opsiAnom1, $opsiAnom2][rand(0,1)];
        }
        // Paragraf 6: Rekomendasi (variasi)
        if ($restWin > $graphqlWin) {
            $opsiRec1 = "<b>Rekomendasi:</b> Gunakan REST API untuk query sederhana dan endpoint yang sering di-cache. GraphQL cocok untuk kebutuhan data kompleks.";
            $opsiRec2 = "<b>Saran:</b> REST API direkomendasikan untuk operasi ringan, GraphQL untuk query fleksibel dan kompleks.";
            $narasi[] = [$opsiRec1, $opsiRec2][rand(0,1)];
        } else {
            $opsiRec1 = "<b>Rekomendasi:</b> Gunakan GraphQL untuk query kompleks, namun optimalkan cache untuk performa terbaik.";
            $opsiRec2 = "<b>Saran:</b> GraphQL unggul untuk kebutuhan data spesifik, REST tetap efisien untuk permintaan sederhana.";
            $narasi[] = [$opsiRec1, $opsiRec2][rand(0,1)];
        }
        // Paragraf 7: Catatan analis (variasi)
        $opsiNote1 = "<i>Catatan analis:</i> Laporan ini dihasilkan otomatis dari data pengujian aktual. Untuk analisis lebih mendalam, gunakan fitur export data mentah.";
        $opsiNote2 = "<i>Catatan:</i> Kesimpulan ini bersifat dinamis dan dapat berubah sesuai data terbaru. Silakan cek data mentah untuk validasi lebih lanjut.";
        $narasi[] = [$opsiNote1, $opsiNote2][rand(0,1)];
        return $narasi;
    }
    
    private function prepareExportData($metrics, $advancedStats, $queryComparisons, $cacheAnalysis, $repoAnalysis)
    {
        // Data summary dan mentah untuk export
        return [
            'summary' => $metrics,
            'advanced' => $advancedStats,
            'query' => $queryComparisons,
            'cache' => $cacheAnalysis,
            'repo' => $repoAnalysis
        ];
    }
} 