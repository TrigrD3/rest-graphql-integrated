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
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="api_type" class="block text-sm font-medium text-gray-700 mb-1">Tipe API:</label>
                        <select id="api_type" name="api_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                            <option value="rest">REST API</option>
                            <option value="graphql">GraphQL API</option>
                            <option value="integrated">Integrated API (Cerdas)</option>
                        </select>
                    </div>
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
            </div>
            
            <!-- Kolom baru untuk menampilkan REST Endpoints -->
            <div id="repoEndpoints" class="hidden">
                <label class="block text-sm font-medium text-gray-700 mb-2">REST Endpoints untuk Repository:</label>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <ul id="repoEndpointsList" class="list-disc list-inside text-sm text-gray-700 space-y-1"></ul>
                </div>
            </div>
            
            <!-- Section untuk menampilkan detail Query -->
        </form>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-info-circle text-primary-600 mr-2"></i>
            Statistik Sistem
        </h2>
        
        <div class="space-y-4">
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Penggunaan CPU</span>
                    <span class="text-sm font-medium text-gray-700">{{ $metrics['cpu_usage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $metrics['cpu_usage'] }}%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Penggunaan Memori</span>
                    <span class="text-sm font-medium text-gray-700">{{ $metrics['memory_usage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $metrics['memory_usage'] }}%"></div>
                </div>
            </div>
            
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium text-gray-700">Penggunaan Disk</span>
                    <span class="text-sm font-medium text-gray-700">{{ $metrics['disk_usage'] }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $metrics['disk_usage'] }}%"></div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-200">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Total Pengujian</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $metrics['total_tests'] }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Cache Hit Rate</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $metrics['cache_hit_rate'] }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">REST Wins</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $metrics['rest_wins'] }}%</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">GraphQL Wins</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $metrics['graphql_wins'] }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-chart-bar text-primary-600 mr-2"></i>
            Perbandingan Waktu Respons
        </h2>
        
        <div class="h-64">
            <canvas id="responseTimeChart"></canvas>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-semibold mb-4 flex items-center">
            <i class="fas fa-chart-pie text-primary-600 mr-2"></i>
            Distribusi API Pemenang
        </h2>
        
        <div class="h-64">
            <canvas id="winnerDistributionChart"></canvas>
        </div>
    </div>
</div>

<!-- API Comparison Section -->
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h2 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-chart-bar text-primary-600 mr-2"></i>
        Perbandingan API
        <span id="dataSourceIndicator" class="ml-2 text-sm font-normal text-gray-500"></span>
    </h2>
    
    <div class="mb-4">
        <label for="comparison_query" class="block text-sm font-medium text-gray-700 mb-2">Pilih Query untuk Perbandingan:</label>
        <div class="flex items-center gap-3">
            <select id="comparison_query" class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-500 focus:ring-opacity-50 {{ empty($availableComparisonQueries) ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ empty($availableComparisonQueries) ? 'disabled' : '' }}>
                @forelse($availableComparisonQueries as $queryId => $description)
                    <option value="{{ $queryId }}">{{ $queryId }}: {{ $description }}</option>
                @empty
                    <option value="">Belum ada data pengujian</option>
                @endforelse
            </select>
            <button id="updateComparisonBtn" class="px-4 py-2 text-white rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 {{ empty($availableComparisonQueries) ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-700 focus:ring-primary-500' }}" {{ empty($availableComparisonQueries) ? 'disabled' : '' }}>
                <i class="fas fa-sync mr-1"></i>
                Tampilkan Perbandingan
            </button>
        </div>
        @if(empty($availableComparisonQueries))
        <p class="mt-2 text-sm text-amber-600">
            <i class="fas fa-info-circle mr-1"></i>
            Silakan jalankan pengujian terlebih dahulu untuk melihat perbandingan.
        </p>
        @endif
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6">
        <h3 class="text-lg font-semibold mb-4 text-center">Perbandingan Metrik API (REST vs GraphQL vs Integrated)</h3>
        <div class="h-96">
            <canvas id="apiComparisonChart"></canvas>
        </div>
    </div>
    
    <!-- Data Info Section -->
    <div id="dataInfo" class="mt-4 p-4 bg-blue-50 rounded-lg hidden">
        <div class="flex items-center">
            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
            <span class="text-sm text-blue-800">
                <strong>Data Source:</strong> <span id="dataSourceText">-</span> | 
                <strong>Query:</strong> <span id="currentQuery">-</span> | 
                <strong>Last Updated:</strong> <span id="lastUpdated">-</span>
            </span>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-xl font-semibold mb-4 flex items-center">
        <i class="fas fa-history text-primary-600 mr-2"></i>
        Riwayat Pengujian Terbaru
    </h2>

    <!-- Tabs Navigation -->
    <div class="mb-4">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button id="tab-rest" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-primary-500 text-primary-600" data-tab="rest">
                    REST API
                </button>
                <button id="tab-graphql" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="graphql">
                    GraphQL API
                </button>
                <button id="tab-integrated" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="integrated">
                    Integrated API
                </button>
                <button id="tab-all" class="tab-button whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300" data-tab="all">
                    Semua
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div id="tab-content-rest" class="tab-content">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cache</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_rest_tests as $test)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->query_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->cache_status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_rest_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                {{ $test->test_count }} tests
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($test->latest_test_time)->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="showTestDetails('{{ $test->query_id }}', '{{ \Carbon\Carbon::parse($test->latest_test_time)->format('Y-m-d') }}', 'rest')"
                                    class="text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data pengujian REST API
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-content-graphql" class="tab-content hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cache</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Response Time (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_graphql_tests as $test)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->query_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->cache_status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_graphql_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $test->test_count }} tests
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($test->latest_test_time)->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="showTestDetails('{{ $test->query_id }}', '{{ \Carbon\Carbon::parse($test->latest_test_time)->format('Y-m-d') }}', 'graphql')"
                                    class="text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data pengujian GraphQL API
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-content-integrated" class="tab-content hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cache</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REST (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GraphQL (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemenang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_integrated_tests as $test)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->query_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->cache_status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_rest_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_graphql_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->latest_winner == 'rest' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($test->latest_winner) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                {{ $test->test_count }} tests
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($test->latest_test_time)->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="showTestDetails('{{ $test->query_id }}', '{{ \Carbon\Carbon::parse($test->latest_test_time)->format('Y-m-d') }}', 'integrated')"
                                    class="text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data pengujian Integrated API
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-content-all" class="tab-content hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cache</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">REST Avg (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GraphQL Avg (ms)</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tests</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pemenang</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($recent_tests as $test)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $test->query_id }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $test->cache_status }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_rest_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($test->avg_graphql_time, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                {{ $test->test_count }} tests
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $test->latest_winner == 'rest' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($test->latest_winner) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ \Carbon\Carbon::parse($test->latest_test_time)->diffForHumans() }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            <button onclick="showTestDetails('{{ $test->query_id }}', '{{ \Carbon\Carbon::parse($test->latest_test_time)->format('Y-m-d') }}')"
                                    class="text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-eye mr-1"></i>Detail
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            Belum ada data pengujian
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4 flex justify-center">
        <a href="/logs" class="inline-flex items-center text-primary-600 hover:text-primary-800">
            Lihat Semua Riwayat
            <i class="fas fa-arrow-right ml-2"></i>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-6 gap-4 mt-6">
    <a href="/documentation" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-book text-primary-600 mr-2"></i>
        <span>Dokumentasi</span>
    </a>
    
    <a href="/docs/dashboard" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-tachometer-alt text-primary-600 mr-2"></i>
        <span>Dashboard</span>
    </a>
    
    <a href="/docs/repositories" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-code-branch text-primary-600 mr-2"></i>
        <span>Repository</span>
    </a>
    
    <a href="/docs/metrics" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-chart-line text-primary-600 mr-2"></i>
        <span>Metrik</span>
    </a>
    
    <a href="/docs/jmeter" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-hammer text-primary-600 mr-2"></i>
        <span>JMeter</span>
    </a>
    
    <a href="/docs/aws" class="flex items-center justify-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
        <i class="fas fa-server text-primary-600 mr-2"></i>
        <span>AWS</span>
    </a>
