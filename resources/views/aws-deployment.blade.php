<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment ke AWS EC2 - API Gateway Testing</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Panduan Deployment ke AWS EC2</h1>
        
        <div class="mb-4">
            <a href="/" class="text-blue-600 hover:underline">← Kembali ke Dashboard</a>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Prasyarat</h2>
            
            <ul class="list-disc pl-6 space-y-2">
                <li>Akun AWS (Free Tier)</li>
                <li>Key Pair AWS EC2 (untuk koneksi SSH)</li>
                <li>Git diinstal di komputer lokal</li>
                <li>Composer diinstal di komputer lokal</li>
                <li>Akses terminal atau command prompt</li>
            </ul>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah 1: Membuat Instance EC2</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Login ke AWS Console</p>
                    <p class="text-gray-600">Buka <a href="https://aws.amazon.com/console/" target="_blank" class="text-blue-600 hover:underline">AWS Console</a> dan login dengan akun AWS Anda.</p>
                </li>
                
                <li>
                    <p class="font-medium">Buka EC2 Dashboard</p>
                    <p class="text-gray-600">Pilih layanan EC2 dari daftar layanan AWS.</p>
                </li>
                
                <li>
                    <p class="font-medium">Luncurkan Instance Baru</p>
                    <p class="text-gray-600">Klik tombol "Launch Instance" dan ikuti langkah-langkah berikut:</p>
                    <ul class="list-disc pl-6 mt-2">
                        <li>Pilih Amazon Machine Image (AMI): <strong>Ubuntu Server 20.04 LTS</strong></li>
                        <li>Pilih Instance Type: <strong>t2.micro</strong> (Free Tier eligible)</li>
                        <li>Konfigurasi Instance: Gunakan pengaturan default</li>
                        <li>Tambahkan Storage: Minimal 8 GB (default)</li>
                        <li>Tambahkan Tags: Opsional</li>
                        <li>Konfigurasi Security Group: Buat security group baru dengan aturan berikut:
                            <ul class="list-disc pl-6 mt-1">
                                <li>SSH (Port 22) - Source: Your IP</li>
                                <li>HTTP (Port 80) - Source: Anywhere</li>
                                <li>HTTPS (Port 443) - Source: Anywhere</li>
                            </ul>
                        </li>
                        <li>Review dan Launch: Periksa konfigurasi dan klik "Launch"</li>
                        <li>Pilih Key Pair yang sudah ada atau buat yang baru, lalu klik "Launch Instances"</li>
                    </ul>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah 2: Menyiapkan Server</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Koneksi ke Instance EC2</p>
                    <p class="text-gray-600">Gunakan SSH untuk terhubung ke instance EC2 Anda:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">ssh -i /path/to/your-key.pem ubuntu@your-ec2-public-dns.amazonaws.com</pre>
                </li>
                
                <li>
                    <p class="font-medium">Update Sistem</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo apt update && sudo apt upgrade -y</pre>
                </li>
                
                <li>
                    <p class="font-medium">Instal Dependensi yang Diperlukan</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo apt install -y nginx php8.0-fpm php8.0-cli php8.0-common php8.0-mysql php8.0-zip php8.0-gd php8.0-mbstring php8.0-curl php8.0-xml php8.0-bcmath php8.0-redis mysql-server redis-server git unzip</pre>
                </li>
                
                <li>
                    <p class="font-medium">Konfigurasi MySQL</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo mysql_secure_installation</pre>
                    <p class="text-gray-600 mt-2">Ikuti petunjuk untuk mengatur password root dan pengaturan keamanan lainnya.</p>
                    
                    <p class="text-gray-600 mt-2">Buat database untuk aplikasi:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo mysql -u root -p
CREATE DATABASE api_gateway_test;
CREATE USER 'api_gateway'@'localhost' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON api_gateway_test.* TO 'api_gateway'@'localhost';
FLUSH PRIVILEGES;
EXIT;</pre>
                </li>
                
                <li>
                    <p class="font-medium">Instal Composer</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">cd ~
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer</pre>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah 3: Deploy Aplikasi</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Clone Repository</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">cd /var/www
sudo git clone https://github.com/username/api-gateway-testing.git</pre>
                    <p class="text-gray-600 mt-2">Ganti URL repository dengan URL repository GitHub Anda.</p>
                </li>
                
                <li>
                    <p class="font-medium">Atur Kepemilikan Direktori</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo chown -R ubuntu:www-data /var/www/api-gateway-testing
cd /var/www/api-gateway-testing</pre>
                </li>
                
                <li>
                    <p class="font-medium">Instal Dependensi PHP</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">composer install --no-dev --optimize-autoloader</pre>
                </li>
                
                <li>
                    <p class="font-medium">Konfigurasi Environment</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">cp .env.example .env
php artisan key:generate</pre>
                    
                    <p class="text-gray-600 mt-2">Edit file .env untuk mengatur koneksi database dan konfigurasi lainnya:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">nano .env</pre>
                    
                    <p class="text-gray-600 mt-2">Ubah pengaturan berikut:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-ec2-public-dns.amazonaws.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=api_gateway_test
DB_USERNAME=api_gateway
DB_PASSWORD=password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

GITHUB_TOKEN=your_github_token

REST_API_URL=https://jsonplaceholder.typicode.com
GRAPHQL_API_URL=https://api.github.com/graphql
API_TIMEOUT=10</pre>
                </li>
                
                <li>
                    <p class="font-medium">Migrasi Database</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">php artisan migrate</pre>
                </li>
                
                <li>
                    <p class="font-medium">Optimasi Laravel</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">php artisan config:cache
php artisan route:cache
php artisan view:cache</pre>
                </li>
                
                <li>
                    <p class="font-medium">Atur Permissions</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache</pre>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah 4: Konfigurasi Nginx</h2>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Buat Konfigurasi Nginx</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo nano /etc/nginx/sites-available/api-gateway</pre>
                    
                    <p class="text-gray-600 mt-2">Tambahkan konfigurasi berikut:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">server {
    listen 80;
    server_name your-ec2-public-dns.amazonaws.com;
    root /var/www/api-gateway-testing/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}</pre>
                    <p class="text-gray-600 mt-2">Ganti "your-ec2-public-dns.amazonaws.com" dengan DNS publik instance EC2 Anda.</p>
                </li>
                
                <li>
                    <p class="font-medium">Aktifkan Konfigurasi</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo ln -s /etc/nginx/sites-available/api-gateway /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx</pre>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Langkah 5: Konfigurasi Supervisor (Opsional)</h2>
            
            <p class="text-gray-600 mb-4">Jika aplikasi Anda menggunakan queue worker, Anda dapat menggunakan Supervisor untuk mengelolanya:</p>
            
            <ol class="list-decimal pl-6 space-y-4">
                <li>
                    <p class="font-medium">Instal Supervisor</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo apt install -y supervisor</pre>
                </li>
                
                <li>
                    <p class="font-medium">Buat Konfigurasi</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo nano /etc/supervisor/conf.d/api-gateway-worker.conf</pre>
                    
                    <p class="text-gray-600 mt-2">Tambahkan konfigurasi berikut:</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">[program:api-gateway-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/api-gateway-testing/artisan queue:work redis --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/api-gateway-testing/storage/logs/worker.log</pre>
                </li>
                
                <li>
                    <p class="font-medium">Reload Supervisor</p>
                    <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start api-gateway-worker:*</pre>
                </li>
            </ol>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold mb-4">Script Otomatisasi Deployment</h2>
            
            <p class="text-gray-600 mb-4">Berikut adalah script bash untuk otomatisasi deployment:</p>
            
            <div class="bg-gray-100 p-4 rounded mt-2 mb-4">
                <p class="font-medium">deploy.sh</p>
                <p class="text-gray-600 mt-2">Script bash untuk otomatisasi deployment ke AWS EC2.</p>
                <a href="/scripts/deploy.sh" class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" download>Download Script</a>
            </div>
            
            <pre class="bg-gray-100 p-4 rounded mt-2 overflow-x-auto">#!/bin/bash

