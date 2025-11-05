<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panduan Integrasi JMeter</title>
    <!-- TODO: Untuk produksi, ganti dengan instalasi Tailwind CSS lokal -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Panduan Integrasi JMeter</h1>
        
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah-langkah Penggunaan JMeter</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Download dan Install Apache JMeter</p>
                    <p class="text-gray-600">Download Apache JMeter dari <a href="https://jmeter.apache.org/download_jmeter.cgi" target="_blank" class="text-blue-600 hover:underline">situs resmi</a> dan ikuti petunjuk instalasi.</p>
                </li>
                
                <li>
                    <p class="font-medium">Buka JMeter dan Buat Test Plan Baru</p>
                    <p class="text-gray-600">Jalankan JMeter dan buat Test Plan baru atau gunakan file konfigurasi JMX yang disediakan.</p>
                </li>
                
                <li>
                    <p class="font-medium">Konfigurasi Thread Group</p>
                    <p class="text-gray-600">Tambahkan Thread Group dengan klik kanan pada Test Plan > Add > Threads (Users) > Thread Group.</p>
                    <p class="text-gray-600">Sesuaikan jumlah thread (pengguna), ramp-up period, dan loop count sesuai kebutuhan pengujian.</p>
                </li>
                
                <li>
                    <p class="font-medium">Tambahkan HTTP Request</p>
                    <p class="text-gray-600">Klik kanan pada Thread Group > Add > Sampler > HTTP Request.</p>
                    <p class="text-gray-600">Konfigurasi HTTP Request dengan detail berikut:</p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Server Name or IP: <code class="bg-gray-100 px-2 py-1 rounded">localhost</code> (atau alamat server)</li>
                        <li>Port Number: <code class="bg-gray-100 px-2 py-1 rounded">8000</code> (atau port yang digunakan)</li>
                        <li>Protocol: <code class="bg-gray-100 px-2 py-1 rounded">http</code></li>
                        <li>Method: <code class="bg-gray-100 px-2 py-1 rounded">POST</code></li>
                        <li>Path: <code class="bg-gray-100 px-2 py-1 rounded">/api/test</code></li>
                    </ul>
                </li>
                
                <li>
                    <p class="font-medium">Tambahkan HTTP Header Manager</p>
                    <p class="text-gray-600">Klik kanan pada HTTP Request > Add > Config Element > HTTP Header Manager.</p>
                    <p class="text-gray-600">Tambahkan header berikut:</p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Content-Type: <code class="bg-gray-100 px-2 py-1 rounded">application/json</code></li>
                        <li>Accept: <code class="bg-gray-100 px-2 py-1 rounded">application/json</code></li>
                    </ul>
                </li>
                
                <li>
                    <p class="font-medium">Tambahkan Body Data</p>
                    <p class="text-gray-600">Pada HTTP Request, pilih tab "Body Data" dan masukkan JSON berikut:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
{
    "query_id": "Q1",
    "api_type": "rest",
    "request_count": 1,
    "jmeter_test_id": "jmeter-test-001"
}</pre>
                    <p class="text-gray-600 mt-2">Sesuaikan nilai parameter sesuai kebutuhan:</p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">query_id</code>: ID query yang ingin diuji (Q1-Q14)</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">api_type</code>: Tipe API (rest, graphql, atau integrated)</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">request_count</code>: Jumlah permintaan yang akan dijalankan</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">jmeter_test_id</code>: ID unik untuk pengujian JMeter</li>
                    </ul>
                </li>
                
                <li>
                    <p class="font-medium">Tambahkan Listener</p>
                    <p class="text-gray-600">Klik kanan pada Thread Group > Add > Listener > View Results Tree.</p>
                    <p class="text-gray-600">Tambahkan juga "Summary Report" untuk melihat ringkasan hasil pengujian.</p>
                </li>
                
                <li>
                    <p class="font-medium">Jalankan Test</p>
                    <p class="text-gray-600">Klik tombol "Start" (ikon play hijau) untuk menjalankan pengujian.</p>
                </li>
                
                <li>
                    <p class="font-medium">Analisis Hasil</p>
                    <p class="text-gray-600">Lihat hasil di "View Results Tree" dan "Summary Report".</p>
                    <p class="text-gray-600">Hasil juga akan tersimpan di database aplikasi dan dapat dilihat melalui halaman Logs.</p>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Contoh File Konfigurasi JMX</h2>
            
            <p class="mb-4">Anda dapat mengunduh file konfigurasi JMX contoh untuk memulai dengan cepat:</p>
            
            <div class="bg-gray-100 p-4 rounded">
                <p class="font-medium">api-gateway-test.jmx</p>
                <p class="text-gray-600 mt-2">File konfigurasi JMeter untuk pengujian API Gateway dengan berbagai skenario.</p>
                <a href="/jmx/api-gateway-test.jmx" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Download JMX</a>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Endpoint API</h2>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">/api/test</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">POST</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Endpoint untuk pengujian API dari JMeter</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <ul class="list-disc pl-4">
                                    <li>query_id (string, required)</li>
                                    <li>api_type (string, required)</li>
                                    <li>request_count (integer, optional)</li>
                                    <li>jmeter_test_id (string, optional)</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">/api/status</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">GET</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Endpoint untuk memeriksa status server</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Tidak ada parameter</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html> 