</div>

@endsection

@section('scripts')
<script>
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
    const queryDetails = {!! json_encode($queryDetails ?? []) !!};
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
        
        // Update query details
        updateQueryDetails(val);
    }
    
    function updateQueryDetails(queryId) {
        const restEndpointElement = document.getElementById('restEndpointUrl');
        const graphqlQueryElement = document.getElementById('graphqlQuery');
        
        if (queryDetails[queryId]) {
            const details = queryDetails[queryId];
            
            // Update REST endpoint
            restEndpointElement.textContent = details.rest || 'Tidak tersedia';
            
            // Update GraphQL query
            graphqlQueryElement.textContent = details.graphql || 'Tidak tersedia';
        } else {
            restEndpointElement.textContent = 'Pilih skenario query untuk melihat detail endpoint';
            graphqlQueryElement.textContent = 'Pilih skenario query untuk melihat detail GraphQL query';
        }
    }

    // Mapping endpoint per repository
    const repoEndpointsMap = {
        'csurfer/gitsuggest': [
            'GET /users/:user',
            'GET /users/:user/starred',
            'GET /users/:user/following',
            'GET /users/:user/starred',
            'GET /search/repositories'
        ],
        'donnemartin/gitsome': [
            'GET /users/:user/followers',
            'GET /users/:user/following',
            'GET /repos/:owner/:repo/issues',
            'GET /users/:user/repos',
            'GET /repos/:owner/:repo/pulls',
            'GET /users/:user/repos',
            'GET /search/issues',
            'GET /search/repositories',
            'GET /users/:user/starred',
            'GET /users/:user',
            'GET /users/:user/repos'
        ],
        'guyzmo/git-repo': [
            'GET /users/:user/repos',
            'GET /users/:user/gists',
            'GET /repos/:owner/:repo',
            'GET /repos/:owner/:repo/pulls',
            'GET /repos/:owner/:repo'
        ],
        'donnemartin/viz': [
            'GET /users/:user',
            'GET /search/repositories',
            'GET /repos/:owner/:repo'
        ],
        'vdaubry/github-awards': [
            'GET /users/:user',
            'GET /users/:user/repos'
        ],
        'bibcure/arxivcheck': [
            'GET /query/:search_query'
        ],
        'karpathy/arxiv-sanity-preserver': [
            'GET /query/:search_query'
        ]
    };

    function updateRepoEndpoints() {
        const repoSelect = document.getElementById('repository');
        const endpointsDiv = document.getElementById('repoEndpoints');
        const endpointsList = document.getElementById('repoEndpointsList');
        const val = repoSelect.value;
        if (repoEndpointsMap[val]) {
            endpointsList.innerHTML = repoEndpointsMap[val].map(e => `<li>${e}</li>`).join('');
            endpointsDiv.classList.remove('hidden');
        } else {
            endpointsList.innerHTML = '';
            endpointsDiv.classList.add('hidden');
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        // Response Time Chart
        const responseTimeCtx = document.getElementById('responseTimeChart').getContext('2d');
        const responseTimeChart = new Chart(responseTimeCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chart_data['labels']) !!},
                datasets: [
                    {
                        label: 'REST API',
                        data: {!! json_encode($chart_data['rest_times']) !!},
                        backgroundColor: 'rgba(79, 70, 229, 0.6)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    },
                    {
                        label: 'GraphQL API',
                        data: {!! json_encode($chart_data['graphql_times']) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.6)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    title: {
                        display: true,
                        text: 'Rata-rata Waktu Respons per Query (ms)',
                        font: {
                            size: 14
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: ${context.raw.toFixed(2)} ms`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Waktu Respons (ms)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Query ID'
                        }
                    }
                }
            }
        });
        
        // Winner Distribution Chart
        const winnerDistributionCtx = document.getElementById('winnerDistributionChart').getContext('2d');
        const winnerDistributionChart = new Chart(winnerDistributionCtx, {
            type: 'pie',
            data: {
                labels: ['REST API', 'GraphQL API'],
                datasets: [{
                    data: [{{ $metrics['rest_wins'] }}, {{ $metrics['graphql_wins'] }}],
                    backgroundColor: [
                        'rgba(79, 70, 229, 0.6)',
                        'rgba(16, 185, 129, 0.6)'
                    ],
                    borderColor: [
                        'rgba(79, 70, 229, 1)',
                        'rgba(16, 185, 129, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });

        // Set initial description
        updateQueryDescription();
        // Update description on change
        document.getElementById('query_id').addEventListener('change', updateQueryDescription);

        // Set initial endpoints
        updateRepoEndpoints();
        // Update endpoints on change
        document.getElementById('repository').addEventListener('change', updateRepoEndpoints);
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
    const apiType = document.getElementById('api_type').value;
    const requestCount = document.getElementById('request_count').value;
    const cacheEnabled = apiType === 'integrated';
    
    // Disable button and show loading state
    const performanceTestBtn = document.getElementById('runPerformanceTestBtn');
    const performanceTestBtnText = document.getElementById('performanceTestBtnText');
    const performanceLoadingIcon = document.getElementById('performanceLoadingIcon');
    
    performanceTestBtn.disabled = true;
    performanceTestBtn.classList.add('opacity-75', 'cursor-not-allowed');
    performanceTestBtnText.textContent = 'Sedang Mengukur...';
    performanceLoadingIcon.classList.remove('hidden');
    
    try {
        const response = await fetch('/run-performance-test', {
            method: 'POST',
            body: JSON.stringify({
                query_id: queryId,
                api_type: apiType,
                request_count: parseInt(requestCount),
                cache: cacheEnabled
            }),
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            showPerformanceResultModal(result.data);
        } else {
            alert('Error: ' + (result.error || 'Terjadi kesalahan'));
        }
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
function showPerformanceResultModal(data) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full';
    modal.style.zIndex = '1000';
    
    // Calculate throughput and response time stats
    const totalTime = data.details?.total_time || 0;
    const responseTimes = data.details?.response_times || [];
    const minResponse = responseTimes.length > 0 ? Math.min(...responseTimes) : 0;
    const maxResponse = responseTimes.length > 0 ? Math.max(...responseTimes) : 0;
    const throughput = totalTime > 0 ? (data.request_count / (totalTime / 1000)) : 0;
    
    modal.innerHTML = `
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center border-b pb-3">
                <h3 class="text-xl font-semibold text-gray-900">Hasil Uji Performa</h3>
                <button onclick="closePerformanceModal()" class="text-gray-400 hover:text-gray-500">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="mt-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-2">Info Pengujian</h4>
                        <p><span class="text-gray-600">Query ID:</span> <span class="font-medium">${data.query_id}</span></p>
                        <p><span class="text-gray-600">Tipe API:</span> <span class="font-medium">${data.api_type.toUpperCase()}</span></p>
                        <p><span class="text-gray-600">Jumlah Request:</span> <span class="font-medium">${data.request_count}</span></p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-semibold text-gray-700 mb-2">Hasil Performa</h4>
                        <p><span class="text-gray-600">Rata-rata Waktu:</span> <span class="font-medium">${data.avg_response_time_ms.toFixed(2)} ms</span></p>
                        <p><span class="text-gray-600">CPU Usage:</span> <span class="font-medium">${data.cpu_usage_percent.toFixed(2)}%</span></p>
                        <p><span class="text-gray-600">Memory Usage:</span> <span class="font-medium">${data.memory_usage_percent.toFixed(2)}%</span></p>
                    </div>
                </div>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">Metrik Performa API Gateway</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <p><span class="text-blue-700">Total Waktu:</span> <span class="font-medium">${totalTime.toFixed(2)} ms</span></p>
                            <p><span class="text-blue-700">Throughput:</span> <span class="font-medium">${throughput.toFixed(2)} req/s</span></p>
                        </div>
                        <div>
                            <p><span class="text-blue-700">Min Response:</span> <span class="font-medium">${minResponse.toFixed(2)} ms</span></p>
                            <p><span class="text-blue-700">Max Response:</span> <span class="font-medium">${maxResponse.toFixed(2)} ms</span></p>
                        </div>
                    </div>
                    <p class="mt-2"><span class="text-blue-700">Pengujian Selesai:</span> <span class="font-medium">${new Date().toLocaleString()}</span></p>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closePerformanceModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Tutup
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Add close function to global scope
    window.closePerformanceModal = function() {
        document.body.removeChild(modal);
        delete window.closePerformanceModal;
    };
}

// Data real dari database - tidak ada data sample

// API Comparison Chart Variable
let apiComparisonChart;

// Initialize API Comparison Chart
function initializeComparisonCharts() {
    const ctx = document.getElementById('apiComparisonChart').getContext('2d');
    apiComparisonChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['REST API', 'GraphQL API', 'Integrated API'],
            datasets: [
                {
                    label: 'Response Time (ms)',
                    data: [0, 0, 0],
                    backgroundColor: 'rgba(59, 130, 246, 0.6)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    yAxisID: 'y'
                },
                {
                    label: 'CPU Usage (%)',
                    data: [0, 0, 0],
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: 'rgba(239, 68, 68, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                },
                {
                    label: 'Memory Usage (%)',
                    data: [0, 0, 0],
                    backgroundColor: 'rgba(251, 146, 60, 0.6)',
                    borderColor: 'rgba(251, 146, 60, 1)',
                    borderWidth: 2,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                title: {
                    display: true,
                    text: 'Perbandingan Performa API (Data Real dari Database)',
                    font: {
                        size: 16
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Response Time (ms)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'CPU (%) & Memory (%)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}

// Update comparison chart with real data from database
function updateComparisonCharts(data) {
    if (!apiComparisonChart) {
        console.error('Chart not initialized');
        return;
    }
    
    // Update dataset dengan data real dari database
    const responseTimes = [
        Number(data.rest.response_time) || 0,
        Number(data.graphql.response_time) || 0,
        Number(data.integrated.response_time) || 0
    ];

    const cpuUsage = [
        Number(data.rest.cpu_usage) || 0,
        Number(data.graphql.cpu_usage) || 0,
        Number(data.integrated.cpu_usage) || 0
    ];

    const memoryUsage = [
        Number(data.rest.memory_usage) || 0,
        Number(data.graphql.memory_usage) || 0,
        Number(data.integrated.memory_usage) || 0
    ];

    apiComparisonChart.data.datasets[0].data = responseTimes;
    apiComparisonChart.data.datasets[1].data = cpuUsage;
    apiComparisonChart.data.datasets[2].data = memoryUsage;

    const responseMax = Math.max(...responseTimes);
    const performanceMax = Math.max(...cpuUsage, ...memoryUsage);

    apiComparisonChart.options.scales.y.suggestedMax = responseMax > 0 ? responseMax * 1.2 : 10;

    if (performanceMax > 0) {
        apiComparisonChart.options.scales.y1.suggestedMax = performanceMax * 1.2;
        apiComparisonChart.options.scales.y1.max = undefined;
    } else {
        apiComparisonChart.options.scales.y1.suggestedMax = 10;
        apiComparisonChart.options.scales.y1.max = undefined;
    }
    
    apiComparisonChart.update();
    
    console.log('Chart updated with real data:', data);
}

// Historical data dari database - tidak digunakan untuk chart gabungan
function updateHistoricalCharts(historicalData) {
    // Data historis tersedia di console untuk debugging jika diperlukan
    console.log('Historical data from database:', historicalData);
    // Chart gabungan hanya menampilkan rata-rata, bukan data historis per waktu
}

// Fetch and update comparison data
async function loadComparisonData() {
    const querySelect = document.getElementById('comparison_query');
    const queryId = querySelect.value;
    const updateBtn = document.getElementById('updateComparisonBtn');

    // Check if query is empty
    if (!queryId || queryId === '') {
        showNotification('Belum ada data pengujian. Silakan jalankan pengujian terlebih dahulu.', 'warning');
        return;
    }

    // Show loading state
    updateBtn.disabled = true;
    updateBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memuat...';

    try {
        console.log('Loading comparison data for query:', queryId);
        const response = await fetch(`/api-comparison-data?query_id=${encodeURIComponent(queryId)}`);
        const result = await response.json();

        console.log('API Response:', result);

        if (result.success) {
            // Check if chart is initialized
            if (typeof apiComparisonChart === 'undefined') {
                console.error('Chart not initialized yet');
                showNotification('Chart belum siap, coba lagi', 'error');
                return;
            }

            // Update comparison chart with current data
            updateComparisonCharts(result.data);

            // Update historical charts if data available
            if (result.historical) {
                updateHistoricalCharts(result.historical);
            }

            // Update data info
            updateDataInfo(result, queryId);

            // Show success message
            if (result.has_real_data) {
                showNotification('Data perbandingan berhasil dimuat dari database!', 'success');
            } else {
                showNotification('Belum ada data pengujian untuk query ini. Silakan jalankan pengujian terlebih dahulu.', 'warning');
            }

        } else {
            console.error('Error loading comparison data:', result.error);
            showNotification('Gagal memuat data perbandingan: ' + result.error, 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        showNotification('Terjadi kesalahan saat memuat data perbandingan: ' + error.message, 'error');
    } finally {
        // Reset button state
        updateBtn.disabled = false;
        updateBtn.innerHTML = '<i class="fas fa-sync mr-1"></i> Tampilkan Perbandingan';
    }
}

// Function to update data info section
function updateDataInfo(result, queryId) {
    const dataInfo = document.getElementById('dataInfo');
    const dataSourceText = document.getElementById('dataSourceText');
    const currentQuery = document.getElementById('currentQuery');
    const lastUpdated = document.getElementById('lastUpdated');
    
    if (dataInfo && dataSourceText && currentQuery && lastUpdated) {
        dataSourceText.textContent = result.has_real_data ? 'Database Real' : 'Belum Ada Data';
        currentQuery.textContent = queryId;
        lastUpdated.textContent = new Date().toLocaleString();
        
        dataInfo.classList.remove('hidden');
    }
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

// Initialize comparison charts when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Chart.js to be fully loaded
    function checkChartJS() {
        if (typeof Chart !== 'undefined') {
            initializeComparisonCharts();

            // Check if there's data available
            const querySelect = document.getElementById('comparison_query');
            const hasData = querySelect.options.length > 0 && querySelect.value !== '';
            
            // Load initial data only if available
            if (hasData) {
                loadComparisonData();
            } else {
                console.log('No comparison data available yet. Please run a test first.');
            }

            // Add event listener for update button
            document.getElementById('updateComparisonBtn').addEventListener('click', loadComparisonData);
        } else {
            // Retry after 100ms if Chart.js not ready
            setTimeout(checkChartJS, 100);
        }
    }

    checkChartJS();
});

// Function to show test details modal
async function showTestDetails(queryId, testDate, apiType = 'all') {
    try {
        const response = await fetch(`/test-details?query_id=${encodeURIComponent(queryId)}&test_date=${encodeURIComponent(testDate)}&api_type=${encodeURIComponent(apiType)}`);
        const result = await response.json();
        
        if (result.success) {
            // Update modal title
            document.getElementById('testDetailsTitle').textContent = `Detail Pengujian ${queryId} - ${testDate}`;
            
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
                
                const cacheLabel = (test.cache_status || 'MISS').toUpperCase();
                const cacheClass =
                    cacheLabel === 'HIT' || cacheLabel === 'CACHE_USED'
                        ? 'bg-purple-100 text-purple-800'
                        : cacheLabel === 'MISS'
                            ? 'bg-gray-100 text-gray-800'
                            : cacheLabel === 'WINNER_REFRESH'
                                ? 'bg-indigo-100 text-indigo-800'
                                : cacheLabel === 'WINNER_ONLY'
                                    ? 'bg-blue-100 text-blue-800'
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

// Tab switching functionality for test history
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');

            // Remove active class from all buttons
            tabButtons.forEach(btn => {
                btn.classList.remove('border-primary-500', 'text-primary-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });

            // Add active class to clicked button
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-primary-500', 'text-primary-600');

            // Hide all tab contents
            tabContents.forEach(content => {
                content.classList.add('hidden');
            });

            // Show selected tab content
            const selectedContent = document.getElementById('tab-content-' + tabName);
            if (selectedContent) {
                selectedContent.classList.remove('hidden');
            }
        });
    });
});
</script>

<!-- Tambahkan Prism.js untuk syntax highlighting -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/themes/prism.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/prism.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/prism/1.24.1/components/prism-json.min.js"></script>
@endsection
