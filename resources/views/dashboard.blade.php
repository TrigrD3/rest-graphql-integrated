@extends('layouts.app')

@section('title', 'Dashboard')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">Dashboard API Gateway Testing</h1>
    <p class="text-gray-600">Bandingkan performa API REST dan GraphQL secara real-time dengan berbagai skenario query.</p>
</div>

<!-- Modal Hasil Pengujian -->
<div id="resultModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 999;">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-900" id="resultTitle">Hasil Pengujian</h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mt-4">
            <!-- Informasi Dasar -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Informasi Query</h4>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">ID Query:</span> <span id="queryId" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Repository:</span> <span id="repository" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Cache Status:</span> <span id="cacheStatus" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Waktu Proses:</span> <span id="processingTime" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Kompleksitas:</span> <span id="complexity" class="font-medium px-2 py-1 rounded text-xs"></span></p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Hasil Perbandingan</h4>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">API Pemenang:</span> <span id="winner" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Selisih Waktu:</span> <span id="timeDiff" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Status:</span> <span id="status" class="font-medium"></span></p>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-gray-700 mb-2">Metrik Performa</h4>
                    <div class="space-y-2">
                        <p><span class="text-gray-600">CPU Usage:</span> <span id="cpuUsage" class="font-medium"></span></p>
                        <p><span class="text-gray-600">Memory Usage:</span> <span id="memoryUsage" class="font-medium"></span></p>
                    </div>
                </div>
            </div>
            
            <!-- Detail Waktu Respons -->
            <div class="mb-6">
                <h4 class="font-semibold text-gray-700 mb-4">Detail Waktu Respons</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-blue-700 font-medium">REST API</span>
                            <span id="restTime" class="text-blue-800 font-bold"></span>
                        </div>
                        <div class="w-full bg-blue-200 rounded-full h-2.5">
                            <div id="restBar" class="bg-blue-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-green-700 font-medium">GraphQL API</span>
                            <span id="graphqlTime" class="text-green-800 font-bold"></span>
                        </div>
                        <div class="w-full bg-green-200 rounded-full h-2.5">
                            <div id="graphqlBar" class="bg-green-600 h-2.5 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Response Data -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Response Data REST API</h4>
                    <div id="restErrorMsg" class="text-red-600 text-xs mb-2 hidden"></div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre id="responseDataRest" class="language-json overflow-x-auto text-sm"></pre>
                    </div>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-700 mb-2">Response Data GraphQL API</h4>
                    <div id="graphqlErrorMsg" class="text-red-600 text-xs mb-2 hidden"></div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <pre id="responseDataGraphql" class="language-json overflow-x-auto text-sm"></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal Detail Pengujian -->
