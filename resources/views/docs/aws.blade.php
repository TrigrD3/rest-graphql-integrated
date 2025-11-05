<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Deployment AWS EC2 - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Dokumentasi Deployment AWS EC2</h1>
        
        <div class="mb-4">
            <a href="/documentation" class="text-blue-600 hover:underline">← Kembali ke Dokumentasi</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Tentang Deployment AWS EC2</h2>
            
            <p class="text-gray-600 mb-4">
                Fitur Deployment AWS EC2 menyediakan panduan langkah demi langkah dan script otomatisasi untuk men-deploy 
                aplikasi API Gateway Testing ke lingkungan cloud Amazon Web Services (AWS) EC2 Free Tier. Fitur ini dirancang 
                untuk memudahkan proses deployment aplikasi dari lingkungan pengembangan lokal ke server produksi.
            </p>
            
            <p class="text-gray-600">
                Dengan mengikuti panduan dan menggunakan script yang disediakan, pengguna dapat dengan cepat menyiapkan 
                lingkungan server yang diperlukan, mengkonfigurasi dependensi, dan men-deploy aplikasi Laravel ke instance 
                EC2 dengan konfigurasi yang optimal untuk performa dan keamanan.
            </p>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Prasyarat</h2>
            
            <p class="text-gray-600 mb-4">Sebelum memulai proses deployment, pastikan Anda memiliki:</p>
            
            <ul class="list-disc pl-6 space-y-2">
                <li>Akun AWS (Free Tier)</li>
                <li>Key Pair AWS EC2 (untuk koneksi SSH)</li>
                <li>Git diinstal di komputer lokal</li>
                <li>Composer diinstal di komputer lokal</li>
                <li>Akses terminal atau command prompt</li>
                <li>Repository aplikasi API Gateway Testing</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Fitur Utama</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">Panduan Step-by-Step</h3>
                    <p class="text-gray-600">
                        Panduan terperinci untuk setiap langkah proses deployment, mulai dari membuat instance EC2 
                        hingga mengkonfigurasi server web dan database.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Script Otomatisasi</h3>
                    <p class="text-gray-600">
                        Script bash yang dapat diunduh untuk mengotomatisasi proses deployment, termasuk instalasi 
                        dependensi, konfigurasi aplikasi, dan pengaturan server web.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Nginx</h3>
                    <p class="text-gray-600">
                        Template konfigurasi Nginx yang optimal untuk aplikasi Laravel, dengan pengaturan keamanan 
                        dan performa yang direkomendasikan.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Database</h3>
                    <p class="text-gray-600">
                        Panduan untuk mengatur dan mengkonfigurasi database MySQL di server EC2, termasuk 
                        pembuatan database dan pengguna dengan hak akses yang sesuai.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Redis</h3>
                    <p class="text-gray-600">
                        Instruksi untuk mengatur Redis sebagai sistem cache untuk mengoptimalkan performa aplikasi 
                        dan mempercepat waktu respons API.
                    </p>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Supervisor (Opsional)</h3>
                    <p class="text-gray-600">
                        Panduan opsional untuk mengatur Supervisor untuk mengelola queue worker Laravel, 
                        yang berguna jika aplikasi menggunakan fitur antrian.
                    </p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah-langkah Deployment</h2>
            
            <div class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium mb-2">1. Membuat Instance EC2</h3>
                    <p class="text-gray-600 mb-2">
                        Langkah pertama adalah membuat instance EC2 di AWS Console:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Login ke AWS Console</li>
                        <li>Buka EC2 Dashboard</li>
                        <li>Luncurkan instance baru dengan AMI Ubuntu Server 20.04 LTS</li>
                        <li>Pilih tipe instance t2.micro (Free Tier eligible)</li>
                        <li>Konfigurasi security group dengan port 22 (SSH), 80 (HTTP), dan 443 (HTTPS)</li>
                        <li>Pilih atau buat key pair untuk koneksi SSH</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">2. Menyiapkan Server</h3>
                    <p class="text-gray-600 mb-2">
                        Setelah instance EC2 berjalan, siapkan server dengan menginstal dependensi yang diperlukan:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Koneksi ke instance EC2 menggunakan SSH</li>
                        <li>Update sistem operasi</li>
                        <li>Instal Nginx, PHP 8.0, MySQL, Redis, dan Git</li>
                        <li>Konfigurasi MySQL dan buat database</li>
                        <li>Instal Composer</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">3. Deploy Aplikasi</h3>
                    <p class="text-gray-600 mb-2">
                        Selanjutnya, deploy aplikasi API Gateway Testing ke server:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Clone repository dari GitHub</li>
                        <li>Atur kepemilikan direktori</li>
                        <li>Instal dependensi PHP dengan Composer</li>
                        <li>Konfigurasi file .env</li>
                        <li>Generate application key</li>
                        <li>Jalankan migrasi database</li>
                        <li>Optimasi aplikasi Laravel</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">4. Konfigurasi Nginx</h3>
                    <p class="text-gray-600 mb-2">
                        Konfigurasi server web Nginx untuk melayani aplikasi Laravel:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Buat konfigurasi Nginx untuk aplikasi</li>
                        <li>Aktifkan konfigurasi</li>
                        <li>Restart Nginx</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">5. Konfigurasi Supervisor (Opsional)</h3>
                    <p class="text-gray-600 mb-2">
                        Jika aplikasi menggunakan queue worker, konfigurasi Supervisor:
                    </p>
                    <ul class="list-disc pl-6 space-y-2">
                        <li>Instal Supervisor</li>
                        <li>Buat konfigurasi untuk queue worker</li>
                        <li>Reload Supervisor dan mulai worker</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Script Otomatisasi</h2>
            
            <p class="text-gray-600 mb-4">
                Untuk mempermudah proses deployment, kami menyediakan script bash yang dapat diunduh dan dijalankan di instance EC2:
            </p>
            
            <div class="bg-gray-50 p-4 rounded-md mb-4">
                <p class="font-medium">deploy.sh</p>
                <p class="text-gray-600 mt-2">Script bash untuk otomatisasi deployment ke AWS EC2.</p>
                <a href="/scripts/deploy.sh" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" download>Download Script</a>
            </div>
            
            <p class="text-gray-600 mb-2">Untuk menggunakan script ini:</p>
            <ol class="list-decimal pl-6 space-y-2">
                <li>Unduh script ke instance EC2</li>
                <li>Berikan izin eksekusi: <code class="bg-gray-100 px-2 py-1 rounded">chmod +x deploy.sh</code></li>
                <li>Jalankan script: <code class="bg-gray-100 px-2 py-1 rounded">./deploy.sh</code></li>
            </ol>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4">
                <p class="text-yellow-700">
                    <span class="font-bold">Catatan:</span> Sebelum menjalankan script, pastikan untuk mengedit URL repository GitHub dan DNS publik EC2 di dalam script.
                </p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Implementasi Teknis</h2>
            
            <div class="space-y-4">
                <div>
                    <h3 class="text-lg font-medium mb-2">Script Deployment</h3>
                    <p class="text-gray-600">
                        Script deployment diimplementasikan sebagai file bash yang melakukan langkah-langkah berikut:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Update sistem dan instal dependensi</li>
                        <li>Konfigurasi direktori aplikasi</li>
                        <li>Instal dependensi PHP</li>
                        <li>Konfigurasi environment</li>
                        <li>Migrasi database</li>
                        <li>Optimasi Laravel</li>
                        <li>Konfigurasi Nginx</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Nginx</h3>
                    <p class="text-gray-600">
                        Konfigurasi Nginx diimplementasikan dengan pengaturan berikut:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Server block untuk aplikasi Laravel</li>
                        <li>Header keamanan (X-Frame-Options, X-XSS-Protection, X-Content-Type-Options)</li>
                        <li>Konfigurasi PHP-FPM</li>
                        <li>Penanganan URL rewriting untuk Laravel</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Optimasi Laravel</h3>
                    <p class="text-gray-600">
                        Aplikasi Laravel dioptimasi dengan perintah berikut:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan config:cache</code>: Meng-cache konfigurasi</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan route:cache</code>: Meng-cache rute</li>
                        <li><code class="bg-gray-100 px-2 py-1 rounded">php artisan view:cache</code>: Meng-cache view</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Database</h3>
                    <p class="text-gray-600">
                        Database dikonfigurasi dengan langkah-langkah berikut:
                    </p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Instalasi MySQL Server</li>
                        <li>Konfigurasi keamanan dengan mysql_secure_installation</li>
                        <li>Pembuatan database dan user</li>
                        <li>Pemberian hak akses ke database</li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-medium mb-2">Konfigurasi Redis</h3>
                    <p class="text-gray-600">
                        Redis dikonfigurasi sebagai cache driver dengan pengaturan di file .env:
                    </p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379</pre>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 