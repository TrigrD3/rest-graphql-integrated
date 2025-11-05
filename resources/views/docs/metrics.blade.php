@extends('layouts.app')

@section('title', 'Dokumentasi Metrik Performa')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <i class="fas fa-chart-line text-primary-600 text-3xl mr-4"></i>
        <h1 class="text-3xl font-bold text-gray-800">Dokumentasi Metrik Performa</h1>
    </div>
    
    <div class="mb-4">
        <a href="/documentation" class="inline-flex items-center text-primary-600 hover:text-primary-800">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Dokumentasi
        </a>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Tentang Metrik Performa</h2>
        
        <p class="text-gray-600 mb-4">
            Fitur Metrik Performa adalah bagian penting dari aplikasi API Gateway Testing yang memungkinkan pengukuran dan 
            analisis performa API REST dan GraphQL secara komprehensif. Fitur ini dirancang untuk mengumpulkan, 
            menyimpan, dan memvisualisasikan berbagai metrik performa seperti waktu respons, penggunaan CPU, 
            dan penggunaan memori.
        </p>
        
        <p class="text-gray-600">
            Melalui fitur ini, pengguna dapat menjalankan pengujian performa dengan jumlah permintaan yang dapat 
            disesuaikan, melihat hasil pengujian dalam bentuk grafik interaktif, dan menganalisis perbandingan 
            performa antara API REST, GraphQL, dan API terintegrasi.
        </p>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Metrik yang Diukur</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Waktu Respons</h3>
                <p class="text-gray-600">
                    Waktu yang dibutuhkan untuk menerima respons dari API, diukur dalam milidetik (ms). 
                    Metrik ini diukur secara internal oleh aplikasi Laravel untuk setiap permintaan ke API 
                    REST dan GraphQL.
                </p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Rata-rata Waktu Respons</h3>
                <p class="text-gray-600">
                    Rata-rata waktu respons dari sejumlah permintaan yang ditentukan, dihitung menggunakan rumus:
                </p>
                <div class="bg-white p-4 rounded mt-2 border border-gray-200">
                    <p class="font-mono">RTj = (1/n) * Σ {ts(respi) - ts(reqi)}</p>
                    <p class="font-mono ml-8">1≤i≤n</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Penggunaan CPU</h3>
                <p class="text-gray-600">
                    Persentase penggunaan CPU selama eksekusi API, dihitung menggunakan rumus:
                </p>
                <div class="bg-white p-4 rounded mt-2 border border-gray-200">
                    <p class="font-mono">CPU usage = (CPUused / CPUtotal) × 100</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Penggunaan Memori</h3>
                <p class="text-gray-600">
                    Persentase penggunaan memori selama eksekusi API, dihitung menggunakan rumus:
                </p>
                <div class="bg-white p-4 rounded mt-2 border border-gray-200">
                    <p class="font-mono">Memory usage = (Memused / Memtotal) × 100</p>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Throughput</h3>
                <p class="text-gray-600">
                    Jumlah permintaan yang dapat diproses per detik, dihitung dengan membagi jumlah 
                    permintaan dengan total waktu eksekusi.
                </p>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Tingkat Kesalahan</h3>
                <p class="text-gray-600">
                    Persentase permintaan yang gagal dari total permintaan yang dikirim, dihitung dengan 
                    membagi jumlah permintaan gagal dengan total permintaan dan dikalikan 100.
                </p>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-tachometer-alt text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Pengujian Performa</h3>
                    <p class="text-gray-600">
                        Pengguna dapat menjalankan pengujian performa dengan memilih skenario query, tipe API, 
                        dan jumlah permintaan yang akan dikirim. Sistem akan menjalankan pengujian dan mengukur 
                        metrik performa untuk setiap permintaan.
                    </p>
                </div>
            </div>
            
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-chart-bar text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Visualisasi Grafik</h3>
                    <p class="text-gray-600">
                        Hasil pengujian performa divisualisasikan dalam bentuk grafik interaktif yang menampilkan 
                        perbandingan waktu respons, penggunaan CPU, dan penggunaan memori antara API REST, 
                        GraphQL, dan API terintegrasi.
                    </p>
                </div>
            </div>
            
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-calculator text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Analisis Statistik</h3>
                    <p class="text-gray-600">
                        Sistem menyediakan analisis statistik dari hasil pengujian, termasuk nilai minimum, 
                        maksimum, rata-rata, dan median untuk setiap metrik performa.
                    </p>
                </div>
            </div>
            
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-code-branch text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Perbandingan API</h3>
                    <p class="text-gray-600">
                        Pengguna dapat membandingkan performa API REST, GraphQL, dan API terintegrasi secara 
                        langsung melalui grafik dan tabel perbandingan.
                    </p>
                </div>
            </div>
            
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-file-export text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Ekspor Data</h3>
                    <p class="text-gray-600">
                        Hasil pengujian performa dapat diekspor dalam format CSV atau JSON untuk analisis 
                        lebih lanjut atau pelaporan.
                    </p>
                </div>
            </div>
            
            <div class="flex">
                <div class="flex-shrink-0 h-12 w-12 flex items-center justify-center bg-primary-100 rounded-lg mr-4">
                    <i class="fas fa-history text-primary-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium mb-2 text-gray-800">Riwayat Pengujian</h3>
                    <p class="text-gray-600">
                        Sistem menyimpan dan menampilkan riwayat pengujian performa, memungkinkan pengguna 
                        untuk melihat dan membandingkan hasil pengujian sebelumnya.
                    </p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
        <h2 class="text-xl font-semibold mb-4">Cara Menggunakan</h2>
        
        <ol class="list-decimal pl-6 space-y-4">
            <li>
                <p class="font-medium text-gray-800">Akses Halaman Metrik Performa</p>
                <p class="text-gray-600">
                    Dari dashboard utama, klik tautan "Metrik Performa" untuk mengakses halaman pengujian performa.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Pilih Skenario Query</p>
                <p class="text-gray-600">
                    Dari dropdown "Skenario Query", pilih salah satu dari 14 skenario query yang tersedia.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Pilih Tipe API</p>
                <p class="text-gray-600">
                    Dari dropdown "Tipe API", pilih jenis API yang ingin diuji: REST API, GraphQL API, atau API Terintegrasi.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Atur Jumlah Permintaan</p>
                <p class="text-gray-600">
                    Masukkan jumlah permintaan yang ingin dikirim (1-100) dalam field "Jumlah Permintaan".
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Jalankan Pengujian</p>
                <p class="text-gray-600">
                    Klik tombol "Jalankan Pengujian" untuk memulai proses pengujian performa. Sistem akan mengirimkan 
                    permintaan sesuai dengan konfigurasi yang dipilih dan mengukur metrik performa.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Analisis Hasil</p>
                <p class="text-gray-600">
                    Setelah pengujian selesai, hasil akan ditampilkan dalam bentuk grafik dan tabel. Analisis 
                    perbandingan performa antara API REST, GraphQL, dan API terintegrasi.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Ekspor Hasil</p>
                <p class="text-gray-600">
                    Jika diperlukan, klik tombol "Ekspor" untuk mengunduh hasil pengujian dalam format CSV atau JSON.
                </p>
            </li>
            
            <li>
                <p class="font-medium text-gray-800">Lihat Riwayat Pengujian</p>
                <p class="text-gray-600">
                    Di bagian bawah halaman, Anda dapat melihat riwayat pengujian performa yang telah dilakukan sebelumnya.
                </p>
            </li>
        </ol>
    </div>
    
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-semibold mb-4">Implementasi Teknis</h2>
        
        <div class="space-y-6">
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Controller</h3>
                <p class="text-gray-600">
                    Fitur Metrik Performa diimplementasikan menggunakan PerformanceMetricController dengan metode utama:
                </p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li><code class="bg-white px-2 py-1 rounded border border-gray-200">index()</code>: Menampilkan halaman metrik performa</li>
                    <li><code class="bg-white px-2 py-1 rounded border border-gray-200">runPerformanceTest()</code>: Menjalankan pengujian performa dan mengukur metrik</li>
                    <li><code class="bg-white px-2 py-1 rounded border border-gray-200">getMetricsData()</code>: Mengambil data metrik untuk visualisasi</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Model</h3>
                <p class="text-gray-600">
                    Metrik performa disimpan dalam model PerformanceMetric dengan struktur:
                </p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>query_id: ID query yang diuji</li>
                    <li>api_type: Tipe API (rest, graphql, integrated)</li>
                    <li>cpu_usage_percent: Persentase penggunaan CPU</li>
                    <li>memory_usage_percent: Persentase penggunaan memori</li>
                    <li>request_count: Jumlah permintaan</li>
                    <li>avg_response_time_ms: Waktu respons rata-rata</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">View</h3>
                <p class="text-gray-600">
                    Tampilan metrik performa diimplementasikan menggunakan Blade template engine Laravel dengan:
                </p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Tailwind CSS untuk styling</li>
                    <li>Chart.js untuk visualisasi grafik</li>
                    <li>jQuery untuk interaksi AJAX</li>
                </ul>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Route</h3>
                <p class="text-gray-600">
                    Fitur Metrik Performa diakses melalui rute berikut:
                </p>
                <pre class="bg-white p-4 rounded mt-2 overflow-x-auto border border-gray-200">
Route::get('/performance', [PerformanceMetricController::class, 'index']);
Route::post('/performance/test', [PerformanceMetricController::class, 'runPerformanceTest']);
Route::get('/performance/metrics-data', [PerformanceMetricController::class, 'getMetricsData']);</pre>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <h3 class="text-lg font-medium mb-2 text-primary-700">Pengukuran CPU dan Memory</h3>
                <p class="text-gray-600">
                    Pengukuran CPU dan memory diimplementasikan menggunakan:
                </p>
                <ul class="list-disc pl-6 mt-2 space-y-1">
                    <li>Di Windows: Windows Management Instrumentation (WMI)</li>
                    <li>Di Linux: File /proc/stat dan /proc/meminfo</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection 