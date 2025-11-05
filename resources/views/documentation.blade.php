@extends('layouts.app')

@section('title', 'Dokumentasi')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center mb-6">
        <i class="fas fa-book text-primary-600 text-3xl mr-4"></i>
        <h1 class="text-3xl font-bold text-gray-800">Dokumentasi API Gateway Testing</h1>
    </div>
    
    <p class="text-gray-600 mb-8">
        Selamat datang di dokumentasi API Gateway Testing. Halaman ini berisi panduan lengkap tentang cara menggunakan aplikasi, 
        fitur-fitur utama, dan implementasi teknis dari sistem perbandingan API REST dan GraphQL.
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-tachometer-alt text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Panduan penggunaan dashboard utama aplikasi, termasuk cara memilih dan menjalankan skenario pengujian.
            </p>
            <a href="/docs/dashboard" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-code-branch text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Repository</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Informasi tentang 7 repository studi kasus yang digunakan dalam pengujian dan cara menggunakannya.
            </p>
            <a href="/docs/repositories" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-chart-line text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Metrik Performa</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Penjelasan tentang metrik performa yang diukur, cara menganalisis hasil, dan visualisasi data.
            </p>
            <a href="/docs/metrics" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-server text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Deployment AWS</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Panduan langkah demi langkah untuk men-deploy aplikasi ke lingkungan AWS EC2 Free Tier.
            </p>
            <a href="/docs/aws" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-hammer text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">JMeter</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Informasi tentang integrasi dengan Apache JMeter untuk pengujian beban dan performa.
            </p>
            <a href="/docs/jmeter" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-200">
            <div class="flex items-center mb-4">
                <div class="bg-primary-100 p-3 rounded-full mr-4">
                    <i class="fas fa-list-alt text-primary-600 text-xl"></i>
                </div>
                <h2 class="text-xl font-semibold text-gray-800">Riwayat Log</h2>
            </div>
            <p class="text-gray-600 mb-4">
                Penjelasan tentang sistem pencatatan log, cara melihat riwayat pengujian, dan menganalisis data.
            </p>
            <a href="/docs/logs" class="inline-flex items-center text-primary-600 hover:text-primary-800 font-medium">
                Baca selengkapnya
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
    
    <div class="bg-primary-50 rounded-lg p-6 border border-primary-200">
        <div class="flex items-start mb-4">
            <div class="bg-primary-100 p-2 rounded-full mr-4">
                <i class="fas fa-lightbulb text-primary-600 text-lg"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">Memulai dengan Cepat</h3>
        </div>
        <ol class="list-decimal pl-6 space-y-3 text-gray-600">
            <li>Akses dashboard utama di <a href="/" class="text-primary-600 hover:underline">halaman beranda</a>.</li>
            <li>Pilih salah satu dari 14 skenario query yang tersedia dari dropdown.</li>
            <li>Klik tombol "Jalankan Pengujian" untuk memulai perbandingan API REST dan GraphQL.</li>
            <li>Lihat hasil perbandingan yang ditampilkan, termasuk API pemenang dan waktu respons.</li>
            <li>Jelajahi fitur lain seperti Metrik Performa, Repository Studi Kasus, dan Riwayat Log.</li>
        </ol>
    </div>
</div>
@endsection 