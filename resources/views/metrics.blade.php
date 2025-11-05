<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metrik Performa API Gateway</title>
    <!-- TODO: Untuk produksi, ganti dengan instalasi Tailwind CSS lokal -->
    <!-- https://tailwindcss.com/docs/installation -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Metrik Performa API Gateway</h1>
        
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Jalankan Pengujian Performa</h2>
            
            <form id="performanceTestForm" class="mb-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="query_id" class="block text-sm font-medium text-gray-700 mb-2">Skenario Query:</label>
                        <select id="query_id" name="query_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            @foreach($queries ?? [] as $id => $description)
                                <option value="{{ $id }}">{{ $id }}: {{ $description }}</option>
                            @endforeach
                            @if(empty($queries))
                                <option value="Q1">Q1: Mengambil nama dari 100 project teratas</option>
                                <option value="Q2">Q2: Mengambil pull request dari project</option>
                                <option value="Q3">Q3: Mengambil komentar dari pull request</option>
                                <option value="Q4">Q4: Mengambil nama dan URL dari 5 project teratas</option>
                                <option value="Q5">Q5: Mengambil data dari tujuh project</option>
                                <option value="Q6">Q6: Mengambil bug yang sudah ditutup</option>
                                <option value="Q7">Q7: Mengambil komentar dari bug</option>
                                <option value="Q8">Q8: Mengambil project Java</option>
                                <option value="Q9">Q9: Mengambil jumlah stars</option>
                                <option value="Q10">Q10: Mengambil repository dengan 1.000+ stars</option>
                                <option value="Q11">Q11: Mengambil jumlah commit</option>
                                <option value="Q12">Q12: Mengambil data dari delapan project</option>
                                <option value="Q13">Q13: Mengambil issue dengan tag bug</option>
                                <option value="Q14">Q14: Mengambil komentar dari issue</option>
                            @endif
                        </select>
                    </div>
                    
                    <div>
                        <label for="api_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe API:</label>
                        <select id="api_type" name="api_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="rest">REST API</option>
                            <option value="graphql">GraphQL API</option>
                            <option value="integrated">Integrated API</option>
                        </select>
                    </div>
                    
                    <div>
                        <label for="request_count" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Request:</label>
                        <input type="number" id="request_count" name="request_count" min="1" max="1000" value="100" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    </div>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Jalankan Pengujian
                </button>
            </form>
        </div>
        
        <div id="results" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Hasil Pengujian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Informasi Umum</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p><span class="font-medium">Query ID:</span> <span id="result-query-id"></span></p>
                        <p><span class="font-medium">API Type:</span> <span id="result-api-type"></span></p>
                        <p><span class="font-medium">Jumlah Request:</span> <span id="result-request-count"></span></p>
                        <p><span class="font-medium">Waktu Respons Rata-rata:</span> <span id="result-avg-response-time"></span> ms</p>
                        <p><span class="font-medium">CPU Usage:</span> <span id="result-cpu-usage"></span>%</p>
                        <p><span class="font-medium">Memory Usage:</span> <span id="result-memory-usage"></span>%</p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Waktu Respons</h3>
                    <div class="h-64">
                        <canvas id="responseTimeChart"></canvas>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Penggunaan Sumber Daya</h3>
                    <div class="h-64">
                        <canvas id="resourceUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <h2 class="text-xl font-semibold p-6">Riwayat Metrik Performa</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Query ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">API Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Request</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Respons (ms)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPU Usage (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Memory Usage (%)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($metrics as $metric)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $metric->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $metric->query_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ 
                                        $metric->api_type === 'rest' ? 'bg-green-100 text-green-800' : 
                                        ($metric->api_type === 'graphql' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800') 
                                    }}">
                                        {{ ucfirst($metric->api_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $metric->request_count }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($metric->avg_response_time_ms, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($metric->cpu_usage_percent, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($metric->memory_usage_percent, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $metric->created_at->format('d M Y, H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 bg-gray-50">
                {{ $metrics->links() }}
            </div>
        </div>
        
        <div class="mt-6 bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Perbandingan API</h2>
            
            <div class="mb-4">
                <label for="compare_query_id" class="block text-sm font-medium text-gray-700 mb-2">Pilih Query untuk Perbandingan:</label>
                <select id="compare_query_id" class="w-full md:w-1/3 px-3 py-2 border border-gray-300 rounded-md">
                    @foreach($queries ?? [] as $id => $description)
                        <option value="{{ $id }}">{{ $id }}: {{ $description }}</option>
                    @endforeach
                    @if(empty($queries))
                        <option value="Q1">Q1: Mengambil nama dari 100 project teratas</option>
                        <option value="Q2">Q2: Mengambil pull request dari project</option>
                        <option value="Q3">Q3: Mengambil komentar dari pull request</option>
                        <option value="Q4">Q4: Mengambil nama dan URL dari 5 project teratas</option>
                        <option value="Q5">Q5: Mengambil data dari tujuh project</option>
                        <option value="Q6">Q6: Mengambil bug yang sudah ditutup</option>
                        <option value="Q7">Q7: Mengambil komentar dari bug</option>
                        <option value="Q8">Q8: Mengambil project Java</option>
                        <option value="Q9">Q9: Mengambil jumlah stars</option>
                        <option value="Q10">Q10: Mengambil repository dengan 1.000+ stars</option>
                        <option value="Q11">Q11: Mengambil jumlah commit</option>
                        <option value="Q12">Q12: Mengambil data dari delapan project</option>
                        <option value="Q13">Q13: Mengambil issue dengan tag bug</option>
                        <option value="Q14">Q14: Mengambil komentar dari issue</option>
                    @endif
                </select>
                <button id="loadComparisonBtn" class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Tampilkan Perbandingan
                </button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Waktu Respons (ms)</h3>
                    <div class="h-64">
                        <canvas id="compareResponseTimeChart"></canvas>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">CPU Usage (%)</h3>
                    <div class="h-64">
                        <canvas id="compareCpuUsageChart"></canvas>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Memory Usage (%)</h3>
                    <div class="h-64">
                        <canvas id="compareMemoryUsageChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        let responseTimeChart = null;
        let resourceUsageChart = null;
        let compareResponseTimeChart = null;
        let compareCpuUsageChart = null;
        let compareMemoryUsageChart = null;
        
        $(document).ready(function() {
            // Form pengujian performa
            $('#performanceTestForm').on('submit', function(e) {
                e.preventDefault();
                
                const queryId = $('#query_id').val();
                const apiType = $('#api_type').val();
                const requestCount = $('#request_count').val();
                
                // Show loading state
                $('#performanceTestForm button').prop('disabled', true).text('Sedang Memproses...');
                
                $.ajax({
                    url: '/performance/test',
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        query_id: queryId,
                        api_type: apiType,
                        request_count: requestCount
                    },
                    success: function(response) {
                        displayResults(response.data);
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat menjalankan pengujian.');
                        console.error(xhr);
                    },
                    complete: function() {
                        $('#performanceTestForm button').prop('disabled', false).text('Jalankan Pengujian');
                    }
                });
            });
            
            // Load perbandingan API
            $('#loadComparisonBtn').on('click', function() {
                const queryId = $('#compare_query_id').val();
                
                $.ajax({
                    url: '/performance/metrics-data',
                    method: 'GET',
                    data: {
                        query_id: queryId
                    },
                    success: function(response) {
                        displayComparison(response);
                    },
                    error: function(xhr) {
                        alert('Terjadi kesalahan saat memuat data perbandingan.');
                        console.error(xhr);
                    }
                });
            });
        });
        
        function displayResults(data) {
            // Show results section
            $('#results').removeClass('hidden');
            
            // Update general info
            $('#result-query-id').text(data.query_id);
            $('#result-api-type').text(data.api_type.toUpperCase());
            $('#result-request-count').text(data.request_count);
            $('#result-avg-response-time').text(data.avg_response_time_ms.toFixed(2));
            $('#result-cpu-usage').text(data.cpu_usage_percent.toFixed(2));
            $('#result-memory-usage').text(data.memory_usage_percent.toFixed(2));
            
            // Update charts
            updateResponseTimeChart(data);
            updateResourceUsageChart(data);
        }
        
        function updateResponseTimeChart(data) {
            const ctx = document.getElementById('responseTimeChart').getContext('2d');
            
            if (responseTimeChart) {
                responseTimeChart.destroy();
            }
            
            // Pastikan data.details dan data.details.response_times tersedia
            const responseTimes = data.details && data.details.response_times ? data.details.response_times : [];
            
            responseTimeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: Array.from({ length: data.request_count }, (_, i) => i + 1),
                    datasets: [{
                        label: 'Waktu Respons (ms)',
                        data: responseTimes,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        function updateResourceUsageChart(data) {
            const ctx = document.getElementById('resourceUsageChart').getContext('2d');
            
            if (resourceUsageChart) {
                resourceUsageChart.destroy();
            }
            
            resourceUsageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['CPU Usage (%)', 'Memory Usage (%)'],
                    datasets: [{
                        label: 'Penggunaan Sumber Daya',
                        data: [data.cpu_usage_percent, data.memory_usage_percent],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
        
        function displayComparison(data) {
            updateCompareResponseTimeChart(data);
            updateCompareCpuUsageChart(data);
            updateCompareMemoryUsageChart(data);
        }
        
        function updateCompareResponseTimeChart(data) {
            const ctx = document.getElementById('compareResponseTimeChart').getContext('2d');
            
            if (compareResponseTimeChart) {
                compareResponseTimeChart.destroy();
            }
            
            compareResponseTimeChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Waktu Respons (ms)',
                        data: data.response_time,
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)',
                            'rgba(255, 159, 64, 0.2)'
                        ],
                        borderColor: [
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)',
                            'rgba(255, 159, 64, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        function updateCompareCpuUsageChart(data) {
            const ctx = document.getElementById('compareCpuUsageChart').getContext('2d');
            
            if (compareCpuUsageChart) {
                compareCpuUsageChart.destroy();
            }
            
            compareCpuUsageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'CPU Usage (%)',
                        data: data.cpu_usage,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
        
        function updateCompareMemoryUsageChart(data) {
            const ctx = document.getElementById('compareMemoryUsageChart').getContext('2d');
            
            if (compareMemoryUsageChart) {
                compareMemoryUsageChart.destroy();
            }
            
            compareMemoryUsageChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Memory Usage (%)',
                        data: data.memory_usage,
                        backgroundColor: [
                            'rgba(255, 159, 64, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 159, 64, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100
                        }
                    }
                }
            });
        }
    </script>
</body>
</html> 