<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Riwayat Log - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Dokumentasi Riwayat Log</h1>
        
        <div class="mb-4">
            <a href="/documentation" class="text-blue-600 hover:underline">← Kembali ke Dokumentasi</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tentang Riwayat Log</h2>
            
            <p class="text-gray-600 mb-4">
                Fitur Riwayat Log adalah komponen penting dari aplikasi API Gateway Testing yang menyimpan dan menampilkan 
                catatan lengkap dari semua pengujian API yang telah dilakukan. Fitur ini dirancang untuk memudahkan penelusuran, 
                analisis, dan perbandingan hasil pengujian sebelumnya.
            </p>
            
            <p class="text-gray-600">
                Dengan fitur ini, pengguna dapat melihat detail setiap pengujian, termasuk query yang digunakan, 
                endpoint yang diuji, status cache, API pemenang, waktu respons, status keberhasilan, dan data respons. 
                Riwayat log ini menjadi sumber data berharga untuk analisis jangka panjang dan pelaporan.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Pencatatan Otomatis</h3>
                    <p class="text-gray-600">
                        Setiap pengujian API secara otomatis dicatat ke dalam basis data, termasuk semua parameter 
                        dan hasil pengujian, tanpa memerlukan intervensi pengguna.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Tampilan Tabel</h3>
                    <p class="text-gray-600">
                        Riwayat log ditampilkan dalam bentuk tabel yang informatif dengan kolom-kolom yang relevan, 
                        seperti ID Query, Endpoint, Cache Status, API Pemenang, Waktu Respons, dan Tanggal.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Paginasi</h3>
                    <p class="text-gray-600">
                        Sistem mengimplementasikan paginasi untuk menangani jumlah log yang besar, memastikan 
                        performa halaman tetap optimal bahkan dengan ribuan catatan log.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Filter dan Pencarian</h3>
                    <p class="text-gray-600">
                        Pengguna dapat memfilter dan mencari log berdasarkan berbagai kriteria, seperti ID Query, 
                        Endpoint, API Pemenang, dan rentang tanggal, untuk menemukan catatan yang relevan dengan cepat.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Detail Log</h3>
                    <p class="text-gray-600">
                        Setiap log memiliki halaman detail yang menampilkan informasi lengkap tentang pengujian, 
                        termasuk respons JSON lengkap dari API REST dan GraphQL.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Ekspor Data</h3>
                    <p class="text-gray-600">
                        Pengguna dapat mengekspor data log dalam format CSV atau JSON untuk analisis lebih lanjut 
                        atau pelaporan di luar aplikasi.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Struktur Data Log</h2>
            
            <p class="text-gray-600 mb-4">
                Setiap catatan log menyimpan informasi berikut:
            </p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Field</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">id</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Integer</td>
                            <td class="px-6 py-4 text-sm text-gray-500">ID unik untuk setiap catatan log</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">query_id</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">String</td>
                            <td class="px-6 py-4 text-sm text-gray-500">ID Query yang diuji (Q1, Q2, dll.)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">endpoint</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">String</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Endpoint yang diuji</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">cache_status</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">String</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Status cache (HIT atau MISS)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">winner_api</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">String</td>
                            <td class="px-6 py-4 text-sm text-gray-500">API pemenang (rest, graphql, atau none)</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">rest_response_time_ms</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Integer</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Waktu respons REST API dalam milidetik</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">graphql_response_time_ms</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Integer</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Waktu respons GraphQL API dalam milidetik</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">rest_succeeded</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Boolean</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Status keberhasilan REST API</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">graphql_succeeded</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Boolean</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Status keberhasilan GraphQL API</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">response_body</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Text</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Data respons dalam format JSON</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">created_at</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Timestamp</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Waktu pencatatan log</td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">updated_at</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Timestamp</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Waktu pembaruan log</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Cara Menggunakan</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Akses Halaman Riwayat Log</p>
                    <p class="text-gray-600">
                        Dari dashboard utama, klik tautan "Riwayat Log" untuk mengakses halaman riwayat log.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Jelajahi Log</p>
                    <p class="text-gray-600">
                        Gunakan kontrol paginasi di bagian bawah tabel untuk menjelajahi halaman log.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Filter dan Cari</p>
                    <p class="text-gray-600">
                        Gunakan opsi filter dan pencarian di bagian atas tabel untuk menemukan log tertentu 
                        berdasarkan kriteria yang Anda tentukan.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Lihat Detail</p>
                    <p class="text-gray-600">
                        Klik tombol "Detail" pada baris log untuk melihat informasi lengkap tentang pengujian, 
                        termasuk respons JSON dari API.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Ekspor Data</p>
                    <p class="text-gray-600">
                        Klik tombol "Ekspor" di bagian atas tabel untuk mengunduh data log dalam format CSV atau JSON.
                    </p>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Implementasi Teknis</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium mb-2">Controller</h3>
                    <p class="text-gray-600">
                        Fitur Riwayat Log diimplementasikan menggunakan DashboardController dengan metode:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">showLogs()</code>: Menampilkan halaman riwayat log dengan paginasi</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">getLogDetail($id)</code>: Menampilkan detail log tertentu</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">exportLogs($format)</code>: Mengekspor data log dalam format tertentu</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Model</h3>
                    <p class="text-gray-600">
                        Data log disimpan dalam model RequestLog dengan struktur yang sesuai dengan tabel request_logs:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
Schema::create('request_logs', function (Blueprint $table) {
    $table->id();
    $table->string('query_id');
    $table->string('endpoint');
    $table->string('cache_status');
    $table->string('winner_api')->nullable();
    $table->integer('rest_response_time_ms')->nullable();
    $table->integer('graphql_response_time_ms')->nullable();
    $table->boolean('rest_succeeded');
    $table->boolean('graphql_succeeded');
    $table->longText('response_body')->nullable();
    $table->timestamps();
});</pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">View</h3>
                    <p class="text-gray-600">
                        Tampilan riwayat log diimplementasikan menggunakan Blade template engine Laravel dengan:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Tailwind CSS untuk styling</li>
                        <li>Komponen tabel dengan paginasi</li>
                        <li>Modal untuk menampilkan detail log</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Route</h3>
                    <p class="text-gray-600">
                        Fitur Riwayat Log diakses melalui rute berikut:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
Route::get('/logs', [DashboardController::class, 'showLogs']);
Route::get('/logs/{id}', [DashboardController::class, 'getLogDetail']);
Route::get('/logs/export/{format}', [DashboardController::class, 'exportLogs']);</pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 