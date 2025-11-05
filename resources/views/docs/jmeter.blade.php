<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Integrasi JMeter - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Dokumentasi Integrasi JMeter</h1>
        
        <div class="mb-4">
            <a href="/documentation" class="text-blue-600 hover:underline">← Kembali ke Dokumentasi</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tentang Integrasi JMeter</h2>
            
            <p class="text-gray-600 mb-4">
                Fitur Integrasi JMeter memungkinkan pengujian beban (load testing) pada API Gateway Testing menggunakan 
                Apache JMeter. Fitur ini dirancang untuk mengevaluasi performa API REST dan GraphQL dalam skenario beban tinggi, 
                membantu mengidentifikasi bottleneck, dan mengukur kapasitas sistem dalam menangani permintaan bersamaan.
            </p>
            
            <p class="text-gray-600">
                Dengan integrasi ini, pengguna dapat menjalankan pengujian beban yang terstruktur dan terukur, menganalisis 
                metrik performa seperti waktu respons, throughput, dan tingkat kesalahan, serta membandingkan performa 
                API REST dan GraphQL dalam berbagai skenario beban.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Prasyarat</h2>
            
            <p class="text-gray-600 mb-4">Sebelum memulai pengujian dengan JMeter, pastikan Anda memiliki:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li>Apache JMeter (versi 5.4 atau lebih baru) diinstal di komputer lokal</li>
                <li>Aplikasi API Gateway Testing berjalan di server lokal atau AWS EC2</li>
                <li>File konfigurasi JMX yang disediakan oleh aplikasi</li>
                <li>Pemahaman dasar tentang pengujian beban dan Apache JMeter</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Endpoint API Khusus</h3>
                    <p class="text-gray-600">
                        Endpoint API khusus yang dioptimalkan untuk pengujian beban, memungkinkan JMeter 
                        untuk mengirimkan permintaan ke API REST dan GraphQL.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">File Konfigurasi JMX</h3>
                    <p class="text-gray-600">
                        File konfigurasi JMeter (.jmx) yang telah dikonfigurasi sebelumnya untuk berbagai 
                        skenario pengujian, memudahkan pengguna untuk memulai pengujian dengan cepat.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Skenario Pengujian Beban</h3>
                    <p class="text-gray-600">
                        Berbagai skenario pengujian beban yang telah ditentukan, termasuk pengujian beban 
                        konstan, pengujian ramp-up, dan pengujian spike untuk mengevaluasi performa sistem 
                        dalam berbagai kondisi.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Pengujian Konkurensi</h3>
                    <p class="text-gray-600">
                        Kemampuan untuk menguji performa API dengan berbagai tingkat konkurensi, mulai dari 
                        beberapa pengguna hingga ratusan pengguna bersamaan.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Pengumpulan Metrik</h3>
                    <p class="text-gray-600">
                        Pengumpulan dan pelaporan metrik performa seperti waktu respons, throughput, 
                        tingkat kesalahan, dan penggunaan sumber daya server.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Visualisasi Hasil</h3>
                    <p class="text-gray-600">
                        Kemampuan untuk memvisualisasikan hasil pengujian beban melalui grafik dan laporan 
                        yang disediakan oleh JMeter, memudahkan analisis dan perbandingan performa.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Endpoint API untuk JMeter</h2>
            
            <p class="text-gray-600 mb-4">
                Aplikasi API Gateway Testing menyediakan endpoint API khusus untuk pengujian dengan JMeter:
            </p>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Endpoint</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">/api/test</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">POST</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Endpoint utama untuk pengujian API dengan JMeter</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <ul class="list-disc pl-6">
                                    <li>query_id: ID query yang akan diuji</li>
                                    <li>api_type: Tipe API (rest, graphql, integrated)</li>
                                </ul>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">/api/status</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">GET</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Endpoint untuk memeriksa status API</td>
                            <td class="px-6 py-4 text-sm text-gray-500">Tidak ada</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Cara Menggunakan JMeter dengan API Gateway Testing</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Unduh dan Instal Apache JMeter</p>
                    <p class="text-gray-600">
                        Unduh Apache JMeter dari <a href="https://jmeter.apache.org/download_jmeter.cgi" target="_blank" class="text-blue-600 hover:underline">situs resmi</a> 
                        dan ikuti instruksi instalasi untuk sistem operasi Anda.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Unduh File Konfigurasi JMX</p>
                    <p class="text-gray-600">
                        Dari halaman panduan JMeter di aplikasi API Gateway Testing, unduh file konfigurasi JMX yang disediakan.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Buka File JMX di JMeter</p>
                    <p class="text-gray-600">
                        Buka Apache JMeter, lalu pilih File > Open dan pilih file JMX yang telah diunduh.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Konfigurasi Parameter Pengujian</p>
                    <p class="text-gray-600">
                        Sesuaikan parameter pengujian seperti jumlah thread (pengguna virtual), ramp-up period, 
                        dan loop count sesuai dengan kebutuhan pengujian Anda.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Konfigurasi URL Server</p>
                    <p class="text-gray-600">
                        Pastikan URL server di HTTP Request samplers menunjuk ke URL yang benar dari aplikasi API Gateway Testing Anda.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Jalankan Pengujian</p>
                    <p class="text-gray-600">
                        Klik tombol Run di JMeter untuk memulai pengujian beban. JMeter akan mengirimkan permintaan ke API 
                        sesuai dengan konfigurasi yang telah ditentukan.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Analisis Hasil</p>
                    <p class="text-gray-600">
                        Setelah pengujian selesai, analisis hasil menggunakan berbagai listener yang tersedia di JMeter, 
                        seperti View Results Tree, Summary Report, dan Aggregate Report.
                    </p>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Contoh Skenario Pengujian</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">1. Pengujian Beban Konstan</h3>
                    <p class="text-gray-600">
                        Pengujian dengan jumlah pengguna konstan untuk mengevaluasi performa sistem dalam kondisi beban stabil.
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Thread Group: 50 pengguna</li>
                        <li>Ramp-up Period: 10 detik</li>
                        <li>Loop Count: 10</li>
                        <li>Total Permintaan: 500</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">2. Pengujian Ramp-up</h3>
                    <p class="text-gray-600">
                        Pengujian dengan jumlah pengguna yang meningkat secara bertahap untuk mengevaluasi 
                        bagaimana sistem menangani peningkatan beban.
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Thread Group: 100 pengguna</li>
                        <li>Ramp-up Period: 60 detik</li>
                        <li>Loop Count: 5</li>
                        <li>Total Permintaan: 500</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">3. Pengujian Spike</h3>
                    <p class="text-gray-600">
                        Pengujian dengan lonjakan pengguna yang tiba-tiba untuk mengevaluasi bagaimana sistem 
                        menangani lonjakan beban yang ekstrem.
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Thread Group: 200 pengguna</li>
                        <li>Ramp-up Period: 5 detik</li>
                        <li>Loop Count: 2</li>
                        <li>Total Permintaan: 400</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">4. Pengujian Daya Tahan</h3>
                    <p class="text-gray-600">
                        Pengujian dengan beban moderat selama periode waktu yang lebih lama untuk mengevaluasi 
                        stabilitas sistem dalam jangka panjang.
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Thread Group: 20 pengguna</li>
                        <li>Ramp-up Period: 10 detik</li>
                        <li>Loop Count: 50</li>
                        <li>Total Permintaan: 1000</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Implementasi Teknis</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium mb-2">Endpoint API</h3>
                    <p class="text-gray-600">
                        Endpoint API untuk JMeter diimplementasikan di ApiController dengan metode berikut:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
