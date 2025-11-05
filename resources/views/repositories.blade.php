<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repository Studi Kasus - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Pengujian Repository Studi Kasus</h1>
        
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Pilih Repository</h2>
            
            <form id="testForm" class="mb-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="repository_id" class="block text-sm font-medium text-gray-700 mb-2">Repository:</label>
                        <select id="repository_id" name="repository_id" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            @foreach($repositories as $repo)
                                <option value="{{ $repo['id'] }}">{{ $repo['name'] }}: {{ $repo['description'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label for="api_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe API:</label>
                        <select id="api_type" name="api_type" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                            <option value="rest">REST API</option>
                            <option value="graphql">GraphQL API</option>
                            <option value="integrated">API Terintegrasi</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="request_count" class="block text-sm font-medium text-gray-700 mb-2">Jumlah Permintaan:</label>
                    <input type="number" id="request_count" name="request_count" min="1" max="100" value="10" class="w-full px-3 py-2 border border-gray-300 rounded-md">
                    <p class="text-sm text-gray-500 mt-1">Masukkan jumlah permintaan (1-100)</p>
                </div>
                
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    Jalankan Pengujian
                </button>
            </form>
        </div>
        
        <div id="results" class="hidden bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Hasil Pengujian</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Informasi Repository</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p><span class="font-medium">Repository:</span> <span id="result-repo-name"></span></p>
                        <p><span class="font-medium">Deskripsi:</span> <span id="result-repo-desc"></span></p>
                        <p><span class="font-medium">GitHub URL:</span> <a id="result-repo-url" href="#" target="_blank" class="text-blue-600 hover:underline"></a></p>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Performa</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <p><span class="font-medium">Tipe API:</span> <span id="result-api-type"></span></p>
                        <p><span class="font-medium">Jumlah Permintaan:</span> <span id="result-request-count"></span></p>
                        <p><span class="font-medium">Waktu Respons Rata-rata:</span> <span id="result-avg-time"></span> ms</p>
                        <p><span class="font-medium">CPU Usage:</span> <span id="result-cpu"></span>%</p>
                        <p><span class="font-medium">Memory Usage:</span> <span id="result-memory"></span>%</p>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">REST API Response</h3>
                    <div class="bg-gray-50 p-4 rounded-md overflow-auto h-64">
                        <p><span class="font-medium">Status:</span> <span id="rest-status"></span></p>
                        <p><span class="font-medium">Waktu:</span> <span id="rest-time"></span> ms</p>
                        <pre id="rest-response" class="text-xs mt-2"></pre>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">GraphQL API Response</h3>
                    <div class="bg-gray-50 p-4 rounded-md overflow-auto h-64">
                        <p><span class="font-medium">Status:</span> <span id="graphql-status"></span></p>
                        <p><span class="font-medium">Waktu:</span> <span id="graphql-time"></span> ms</p>
                        <pre id="graphql-response" class="text-xs mt-2"></pre>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Riwayat Pengujian Repository</h2>
            
            @if($testResults->isEmpty())
                <p class="text-gray-500">Belum ada pengujian repository yang dilakukan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Repository</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe API</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Request</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Respons (ms)</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">CPU Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Memory Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($testResults as $result)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $result->query_id }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ strtoupper($result->api_type) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->request_count }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($result->avg_response_time_ms, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($result->cpu_usage_percent, 2) }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ number_format($result->memory_usage_percent, 2) }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $result->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $testResults->links() }}
                </div>
            @endif
        </div>
    </div>
    
    <script>
        $(document).ready(function() {
            $('#testForm').on('submit', function(e) {
                e.preventDefault();
                
                const repositoryId = $('#repository_id').val();
                const apiType = $('#api_type').val();
                const requestCount = $('#request_count').val();
                
                // Show loading state
                $('#testForm button').prop('disabled', true).text('Sedang Memproses...');
                
                $.ajax({
                    url: '/repositories/test',
                    method: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        repository_id: repositoryId,
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
                        $('#testForm button').prop('disabled', false).text('Jalankan Pengujian');
                    }
                });
            });
        });
        
        function displayResults(data) {
            // Show results section
            $('#results').removeClass('hidden');
            
            // Update repository info
            $('#result-repo-name').text(data.repository.name);
            $('#result-repo-desc').text(data.repository.description);
            $('#result-repo-url').text(data.repository.github_url).attr('href', data.repository.github_url);
            
            // Update performance info
            $('#result-api-type').text(data.api_type === 'rest' ? 'REST API' : 
                                     data.api_type === 'graphql' ? 'GraphQL API' : 'API Terintegrasi');
            $('#result-request-count').text(data.request_count);
            $('#result-avg-time').text(data.avg_response_time_ms.toFixed(2));
            $('#result-cpu').text(data.cpu_usage_percent.toFixed(2));
            $('#result-memory').text(data.memory_usage_percent.toFixed(2));
            
            // Update REST info if available
            if (data.api_type === 'rest' || data.api_type === 'integrated') {
                const restData = data.api_type === 'rest' ? data.last_response : data.last_response.rest;
                $('#rest-status').text(restData.succeeded ? 'Berhasil' : 'Gagal')
                    .removeClass('text-green-600 text-red-600')
                    .addClass(restData.succeeded ? 'text-green-600' : 'text-red-600');
                $('#rest-time').text(restData.response_time_ms.toFixed(2));
                $('#rest-response').text(JSON.stringify(restData.response, null, 2));
            } else {
                $('#rest-status').text('Tidak Diuji').removeClass('text-green-600 text-red-600');
                $('#rest-time').text('-');
                $('#rest-response').text('');
            }
            
            // Update GraphQL info if available
            if (data.api_type === 'graphql' || data.api_type === 'integrated') {
                const graphqlData = data.api_type === 'graphql' ? data.last_response : data.last_response.graphql;
                $('#graphql-status').text(graphqlData.succeeded ? 'Berhasil' : 'Gagal')
                    .removeClass('text-green-600 text-red-600')
                    .addClass(graphqlData.succeeded ? 'text-green-600' : 'text-red-600');
                $('#graphql-time').text(graphqlData.response_time_ms.toFixed(2));
                $('#graphql-response').text(JSON.stringify(graphqlData.response, null, 2));
            } else {
                $('#graphql-status').text('Tidak Diuji').removeClass('text-green-600 text-red-600');
                $('#graphql-time').text('-');
                $('#graphql-response').text('');
            }
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $('#results').offset().top - 20
            }, 500);
        }
    </script>
</body>
</html> 