<div id="testDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 999;">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-900" id="testDetailsTitle">Detail Pengujian</h3>
            <button onclick="closeTestDetailsModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mt-4">
            <!-- Statistik Pengujian -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Total Pengujian</h4>
                    <p class="text-2xl font-bold text-blue-900" id="totalTests">-</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">Success Rate</h4>
                    <p class="text-2xl font-bold text-green-900" id="successRate">-</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-800 mb-2">Cache Hits</h4>
                    <p class="text-2xl font-bold text-purple-900" id="cacheHits">-</p>
                </div>
            </div>
            
            <!-- Detail Tabel -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">REST (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GraphQL (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPU (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memory (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cache</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemenang</th>
                        </tr>
                    </thead>
                    <tbody id="testDetailsTable" class="bg-white divide-y divide-gray-200">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeTestDetailsModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6 col-span-1 lg:col-span-2">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-tachometer-alt text-primary-600 mr-2"></i>
            Uji Performa API
        </h2>
        
        <form action="/test" method="POST" class="space-y-4">
            @csrf
            <div>
                <label for="query_id" class="block text-sm font-medium text-gray-700 mb-1">Pilih Skenario Query:</label>
                <select id="query_id" name="query_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                    <option value="Q1">Q1: Mengambil nama dari 100 project teratas berdasarkan jumlah stars</option>
                    <option value="Q2">Q2: Mengambil jumlah total pull request dan isi dari 1.000 pull request terbaru</option>
                    <option value="Q3">Q3: Mengambil isi komentar untuk setiap pull request</option>
                    <option value="Q4">Q4: Mengambil nama dan URL dari 5 project teratas</option>
                    <option value="Q5">Q5: Mengambil jumlah commit, branch, bug, release, dan kontributor</option>
                    <option value="Q6">Q6: Mengambil judul dan isi dari bug yang sudah ditutup</option>
                    <option value="Q7">Q7: Mengambil isi komentar untuk setiap bug yang ditutup</option>
                    <option value="Q8">Q8: Mengambil nama dan URL dari project Java tertentu</option>
                    <option value="Q9">Q9: Mengambil jumlah stars dari project tertentu</option>
                    <option value="Q10">Q10: Mengambil nama repository dengan 1.000+ stars</option>
                    <option value="Q11">Q11: Mengambil jumlah commit dalam sebuah repository</option>
                    <option value="Q12">Q12: Mengambil jumlah release, stars, dan bahasa pemrograman</option>
                    <option value="Q13">Q13: Mengambil judul, isi, tanggal dari open issue dengan tag "bug"</option>
                    <option value="Q14">Q14: Mengambil isi komentar untuk setiap issue</option>
                    <option value="Q15">Q15: Skenario Lainnya (isi sendiri)</option>
                </select>
                <div id="queryDescription" class="mt-2 text-sm text-gray-600 bg-gray-50 rounded p-2 border border-gray-200"></div>
                <div id="customDescriptionWrapper" class="mt-2 hidden">
                    <label for="customDescription" class="block text-xs font-medium text-gray-700 mb-1">Deskripsi Skenario Lainnya (Opsional):</label>
                    <textarea id="customDescription" name="custom_description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50" placeholder="Masukkan deskripsi skenario custom..."></textarea>
                </div>
            </div>
            
            <input type="hidden" id="cache" name="cache" value="1">
            
            <!-- Performance Testing Section -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mt-4">
                <h4 class="font-semibold text-blue-800 mb-3 flex items-center">
                    <i class="fas fa-chart-line mr-2"></i>
                    Uji Performa Skenario API
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="request_count" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Request:</label>
                        <input type="number" id="request_count" name="request_count" min="1" max="1000" value="10" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    </div>
                    <div class="flex items-end">
                        <button type="button" id="runPerformanceTestBtn" class="w-full px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-300 flex items-center justify-center">
                            <i class="fas fa-rocket mr-2"></i>
                            <span id="performanceTestBtnText">Uji Performa</span>
                            <svg id="performanceLoadingIcon" class="hidden animate-spin ml-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <p class="mt-3 text-xs text-blue-700 bg-blue-100 border border-blue-200 rounded p-3">
                    Pengujian ini akan menjalankan REST, GraphQL, dan Integrated API secara berurutan agar metrik CPU, memori, dan waktu tetap akurat.
                </p>
            </div>
            
            <!-- Section untuk menampilkan detail Query -->
        </form>
    </div>
    
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-4 gap-4">
        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
            <i class="fas fa-chart-bar text-primary-600 mr-2"></i>
            Ringkasan Performa API
        </h2>
        <div class="flex items-center gap-2">
            <select data-chart-filter class="rounded-md border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50">
                <option value="">Semua Query</option>
                @foreach($chartQueryOptions as $optionQueryId)
                    <option value="{{ $optionQueryId }}" {{ ($chartQueryId ?? null) === $optionQueryId ? 'selected' : '' }}>
                        {{ $optionQueryId }}
                    </option>
                @endforeach
            </select>
            <button type="button" data-chart-filter-button class="px-3 py-2 text-sm bg-primary-600 text-white rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                Terapkan
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <div class="flex items-center mb-3">
                <i class="fas fa-tachometer-alt text-blue-500 mr-2"></i>
                <h3 class="text-md font-semibold text-gray-800">Response Time (ms)</h3>
            </div>
            <div class="h-64">
                <canvas id="chartResponseTime"></canvas>
            </div>
        </div>

        <div>
            <div class="flex items-center mb-3">
                <i class="fas fa-microchip text-green-500 mr-2"></i>
                <h3 class="text-md font-semibold text-gray-800">CPU Usage (%)</h3>
            </div>
            <div class="h-64">
                <canvas id="chartCpuUsage"></canvas>
            </div>
        </div>

        <div>
            <div class="flex items-center mb-3">
                <i class="fas fa-memory text-purple-500 mr-2"></i>
                <h3 class="text-md font-semibold text-gray-800">Memory Usage (%)</h3>
            </div>
            <div class="h-64">
                <canvas id="chartMemoryUsage"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-history text-primary-600 mr-2"></i>
        Riwayat Pengujian Terbaru
    </h2>

    @php
        $cacheBadgeClasses = [
            'HIT' => 'bg-purple-100 text-purple-800',
            'CACHE_USED' => 'bg-purple-100 text-purple-800',
            'MISS' => 'bg-gray-100 text-gray-800',
            '-' => 'bg-gray-100 text-gray-500',
        ];
        $defaultCacheBadgeClass = 'bg-gray-100 text-gray-600';
        $winnerBadgeClasses = [
            'rest' => 'bg-blue-100 text-blue-800',
            'graphql' => 'bg-green-100 text-green-800',
            'integrated' => 'bg-purple-100 text-purple-800',
        ];
        $defaultWinnerBadgeClass = 'bg-gray-100 text-gray-700';
        $renderSummaryCell = function ($summary, $apiType = null) use ($cacheBadgeClasses, $defaultCacheBadgeClass, $winnerBadgeClasses, $defaultWinnerBadgeClass) {
            if (!$summary) {
                return '<span class="text-xs text-gray-400">-</span>';
            }

            $avgResponse = $summary['avg_response_time_ms'] ?? null;
            $avgCpu = $summary['avg_cpu_usage'] ?? null;
            $avgMemory = $summary['avg_memory_usage'] ?? null;
            $count = $summary['count'] ?? 0;

            $responseText = !is_null($avgResponse) ? number_format($avgResponse, 2) . ' ms' : '-';
            $cpuText = !is_null($avgCpu) ? number_format($avgCpu, 2) . '%' : '-';
            $memoryText = !is_null($avgMemory) ? number_format($avgMemory, 2) . '%' : '-';

            $cacheStatus = strtoupper($summary['cache_status'] ?? '-');
            $cacheClass = $cacheBadgeClasses[$cacheStatus] ?? $defaultCacheBadgeClass;

            $winner = $summary['winner_api'] ?? '-';
            $winnerKey = strtolower((string) $winner);
            $winnerClass = $winnerBadgeClasses[$winnerKey] ?? $defaultWinnerBadgeClass;
            $winnerLabel = strtoupper($winner ?: '-');

            $selected = $summary['selected_api'] ?? null;
            $selectedLabel = $selected ? 'Selected: ' . strtoupper($selected) : null;

            $countLabel = $count > 0 ? $count . 'x' : '0x';

            $html = '<div class="flex flex-col gap-1">';
            $html .= '<span class="text-sm font-semibold text-gray-900">' . $responseText . '</span>';
            $html .= '<div class="text-xs text-gray-500">CPU ' . $cpuText . ' | Mem ' . $memoryText . '</div>';
            $html .= '<div class="flex flex-wrap items-center gap-2 text-xs">';
            if ($apiType === 'integrated') {
                $html .= '<span class="px-2 inline-flex font-semibold rounded-full ' . $cacheClass . '">' . $cacheStatus . '</span>';
            }
            $html .= '<span class="px-2 inline-flex font-semibold rounded-full ' . $winnerClass . '">' . $winnerLabel . '</span>';
            $html .= '<span class="px-2 inline-flex font-semibold rounded-full bg-gray-100 text-gray-700">' . $countLabel . '</span>';
            $html .= '</div>';
            if ($apiType === 'integrated' && $selectedLabel) {
                $html .= '<div class="text-xs text-gray-500">' . $selectedLabel . '</div>';
            }
            $html .= '</div>';

            return $html;
        };
    @endphp

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch ID</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REST</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GraphQL</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Integrated</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($recent_batches as $batch)
                @php
                    $summaries = $batch['summaries'];
                    $createdAt = $batch['created_at'] instanceof \Illuminate\Support\Carbon
                        ? $batch['created_at']
                        : \Illuminate\Support\Carbon::parse($batch['created_at']);
                    $createdAtLabel = $createdAt?->timezone(config('app.timezone'))->toDayDateTimeString() ?? '-';
                    $batchId = $batch['batch_id'] ?? null;
                    $logIds = $batch['log_ids'] ?? [];
                    $displayLabel = $batch['display_batch_id'] ?? ($batchId ?: 'Batch');
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-700">
                        {{ $batch['display_batch_id'] ?? $batch['batch_id'] ?? '-' }}
                        @if(!empty($batch['is_legacy']))
                            <span class="ml-2 px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-200 text-gray-700">Legacy</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $batch['query_id'] }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $createdAtLabel }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{!! $renderSummaryCell($summaries['rest'], 'rest') !!}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{!! $renderSummaryCell($summaries['graphql'], 'graphql') !!}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{!! $renderSummaryCell($summaries['integrated'], 'integrated') !!}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <div class="flex items-center gap-3">
                            <button
                                type="button"
                                onclick="showBatchDetails({{ json_encode($batch['batch_id'] ?? null) }}, {{ json_encode($batch['query_id']) }}, {{ json_encode($batch['created_date'] ?? null) }})"
                                class="text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                            <button
                                type="button"
                                onclick="confirmDeleteBatch({{ json_encode($batchId) }}, {{ json_encode($logIds) }}, {{ json_encode($displayLabel) }})"
                                class="text-red-600 hover:text-red-800 font-medium">
                                <i class="fas fa-trash mr-1"></i>Hapus
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        Belum ada data pengujian
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">
        {{ $recent_batches->appends(request()->except('history_page'))->links() }}
    </div>