public function test(Request $request)
{
    $request->validate([
        'query_id' => 'required|string',
        'api_type' => 'required|in:rest,graphql,integrated'
    ]);
    
    $queryId = $request->input('query_id');
    $apiType = $request->input('api_type');
    
    // Proses pengujian API
    $result = $this->apiGatewayService->executeTest($queryId, $apiType);
    
    return response()->json($result);
}</pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Route</h3>
                    <p class="text-gray-600">
                        Route untuk endpoint API JMeter didefinisikan di routes/web.php:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
// Route untuk integrasi dengan JMeter
Route::post('/api/test', [ApiController::class, 'test']);
Route::get('/api/status', function () {
    return response()->json([
        'status' => 'online',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version', '1.0.0')
    ]);
});</pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">File Konfigurasi JMX</h3>
                    <p class="text-gray-600">
                        File konfigurasi JMX disimpan di direktori public/jmeter dan dapat diunduh dari halaman panduan JMeter.
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>api_gateway_load_test.jmx: Konfigurasi untuk pengujian beban</li>
                        <li>api_gateway_stress_test.jmx: Konfigurasi untuk pengujian stress</li>
                        <li>api_gateway_spike_test.jmx: Konfigurasi untuk pengujian spike</li>
                        <li>api_gateway_endurance_test.jmx: Konfigurasi untuk pengujian daya tahan</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Panduan JMeter</h3>
                    <p class="text-gray-600">
                        Panduan penggunaan JMeter diimplementasikan sebagai view Blade di resources/views/jmeter-guide.blade.php 
                        dan dapat diakses melalui URL /jmeter-guide.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 