# Script untuk deployment API Gateway Testing ke AWS EC2
# Simpan sebagai deploy.sh dan jalankan dengan: bash deploy.sh

echo "Memulai deployment API Gateway Testing ke AWS EC2..."

# Update sistem
echo "Memperbarui sistem..."
sudo apt update && sudo apt upgrade -y

# Instal dependensi
echo "Menginstal dependensi..."
sudo apt install -y nginx php8.0-fpm php8.0-cli php8.0-common php8.0-mysql php8.0-zip php8.0-gd php8.0-mbstring php8.0-curl php8.0-xml php8.0-bcmath php8.0-redis mysql-server redis-server git unzip

# Konfigurasi direktori aplikasi
echo "Menyiapkan direktori aplikasi..."
cd /var/www
if [ -d "api-gateway-testing" ]; then
    echo "Direktori sudah ada, memperbarui kode..."
    cd api-gateway-testing
    git pull
else
    echo "Mengkloning repository..."
    sudo git clone https://github.com/username/api-gateway-testing.git
    cd api-gateway-testing
fi

# Atur kepemilikan dan izin
sudo chown -R ubuntu:www-data /var/www/api-gateway-testing
sudo chmod -R 775 storage bootstrap/cache

# Instal dependensi PHP
echo "Menginstal dependensi PHP..."
composer install --no-dev --optimize-autoloader

# Konfigurasi environment
echo "Menyiapkan konfigurasi environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    php artisan key:generate
    
    # Edit file .env sesuai kebutuhan
    # Anda perlu mengedit file ini secara manual atau menggunakan sed
    # contoh: sed -i 's/DB_DATABASE=laravel/DB_DATABASE=api_gateway_test/g' .env
fi

# Migrasi database
echo "Menjalankan migrasi database..."
php artisan migrate --force

# Optimasi Laravel
echo "Mengoptimasi aplikasi..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Konfigurasi Nginx
echo "Menyiapkan konfigurasi Nginx..."
if [ ! -f "/etc/nginx/sites-available/api-gateway" ]; then
    sudo tee /etc/nginx/sites-available/api-gateway > /dev/null << 'EOL'
server {
    listen 80;
    server_name your-ec2-public-dns.amazonaws.com;
    root /var/www/api-gateway-testing/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOL

    # Aktifkan konfigurasi
    sudo ln -s /etc/nginx/sites-available/api-gateway /etc/nginx/sites-enabled/
fi

# Restart layanan
echo "Memulai ulang layanan..."
sudo nginx -t && sudo systemctl restart nginx
sudo systemctl restart php8.0-fpm

echo "Deployment selesai! Aplikasi seharusnya sudah berjalan di http://your-ec2-public-dns.amazonaws.com"
echo "Pastikan untuk mengganti 'your-ec2-public-dns.amazonaws.com' dengan DNS publik instance EC2 Anda."</pre>
            
            <div class="mt-4">
                <p class="text-gray-600">Untuk menggunakan script ini:</p>
                <ol class="list-decimal pl-6 mt-2">
                    <li>Simpan sebagai <code class="bg-gray-100 px-2 py-1 rounded">deploy.sh</code> di instance EC2 Anda</li>
                    <li>Berikan izin eksekusi: <code class="bg-gray-100 px-2 py-1 rounded">chmod +x deploy.sh</code></li>
                    <li>Jalankan script: <code class="bg-gray-100 px-2 py-1 rounded">./deploy.sh</code></li>
                </ol>
            </div>
        </div>
    </div>
</body>
</html>