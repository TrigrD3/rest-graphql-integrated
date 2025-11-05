<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Repository Studi Kasus - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Dokumentasi Repository Studi Kasus</h1>
        
        <div class="mb-4">
            <a href="/documentation" class="text-blue-600 hover:underline">← Kembali ke Dokumentasi</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tentang Repository Studi Kasus</h2>
            
            <p class="text-gray-600 mb-4">
                Fitur Repository Studi Kasus adalah bagian penting dari aplikasi API Gateway Testing yang memungkinkan pengujian 
                dengan 7 repository yang disebutkan dalam metodologi penelitian. Fitur ini dirancang untuk mengevaluasi performa 
                API REST dan GraphQL dalam konteks aplikasi dunia nyata yang mengakses data dari GitHub dan arXiv.
            </p>
            
            <p class="text-gray-600">
                Melalui fitur ini, pengguna dapat memilih salah satu dari 7 repository studi kasus, memilih tipe API yang ingin diuji 
                (REST, GraphQL, atau API terintegrasi), dan menjalankan pengujian dengan jumlah permintaan yang dapat disesuaikan. 
                Hasil pengujian mencakup waktu respons, penggunaan CPU, penggunaan memori, dan data yang diterima dari API.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Repository yang Didukung</h2>
            
            <p class="text-gray-600 mb-4">Fitur ini mendukung 7 repository studi kasus berikut:</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">donnemartin/viz</h3>
                    <p class="text-gray-600">Visualisasi repositori GitHub.</p>
                    <a href="https://github.com/donnemartin/viz" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">donnemartin/gitsome</h3>
                    <p class="text-gray-600">Command line interface untuk GitHub.</p>
                    <a href="https://github.com/donnemartin/gitsome" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">csurfer/gitsuggest</h3>
                    <p class="text-gray-600">Alat untuk menyarankan repositori GitHub.</p>
                    <a href="https://github.com/csurfer/gitsuggest" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">guyzmo/git-repo</h3>
                    <p class="text-gray-600">Command line interface untuk mengelola layanan Git.</p>
                    <a href="https://github.com/guyzmo/git-repo" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">vdaubry/github-awards</h3>
                    <p class="text-gray-600">Peringkat repositori GitHub.</p>
                    <a href="https://github.com/vdaubry/github-awards" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">bibcure/arxivcheck</h3>
                    <p class="text-gray-600">Alat untuk menghasilkan BIBTEX dari arXiv preprints.</p>
                    <a href="https://github.com/bibcure/arxivcheck" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-md">
                    <h3 class="text-lg font-medium mb-2">karpathy/arxiv-sanity-preserver</h3>
                    <p class="text-gray-600">Web interface untuk mencari submission arXiv.</p>
                    <a href="https://github.com/karpathy/arxiv-sanity-preserver" target="_blank" class="text-blue-600 hover:underline">GitHub Repository</a>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Pemilihan Repository</h3>
                    <p class="text-gray-600">
                        Pengguna dapat memilih salah satu dari 7 repository studi kasus yang tersedia. Setiap repository 
                        memiliki deskripsi singkat untuk membantu pengguna memahami tujuan dan fungsi repository tersebut.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Pemilihan Tipe API</h3>
                    <p class="text-gray-600">
                        Pengguna dapat memilih tipe API yang ingin diuji: REST API, GraphQL API, atau API terintegrasi 
                        (kombinasi keduanya). Ini memungkinkan perbandingan langsung antara ketiga pendekatan.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Jumlah Permintaan</h3>
                    <p class="text-gray-600">
                        Pengguna dapat mengatur jumlah permintaan yang akan dikirim (1-100) untuk mengukur performa 
                        rata-rata dan menguji konsistensi respons API dalam berbagai skenario beban.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Pengukuran Performa</h3>
                    <p class="text-gray-600">
                        Sistem mengukur dan menampilkan metrik performa kunci seperti waktu respons rata-rata, 
                        penggunaan CPU, dan penggunaan memori untuk setiap pengujian.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Visualisasi Respons</h3>
                    <p class="text-gray-600">
                        Respons dari API REST dan GraphQL ditampilkan dalam format JSON yang mudah dibaca, 
                        memungkinkan pengguna untuk membandingkan struktur data dan kelengkapan informasi.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Riwayat Pengujian</h3>
                    <p class="text-gray-600">
                        Sistem menyimpan dan menampilkan riwayat pengujian repository, memungkinkan pengguna 
                        untuk melihat dan membandingkan hasil pengujian sebelumnya.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Cara Menggunakan</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Akses Halaman Repository Studi Kasus</p>
                    <p class="text-gray-600">
                        Dari dashboard utama, klik tautan "Repository Studi Kasus" untuk mengakses halaman pengujian repository.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Pilih Repository</p>
                    <p class="text-gray-600">
                        Dari dropdown "Repository", pilih salah satu dari 7 repository studi kasus yang tersedia.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Pilih Tipe API</p>
                    <p class="text-gray-600">
                        Dari dropdown "Tipe API", pilih jenis API yang ingin diuji: REST API, GraphQL API, atau API Terintegrasi.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Atur Jumlah Permintaan</p>
                    <p class="text-gray-600">
                        Masukkan jumlah permintaan yang ingin dikirim (1-100) dalam field "Jumlah Permintaan".
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Jalankan Pengujian</p>
                    <p class="text-gray-600">
                        Klik tombol "Jalankan Pengujian" untuk memulai proses pengujian. Sistem akan mengirimkan permintaan 
                        sesuai dengan konfigurasi yang dipilih dan mengukur performa.
                    </p>
                </li>
                
                <li>
                    <p class="font-medium">Analisis Hasil</p>
                    <p class="text-gray-600">
                        Setelah pengujian selesai, hasil akan ditampilkan dalam beberapa bagian:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Informasi Repository: Nama, deskripsi, dan URL GitHub</li>
                        <li>Performa: Tipe API, jumlah permintaan, waktu respons rata-rata, CPU usage, dan memory usage</li>
                        <li>Detail REST API: Status, waktu, dan respons JSON (jika diuji)</li>
                        <li>Detail GraphQL API: Status, waktu, dan respons JSON (jika diuji)</li>
                    </ul>
                </li>
                
                <li>
                    <p class="font-medium">Lihat Riwayat Pengujian</p>
                    <p class="text-gray-600">
                        Di bagian bawah halaman, Anda dapat melihat riwayat pengujian repository yang telah dilakukan sebelumnya, 
                        termasuk repository, tipe API, jumlah permintaan, waktu respons, CPU usage, memory usage, dan tanggal pengujian.
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
                        Fitur Repository Studi Kasus diimplementasikan menggunakan RepositoryTestController dengan dua metode utama:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">index()</code>: Menampilkan halaman pengujian repository dengan daftar repository dan riwayat pengujian</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">runRepositoryTest()</code>: Memproses permintaan pengujian repository dan mengembalikan hasil</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Model</h3>
                    <p class="text-gray-600">
                        Hasil pengujian disimpan dalam dua model:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">PerformanceMetric</code>: Menyimpan data metrik performa seperti CPU usage, memory usage, dan waktu respons rata-rata</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">RequestLog</code>: Menyimpan log permintaan terakhir, termasuk respons dari API</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">View</h3>
                    <p class="text-gray-600">
                        Tampilan repository studi kasus diimplementasikan menggunakan Blade template engine Laravel dengan:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Tailwind CSS untuk styling</li>
                        <li>jQuery untuk interaksi AJAX</li>
                        <li>Tabel untuk menampilkan riwayat pengujian</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Route</h3>
                    <p class="text-gray-600">
                        Fitur Repository Studi Kasus diakses melalui rute berikut:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
Route::get('/repositories', [RepositoryTestController::class, 'index']);
Route::post('/repositories/test', [RepositoryTestController::class, 'runRepositoryTest']);</pre>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">API Integration</h3>
                    <p class="text-gray-600">
                        Fitur ini mengintegrasikan dengan API GitHub melalui:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>REST API: Menggunakan endpoint <code class="bg-gray-100 px-2 py-1 rounded">https://api.github.com/repos/{owner}/{repo}</code></li>
                        <li>GraphQL API: Menggunakan endpoint <code class="bg-gray-100 px-2 py-1 rounded">https://api.github.com/graphql</code> dengan query GraphQL yang sesuai</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 