</div>



<div id="testDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 999;">
    <div class="relative top-10 mx-auto p-5 border w-11/12 md:w-4/5 lg:w-3/4 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="text-xl font-semibold text-gray-900" id="testDetailsTitle">Detail Pengujian</h3>
            <button onclick="closeTestDetailsModal()" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mt-4">
            <!-- Statistik Pengujian -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Total Pengujian</h4>
                    <p class="text-2xl font-bold text-blue-900" id="totalTests">-</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">Success Rate</h4>
                    <p class="text-2xl font-bold text-green-900" id="successRate">-</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-purple-800 mb-2">Cache Hits</h4>
                    <p class="text-2xl font-bold text-purple-900" id="cacheHits">-</p>
                </div>
            </div>
            
            <!-- Detail Tabel -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">REST (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GraphQL (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">CPU (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Memory (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cache</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pemenang</th>
                        </tr>
                    </thead>
                    <tbody id="testDetailsTable" class="bg-white divide-y divide-gray-200">
                        <!-- Data akan diisi oleh JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeTestDetailsModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                Tutup
            </button>
        </div>
    </div>
</div>

<div id="deleteBatchModal" class="fixed inset-0 bg-gray-700 bg-opacity-50 hidden overflow-y-auto h-full w-full" style="z-index: 1000;">
    <div class="relative top-20 mx-auto w-11/12 md:w-1/2 lg:w-1/3 bg-white rounded-lg shadow-xl border">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h3 class="text-lg font-semibold text-gray-900">Konfirmasi Penghapusan</h3>
            <button type="button" onclick="closeDeleteBatchModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="px-6 py-5 space-y-4">
            <p class="text-gray-700 text-sm leading-relaxed">
                Apakah Anda yakin ingin menghapus riwayat pengujian untuk batch
                <span id="deleteBatchLabel" class="font-semibold text-gray-900"></span>?
            </p>
            <p class="text-xs text-gray-500">
                Tindakan ini tidak dapat dibatalkan dan semua data terkait batch tersebut akan dihapus.
            </p>
        </div>
        <div class="px-6 py-4 border-t flex justify-end gap-3">
            <button type="button" onclick="closeDeleteBatchModal()" class="px-4 py-2 rounded-md border border-gray-300 text-gray-700 hover:bg-gray-100">
                Batal
            </button>
            <button type="button" id="confirmDeleteBatchButton" onclick="performBatchDeletion()" class="px-4 py-2 rounded-md bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                Hapus
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const performanceChartData = @json($performanceCharts);

    // Query descriptions mapping
    const queryDescriptions = {
        Q1: 'Mengambil nama dari 100 project teratas berdasarkan jumlah stars.',
        Q2: 'Mengambil jumlah total pull request dan isi dari 1.000 pull request terbaru.',
        Q3: 'Mengambil isi komentar untuk setiap pull request.',
        Q4: 'Mengambil nama dan URL dari 5 project teratas.',
        Q5: 'Mengambil jumlah commit, branch, bug, release, dan kontributor.',
        Q6: 'Mengambil judul dan isi dari bug yang sudah ditutup.',
        Q7: 'Mengambil isi komentar untuk setiap bug yang ditutup.',
        Q8: 'Mengambil nama dan URL dari project Java tertentu.',
        Q9: 'Mengambil jumlah stars dari project tertentu.',
        Q10: 'Mengambil nama repository dengan 1.000+ stars.',
        Q11: 'Mengambil jumlah commit dalam sebuah repository.',
        Q12: 'Mengambil jumlah release, stars, dan bahasa pemrograman.',
        Q13: 'Mengambil judul, isi, tanggal dari open issue dengan tag "bug".',
        Q14: 'Mengambil isi komentar untuk setiap issue.'
    };
    
    // Query details dari server
    function updateQueryDescription() {
        const select = document.getElementById('query_id');
        const descDiv = document.getElementById('queryDescription');
        const customWrapper = document.getElementById('customDescriptionWrapper');
        const val = select.value;
        if (val === 'Q15') {
            descDiv.classList.add('hidden');
            customWrapper.classList.remove('hidden');
        } else {
            descDiv.textContent = queryDescriptions[val] || '';
            descDiv.classList.remove('hidden');
            customWrapper.classList.add('hidden');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Set initial description
        updateQueryDescription();
        // Update description on change
        document.getElementById('query_id').addEventListener('change', updateQueryDescription);

        initializePerformanceCharts(performanceChartData);
    });

function showResultModal(result) {
    // Update modal content
    const resultTitle = document.getElementById('resultTitle');
    if (resultTitle) {
        if (result.error) {
            resultTitle.textContent = 'Pengujian Gagal';
        } else if (result.served_from_cache) {
            resultTitle.textContent = 'Hasil Pengujian (Cache HIT)';
        } else {
            resultTitle.textContent = 'Hasil Pengujian';
        }
    }

    document.getElementById('queryId').textContent = result.query_id || '-';
    document.getElementById('repository').textContent = result.repository || '-';

    const cacheStatusEl = document.getElementById('cacheStatus');
    const cacheStatusRaw = result.cache_status || '-';
    let cacheStatusText = cacheStatusRaw;
    let cacheStatusClass = 'text-gray-700';
    if (cacheStatusRaw === 'HIT') {
        cacheStatusText = 'HIT (Cache)';
        cacheStatusClass = 'text-purple-700';
    } else if (cacheStatusRaw === 'MISS') {
        cacheStatusText = 'MISS (Fresh Fetch)';
        cacheStatusClass = 'text-green-700';
    } else if (cacheStatusRaw === 'WINNER_REFRESH') {
        cacheStatusText = 'Winner Re-run';
        cacheStatusClass = 'text-indigo-700';
    } else if (cacheStatusRaw === 'WINNER_ONLY') {
        cacheStatusText = 'Winner Cached';
        cacheStatusClass = 'text-blue-700';
    } else if (cacheStatusRaw === 'DISABLED') {
        cacheStatusText = 'Cache Disabled';
        cacheStatusClass = 'text-gray-500';
    }
    cacheStatusEl.textContent = cacheStatusText;
    cacheStatusEl.className = 'font-medium ' + cacheStatusClass;

    const processingTime = typeof result.processing_time_ms === 'number'
        ? result.processing_time_ms
        : null;
    const processingTimeEl = document.getElementById('processingTime');
    processingTimeEl.textContent = processingTime !== null
        ? `${processingTime.toFixed(2)} ms${result.served_from_cache ? ' (permintaan ini)' : ''}`
        : '-';
    
    // Update complexity
    const complexityEl = document.getElementById('complexity');
    const complexity = result.complexity || 'simple';
    complexityEl.textContent = complexity === 'simple' ? 'Sederhana' : 'Kompleks';
    complexityEl.className = 'font-medium px-2 py-1 rounded text-xs ' + 
        (complexity === 'simple' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800');
    
    // Update CPU and Memory usage
    document.getElementById('cpuUsage').textContent = result.cpu_usage 
        ? `${result.cpu_usage.toFixed(2)}%` 
        : '0%';
    document.getElementById('memoryUsage').textContent = result.memory_usage 
        ? `${result.memory_usage.toFixed(2)}%` 
        : '0%';

    const statusEl = document.getElementById('status');
    if (result.error) {
        statusEl.textContent = result.message || 'Terjadi kesalahan';
        statusEl.className = 'font-medium text-red-600';
    } else if (result.served_from_cache) {
        statusEl.textContent = 'Cache HIT - dilayani dari cache';
        statusEl.className = 'font-medium text-purple-600';
    } else if (cacheStatusRaw === 'WINNER_REFRESH') {
        statusEl.textContent = 'Winner re-run – only fastest API executed';
        statusEl.className = 'font-medium text-indigo-700';
    } else if (cacheStatusRaw === 'WINNER_ONLY') {
        statusEl.textContent = 'Winner cached – only fastest API reused';
        statusEl.className = 'font-medium text-blue-700';
    } else if (cacheStatusRaw === 'DISABLED') {
        statusEl.textContent = 'Cache dimatikan';
        statusEl.className = 'font-medium text-gray-600';
    } else {
        statusEl.textContent = 'Live Request';
        statusEl.className = 'font-medium text-green-600';
    }
    
    // Update winner information
    const winner = document.getElementById('winner');
    winner.textContent = result.winner_api ? result.winner_api.toUpperCase() : '-';
    winner.className = 'font-medium ' + (result.winner_api === 'rest' ? 'text-blue-600' : 'text-green-600');
    
    const restValue = typeof result.rest_response_time_ms === 'number' ? result.rest_response_time_ms : 0;
    const graphqlValue = typeof result.graphql_response_time_ms === 'number' ? result.graphql_response_time_ms : 0;

    const timeDiff = Math.abs(restValue - graphqlValue);
    document.getElementById('timeDiff').textContent = `${timeDiff.toFixed(2)} ms`;
    
    const restTimeElement = document.getElementById('restTime');
    const graphqlTimeElement = document.getElementById('graphqlTime');
    const restDisplay = Number.isFinite(restValue) ? restValue.toFixed(2) : '0.00';
    const graphqlDisplay = Number.isFinite(graphqlValue) ? graphqlValue.toFixed(2) : '0.00';

    restTimeElement.textContent = `${restDisplay} ms`;
    graphqlTimeElement.textContent = `${graphqlDisplay} ms`;
    
    const maxValue = Math.max(restValue, graphqlValue, 1);
    const restWidth = Number.isFinite(restValue) ? (restValue / maxValue) * 100 : 0;
    const graphqlWidth = Number.isFinite(graphqlValue) ? (graphqlValue / maxValue) * 100 : 0;
    
    document.getElementById('restBar').style.width = `${Math.min(100, Math.max(0, restWidth))}%`;
    document.getElementById('graphqlBar').style.width = `${Math.min(100, Math.max(0, graphqlWidth))}%`;
    
    // Update response data REST
    const responseDataRest = document.getElementById('responseDataRest');
    responseDataRest.textContent = JSON.stringify(result.response_data_rest || {}, null, 2);
    // Tampilkan error REST jika ada
    const restErrorMsg = document.getElementById('restErrorMsg');
    if (result.rest_error) {
        restErrorMsg.textContent = 'REST API Error: ' + result.rest_error;
        restErrorMsg.classList.remove('hidden');
    } else {
        restErrorMsg.textContent = '';
        restErrorMsg.classList.add('hidden');
    }
    if (typeof Prism !== 'undefined') {
        Prism.highlightElement(responseDataRest);
    }
    // Update response data GraphQL
    const responseDataGraphql = document.getElementById('responseDataGraphql');
    responseDataGraphql.textContent = JSON.stringify(result.response_data_graphql || {}, null, 2);
    // Tampilkan error GraphQL jika ada
    const graphqlErrorMsg = document.getElementById('graphqlErrorMsg');
    if (result.graphql_error) {
        graphqlErrorMsg.textContent = 'GraphQL API Error: ' + JSON.stringify(result.graphql_error);
        graphqlErrorMsg.classList.remove('hidden');
    } else {
        graphqlErrorMsg.textContent = '';
        graphqlErrorMsg.classList.add('hidden');
    }
    if (typeof Prism !== 'undefined') {
        Prism.highlightElement(responseDataGraphql);
    }
    
    // Show modal
    document.getElementById('resultModal').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('resultModal').classList.add('hidden');
}

// Modify form submission
document.querySelector('form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Disable button and show loading state
    const submitBtn = document.getElementById('submitBtn');
    const submitBtnText = document.getElementById('submitBtnText');
    const loadingIcon = document.getElementById('loadingIcon');
    
    submitBtn.disabled = true;
    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
    submitBtnText.textContent = 'Sedang Memproses...';
    loadingIcon.classList.remove('hidden');
    
    try {
        const formData = new FormData(this);
        const response = await fetch('/test', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        showResultModal(result);
    } catch (error) {
        console.error('Error:', error);
        showResultModal({
            error: true,
            message: 'Terjadi kesalahan saat menjalankan pengujian'
        });
    } finally {
        // Reset button state
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        submitBtnText.textContent = 'Jalankan Pengujian';
        loadingIcon.classList.add('hidden');
    }
});

// Performance Test Button Handler
document.getElementById('runPerformanceTestBtn').addEventListener('click', async function() {
    const queryId = document.getElementById('query_id').value;
    const requestCount = parseInt(document.getElementById('request_count').value, 10);
    const batchId = `batch-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
    const scenarios = [
        { api_type: 'rest', label: 'REST API', cache: false },
        { api_type: 'graphql', label: 'GraphQL API', cache: false },
        { api_type: 'integrated', label: 'Integrated API', cache: true }
    ];
    const results = [];
    
    // Disable button and show loading state
    const performanceTestBtn = document.getElementById('runPerformanceTestBtn');
    const performanceTestBtnText = document.getElementById('performanceTestBtnText');
    const performanceLoadingIcon = document.getElementById('performanceLoadingIcon');
    
    performanceTestBtn.disabled = true;
    performanceTestBtn.classList.add('opacity-75', 'cursor-not-allowed');
    performanceTestBtnText.textContent = 'Sedang Mengukur...';
    performanceLoadingIcon.classList.remove('hidden');
    
    try {
        for (const scenario of scenarios) {
            let scenarioResult;
            try {
                const response = await fetch('/run-performance-test', {
                    method: 'POST',
                    body: JSON.stringify({
                        query_id: queryId,
                        api_type: scenario.api_type,
                        request_count: requestCount,
                        cache: scenario.cache,
                        batch_id: batchId
                    }),
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                scenarioResult = await response.json();
            } catch (err) {
                scenarioResult = {
                    success: false,
                    error: err.message || 'Terjadi kesalahan jaringan'
                };
            }

            results.push({
                api_type: scenario.api_type,
                label: scenario.label,
                cache: scenario.cache,
                success: scenarioResult.success === true,
                data: scenarioResult.success === true ? scenarioResult.data : null,
                error: scenarioResult.success === true ? null : (scenarioResult.error || 'Terjadi kesalahan saat menjalankan pengujian')
            });

            if (!scenarioResult.success) {
                break;
            }
        }

        showPerformanceResultsSummary({
            query_id: queryId,
            request_count: requestCount,
            batch_id: batchId,
            results
        });
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menjalankan uji performa');
    } finally {
        // Reset button state
        performanceTestBtn.disabled = false;
        performanceTestBtn.classList.remove('opacity-75', 'cursor-not-allowed');
        performanceTestBtnText.textContent = 'Uji Performa';
        performanceLoadingIcon.classList.add('hidden');
    }
});

// Function to show performance test result
// Function to show performance test result summary
function showPerformanceResultsSummary(payload) {
    const { query_id, request_count, batch_id, results } = payload;
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';
    modal.style.zIndex = '1000';

    const renderValue = (value, suffix = '', fallback = '-') => {
        if (value === null || value === undefined || Number.isNaN(value)) {
            return fallback;
        }
        if (typeof value === 'number') {
            return `${value.toFixed(2)}${suffix}`;
        }
        return `${value}${suffix}`;
    };

    const cardsHtml = results.map(result => {
        if (!result.success || !result.data) {
            return `
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-700 mb-2 flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>${result.label}
                    </h4>
                    <p class="text-sm text-red-600">${result.error || 'Terjadi kesalahan saat menjalankan pengujian.'}</p>
                </div>
            `;
        }

        const data = result.data;
        const responseTimes = data.details?.response_times || [];
        const totalTime = data.details?.total_time || 0;
        const throughput = totalTime > 0 ? data.request_count / (totalTime / 1000) : 0;
        const minResponse = responseTimes.length ? Math.min(...responseTimes) : null;
        const maxResponse = responseTimes.length ? Math.max(...responseTimes) : null;

        return `
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 space-y-3">
                <div>
                    <h4 class="font-semibold text-gray-800">${result.label}</h4>
                    <p class="text-xs text-gray-500 uppercase tracking-wide">${result.cache ? 'Cache Enabled (Integrated)' : 'Cache Disabled'}</p>
                    <span class="inline-flex items-center px-2 py-0.5 mt-2 text-xs font-semibold rounded-full bg-green-100 text-green-800">Berhasil</span>
                </div>
                <div class="space-y-1 text-sm text-gray-700">
                    <p><span class="font-medium text-gray-600">Avg. Response:</span> ${renderValue(data.avg_response_time_ms, ' ms')}</p>
                    <p><span class="font-medium text-gray-600">CPU Usage:</span> ${renderValue(data.cpu_usage_percent, ' %')}</p>
                    <p><span class="font-medium text-gray-600">Memory Usage:</span> ${renderValue(data.memory_usage_percent, ' %')}</p>
                </div>
                <div class="space-y-1 text-sm text-gray-700">
                    <p><span class="font-medium text-gray-600">Throughput:</span> ${renderValue(throughput, ' req/s')}</p>
                    <p><span class="font-medium text-gray-600">Total Time:</span> ${renderValue(totalTime, ' ms')}</p>
                </div>
                <div class="space-y-1 text-sm text-gray-700">
                    <p><span class="font-medium text-gray-600">Min Response:</span> ${renderValue(minResponse, ' ms')}</p>
                    <p><span class="font-medium text-gray-600">Max Response:</span> ${renderValue(maxResponse, ' ms')}</p>
                </div>
            </div>
        `;
    }).join('');

    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-900">Hasil Uji Performa</h3>
                <button onclick="closePerformanceSummaryModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-700 mb-2">Ringkasan Pengujian</h4>
                    <div class="flex flex-wrap gap-4 text-sm text-gray-700">
                        <p><span class="font-medium">Query ID:</span> ${query_id}</p>
                        <p><span class="font-medium">Jumlah Request:</span> ${request_count}</p>
                        <p><span class="font-medium">Batch ID:</span> ${batch_id}</p>
                        <p><span class="font-medium">Timestamp:</span> ${new Date().toLocaleString()}</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    ${cardsHtml}
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closePerformanceSummaryModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Tutup
                </button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    window.closePerformanceSummaryModal = function() {
        document.body.removeChild(modal);
        delete window.closePerformanceSummaryModal;
    };
}

// Helper function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'warning' ? 'bg-yellow-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

const apiBarColors = {
    REST: {
        background: 'rgba(59, 130, 246, 0.7)',
        border: 'rgba(37, 99, 235, 1)'
    },
    GraphQL: {
        background: 'rgba(34, 197, 94, 0.7)',
        border: 'rgba(22, 163, 74, 1)'
    },
    Integrated: {
        background: 'rgba(168, 85, 247, 0.7)',
        border: 'rgba(147, 51, 234, 1)'
    }
};

const chartConfigs = [
    {
        canvasId: 'chartResponseTime',
        title: 'Waktu (ms)',
        key: 'response',
        precision: 2
    },
    {
        canvasId: 'chartCpuUsage',
        title: 'Penggunaan CPU (%)',
        key: 'cpu',
        precision: 2
    },
    {
        canvasId: 'chartMemoryUsage',
        title: 'Penggunaan Memori (%)',
        key: 'memory',
        precision: 2
    }
];

const dashboardCharts = {};
let chartFilterInitialized = false;
let currentPerformanceChartData = null;

function initializePerformanceCharts(data) {
    currentPerformanceChartData = data;
    chartConfigs.forEach(config => buildAverageBarChart(config, data));
    initChartFilterControls();
}

function updatePerformanceCharts(data) {
    currentPerformanceChartData = data;
    chartConfigs.forEach(config => buildAverageBarChart(config, data));
}

function initChartFilterControls() {
    if (chartFilterInitialized) {
        return;
    }

    const select = document.querySelector('[data-chart-filter]');
    const button = document.querySelector('[data-chart-filter-button]');
    if (!select) {
        return;
    }

    const triggerRefresh = () => {
        refreshDashboardCharts(select.value);
    };

    select.addEventListener('change', triggerRefresh);
    if (button) {
        button.addEventListener('click', triggerRefresh);
    }

    chartFilterInitialized = true;
}

function renderChartPlaceholder(canvasId) {
    const canvas = document.getElementById(canvasId);
    if (!canvas || !canvas.parentNode) {
        return;
    }

    const container = canvas.parentNode;
    let placeholder = container.querySelector('.chart-placeholder');
    if (!placeholder) {
        placeholder = document.createElement('p');
        placeholder.className = 'chart-placeholder text-sm text-gray-500';
        container.appendChild(placeholder);
    }
    placeholder.textContent = 'Belum ada data pengujian untuk ditampilkan.';
    placeholder.classList.remove('hidden');
    canvas.classList.add('hidden');

    if (dashboardCharts[canvasId]) {
        dashboardCharts[canvasId].destroy();
        delete dashboardCharts[canvasId];
    }
}

function hideChartPlaceholder(canvas) {
    if (!canvas) {
        return;
    }
    canvas.classList.remove('hidden');
    const placeholder = canvas.parentNode?.querySelector('.chart-placeholder');
    if (placeholder) {
        placeholder.classList.add('hidden');
    }
}

function buildAverageBarChart(config, data) {
    const { canvasId, title, key, precision } = config;
    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        return;
    }

    const labels = Array.isArray(data?.labels) ? data.labels : [];
    const rawValues = Array.isArray(data?.[key]) ? data[key] : [];

    if (labels.length === 0 || rawValues.length === 0) {
        renderChartPlaceholder(canvasId);
        return;
    }

    const sanitizedValues = rawValues.map(value => {
        if (value === null || typeof value === 'undefined') {
            return null;
        }
        const numeric = Number(value);
        return Number.isNaN(numeric) ? null : numeric;
    });

    const numericValues = sanitizedValues.filter(value => typeof value === 'number' && !Number.isNaN(value));
    const hasData = numericValues.length > 0;
    if (!hasData) {
        renderChartPlaceholder(canvasId);
        return;
    }

    hideChartPlaceholder(canvas);

    const backgroundColors = labels.map(label => apiBarColors[label]?.background || 'rgba(107, 114, 128, 0.6)');
    const borderColors = labels.map(label => apiBarColors[label]?.border || 'rgba(75, 85, 99, 1)');

    const maxValue = Math.max(...numericValues);
    const dynamicMax = maxValue <= 0 ? 10 : maxValue * 1.15;

    const ctx = canvas.getContext('2d');
    const existingChart = dashboardCharts[canvasId];
    if (existingChart) {
        existingChart.data.labels = labels;
        existingChart.data.datasets[0].data = sanitizedValues;
        existingChart.data.datasets[0].backgroundColor = backgroundColors;
        existingChart.data.datasets[0].borderColor = borderColors;
        existingChart.options.scales.y.suggestedMax = dynamicMax;
        existingChart.options.plugins.tooltip.callbacks.label = buildTooltipFormatter(precision);
        existingChart.update();
        return;
    }

    dashboardCharts[canvasId] = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Rata-rata',
                    data: sanitizedValues,
                    backgroundColor: backgroundColors,
                    borderColor: borderColors,
                    borderWidth: 1,
                    maxBarThickness: 48
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    suggestedMax: dynamicMax,
                    title: {
                        display: Boolean(title),
                        text: title,
                        font: {
                            size: 12,
                            weight: '500'
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: buildTooltipFormatter(precision)
                    }
                }
            }
        }
    });
}

function buildTooltipFormatter(precision) {
    return function(context) {
        const value = context.parsed.y;
        if (value === null || typeof value === 'undefined' || Number.isNaN(value)) {
            return '-';
        }
        const decimals = typeof precision === 'number' ? precision : 2;
        return `${value.toFixed(decimals)}`;
    };
}

async function refreshDashboardCharts(queryId) {
    const select = document.querySelector('[data-chart-filter]');
    const button = document.querySelector('[data-chart-filter-button]');

    const params = new URLSearchParams();
    if (queryId) {
        params.set('chart_query', queryId);
    }

    const url = `/dashboard-chart-data${params.toString() ? `?${params.toString()}` : ''}`;

    try {
        if (select) {
            select.disabled = true;
        }
        if (button) {
            button.disabled = true;
            button.classList.add('opacity-75', 'cursor-not-allowed');
        }

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (result.success) {
            updatePerformanceCharts(result.data);
        } else {
            showNotification(result.error || 'Gagal memuat data performa.', 'error');
        }
    } catch (error) {
        console.error('Error refreshing chart data:', error);
        showNotification('Terjadi kesalahan saat memuat data performa.', 'error');
    } finally {
        if (select) {
            select.disabled = false;
        }
        if (button) {
            button.disabled = false;
            button.classList.remove('opacity-75', 'cursor-not-allowed');
        }
    }
}

// Function to show test details modal
async function showBatchDetails(batchId, queryId, testDate = null, apiType = 'all') {
    try {
        const params = new URLSearchParams({
            api_type: apiType
        });

        if (batchId) {
            params.set('batch_id', batchId);
        } else {
            params.set('query_id', queryId);
            if (testDate) {
                params.set('test_date', testDate);
            }
        }

        const response = await fetch(`/test-details?${params.toString()}`);
        const result = await response.json();
        
        if (result.success) {
            // Update modal title
            const batchLabel = batchId || (testDate ? `Tanggal ${testDate}` : 'Riwayat');
            document.getElementById('testDetailsTitle').textContent = `Detail Pengujian ${queryId} - ${batchLabel}`;
            
            // Update statistics
            document.getElementById('totalTests').textContent = result.stats.total_tests;
            document.getElementById('successRate').textContent = result.stats.success_rate + '%';
            document.getElementById('cacheHits').textContent = result.stats.cache_hits;
            
            // Update table
            const tbody = document.getElementById('testDetailsTable');
            tbody.innerHTML = '';
            
            result.data.forEach(test => {
                const row = document.createElement('tr');
                const restValue = test.rest_response_time_ms !== null && test.rest_response_time_ms !== undefined
                    ? Number(test.rest_response_time_ms).toFixed(2) + ' ms'
                    : '-';
                const graphqlValue = test.graphql_response_time_ms !== null && test.graphql_response_time_ms !== undefined
                    ? Number(test.graphql_response_time_ms).toFixed(2) + ' ms'
                    : '-';
                
                let cacheLabelRaw = (test.cache_status || 'MISS').toUpperCase();
                if (cacheLabelRaw === 'WINNER_REFRESH') {
                    cacheLabelRaw = 'HIT';
                }
                const cacheLabel = cacheLabelRaw === 'DISABLED' ? '-' : cacheLabelRaw;
                const cacheClass =
                    cacheLabel === 'HIT' || cacheLabel === 'CACHE_USED'
                        ? 'bg-purple-100 text-purple-800'
                        : cacheLabel === 'MISS'
                            ? 'bg-gray-100 text-gray-800'
                            : cacheLabel === 'WINNER_REFRESH'
                                ? 'bg-indigo-100 text-indigo-800'
                                : cacheLabel === 'WINNER_ONLY'
                                    ? 'bg-blue-100 text-blue-800'
                                    : cacheLabel === '-'
                                        ? 'bg-gray-100 text-gray-500'
                                        : 'bg-yellow-100 text-yellow-800';
                const cpuUsage = test.cpu_usage !== null && test.cpu_usage !== undefined
                    ? Number(test.cpu_usage).toFixed(2) + '%'
                    : '-';
                const memoryUsage = test.memory_usage !== null && test.memory_usage !== undefined
                    ? Number(test.memory_usage).toFixed(2) + '%'
                    : '-';

                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${new Date(test.created_at).toLocaleString()}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${restValue}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${graphqlValue}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${cpuUsage}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${memoryUsage}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${cacheClass}">
                            ${cacheLabel}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${test.winner_api === 'rest' ? 'bg-blue-100 text-blue-800' : test.winner_api === 'graphql' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                            ${test.winner_api ? test.winner_api.toUpperCase() : 'NONE'}
                        </span>
                    </td>
                `;
                tbody.appendChild(row);
            });
            
            // Show modal
            document.getElementById('testDetailsModal').classList.remove('hidden');
        } else {
            showNotification('Gagal memuat detail pengujian: ' + result.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memuat detail pengujian', 'error');
    }
}

// Function to close test details modal
function closeTestDetailsModal() {
    document.getElementById('testDetailsModal').classList.add('hidden');
}

let pendingDeleteBatch = { batchId: null, logIds: [], label: null };
let deleteRequestInFlight = false;

function confirmDeleteBatch(batchId, logIds, label) {
    const modal = document.getElementById('deleteBatchModal');
    const labelEl = document.getElementById('deleteBatchLabel');

    pendingDeleteBatch = {
        batchId: batchId || null,
        logIds: Array.isArray(logIds) ? logIds : [],
        label: label || batchId || 'Batch'
    };

    if (labelEl) {
        labelEl.textContent = pendingDeleteBatch.label;
    }

    if (modal) {
        modal.classList.remove('hidden');
    }
}

function closeDeleteBatchModal(force = false) {
    if (!force && deleteRequestInFlight) {
        return;
    }

    const modal = document.getElementById('deleteBatchModal');
    if (modal) {
        modal.classList.add('hidden');
    }

    pendingDeleteBatch = { batchId: null, logIds: [], label: null };
}

async function performBatchDeletion() {
    if (deleteRequestInFlight) {
        return;
    }

    const modal = document.getElementById('deleteBatchModal');
    const button = document.getElementById('confirmDeleteBatchButton');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    if (!modal || !button || !csrfToken) {
        showNotification('Konfigurasi penghapusan tidak lengkap', 'error');
        return;
    }

    if (!pendingDeleteBatch.batchId && (!pendingDeleteBatch.logIds || pendingDeleteBatch.logIds.length === 0)) {
        showNotification('Batch tidak valid untuk dihapus', 'error');
        return;
    }

    deleteRequestInFlight = true;
    let shouldReloadAfterDelete = false;
    const originalLabel = button.innerHTML;

    button.disabled = true;
    button.innerHTML = '<span class="flex items-center gap-2"><svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menghapus...</span>';

    try {
        const payload = {};
        if (pendingDeleteBatch.batchId) {
            payload.batch_id = pendingDeleteBatch.batchId;
        } else {
            payload.log_ids = pendingDeleteBatch.logIds;
        }

        const response = await fetch('/test-batches', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.error || 'Gagal menghapus riwayat pengujian');
        }

        shouldReloadAfterDelete = true;
        showNotification('Riwayat pengujian berhasil dihapus', 'success');
    } catch (error) {
        console.error('Error deleting batch:', error);
        showNotification(error.message || 'Terjadi kesalahan saat menghapus riwayat pengujian', 'error');
    } finally {
        deleteRequestInFlight = false;
        button.disabled = false;
        button.innerHTML = originalLabel;

        if (shouldReloadAfterDelete) {
            closeDeleteBatchModal(true);
            setTimeout(() => {
                window.location.reload();
            }, 600);
        }
    }
}

</script>

<!-- Tambahkan Prism.js untuk syntax highlighting -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-json.min.js"></script>
@endsection
