<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Dashboard Pengujian - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Dokumentasi Dashboard Pengujian</h1>
        
        <div class="mb-4">
            <a href="/documentation" class="text-blue-600 hover:underline">← Kembali ke Dokumentasi</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tentang Dashboard Pengujian</h2>
            
            <p class="text-gray-600 mb-4">
                Dashboard Pengujian adalah antarmuka utama aplikasi API Gateway Testing yang memungkinkan pengguna untuk memilih dan menjalankan 
                skenario pengujian API. Dashboard ini dirancang untuk memberikan pengalaman pengguna yang intuitif dan informatif dalam melakukan 
                pengujian perbandingan antara API REST dan GraphQL.
            </p>
            
            <p class="text-gray-600">
                Melalui dashboard ini, pengguna dapat memilih satu dari 14 skenario query yang telah ditentukan, menjalankan pengujian, 
                dan melihat hasil perbandingan secara real-time. Hasil pengujian mencakup waktu respons, status keberhasilan, dan data 
                yang diterima dari kedua jenis API.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Pemilihan Query</h3>
                    <p class="text-gray-600">
                        Dashboard menyediakan dropdown untuk memilih skenario query dari 14 skenario yang telah ditentukan. 
                        Setiap skenario dirancang untuk menguji aspek tertentu dari API REST dan GraphQL, seperti over-fetching, 
                        under-fetching, dan kompleksitas query.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Eksekusi Pengujian</h3>
                    <p class="text-gray-600">
                        Dengan tombol "Jalankan Pengujian", pengguna dapat mengirimkan permintaan ke kedua API secara bersamaan 
                        dan melihat hasilnya. Sistem akan mengirimkan permintaan secara paralel ke endpoint REST dan GraphQL, 
                        mengukur waktu respons, dan menentukan API mana yang lebih cepat.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Visualisasi Hasil</h3>
                    <p class="text-gray-600">
                        Hasil pengujian ditampilkan dalam bentuk grafik perbandingan waktu respons antara REST API dan GraphQL API. 
                        Grafik batang yang interaktif memudahkan pengguna untuk melihat perbedaan performa antara kedua API.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Detail Respons</h3>
                    <p class="text-gray-600">
                        Pengguna dapat melihat detail respons dari kedua API, termasuk status keberhasilan, waktu respons dalam milidetik, 
                        dan data JSON yang diterima. Ini memungkinkan pengguna untuk membandingkan tidak hanya performa tetapi juga struktur 
                        dan konten data yang diterima.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Caching Otomatis</h3>
                    <p class="text-gray-600">
                        Dashboard mengimplementasikan sistem cache untuk mengoptimalkan pengujian berulang. Status cache (HIT atau MISS) 
                        ditampilkan untuk memberikan informasi apakah hasil pengujian diambil dari cache atau melalui permintaan baru.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Navigasi ke Fitur Lain</h3>
                    <p class="text-gray-600">
                        Dashboard menyediakan tautan ke fitur-fitur lain dalam aplikasi, seperti Metrik Performa, Riwayat Log, 
                        Panduan JMeter, Repository Studi Kasus, dan Deployment AWS EC2.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Cara Menggunakan Dashboard</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Pilih Skenario Query</p>
                    <p class="text-gray-600">
                        Dari dropdown "Skenario Query", pilih salah satu dari 14 skenario pengujian yang tersedia. 
                        Setiap skenario memiliki deskripsi singkat untuk membantu Anda memahami apa yang akan diuji.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Jalankan Pengujian</p>
                    <p class="text-gray-600">
                        Klik tombol "Jalankan Pengujian" untuk memulai proses pengujian. Sistem akan mengirimkan permintaan 
                        ke endpoint REST dan GraphQL secara bersamaan dan mengukur waktu respons masing-masing.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Analisis Hasil</p>
                    <p class="text-gray-600">
                        Setelah pengujian selesai, hasil akan ditampilkan dalam beberapa bagian:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Informasi Umum: Query ID, status cache, dan API pemenang</li>
                        <li>Grafik Perbandingan: Visualisasi waktu respons kedua API</li>
                        <li>Detail REST API: Status, waktu, dan respons JSON</li>
                        <li>Detail GraphQL API: Status, waktu, dan respons JSON</li>
                    </ul>
                </li>
                
                <li>
                    <p class="font-medium">Eksplorasi Fitur Lain</p>
                    <p class="text-gray-600">
                        Gunakan tautan di bagian bawah dashboard untuk mengakses fitur-fitur lain dalam aplikasi, 
                        seperti riwayat pengujian, metrik performa, dan panduan integrasi.
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
                        Dashboard diimplementasikan menggunakan DashboardController dengan dua metode utama:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">index()</code>: Menampilkan halaman dashboard dengan daftar query</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">startTest()</code>: Memproses permintaan pengujian dan mengembalikan hasil</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Service</h3>
                    <p class="text-gray-600">
                        Logika inti dashboard ditangani oleh ApiGatewayService yang mengimplementasikan:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Parallel Request Executor: Menggunakan Http::pool() Laravel</li>
                        <li>Performance Evaluator: Membandingkan waktu respons dan status keberhasilan</li>
                        <li>Cache Manager: Menggunakan Cache facade Laravel</li>
                        <li>Fallback Mechanism: Menangani kegagalan API</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">View</h3>
                    <p class="text-gray-600">
                        Tampilan dashboard diimplementasikan menggunakan Blade template engine Laravel dengan:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Tailwind CSS untuk styling</li>
                        <li>Chart.js untuk visualisasi grafik</li>
                        <li>jQuery untuk interaksi AJAX</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Route</h3>
                    <p class="text-gray-600">
                        Dashboard diakses melalui rute berikut:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
Route::get('/', [DashboardController::class, 'index']);
Route::post('/test', [DashboardController::class, 'startTest']);</pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 