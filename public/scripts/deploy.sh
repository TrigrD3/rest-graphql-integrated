#!/bin/bash

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
echo "Pastikan untuk mengganti 'your-ec2-public-dns.amazonaws.com' dengan DNS publik instance EC2 Anda." 