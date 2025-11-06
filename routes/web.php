<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PerformanceMetricController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\RepositoryTestController;
use App\Http\Controllers\ReportController;

Route::get('/', [DashboardController::class, 'index']);
Route::post('/test', [DashboardController::class, 'startTest']);
Route::post('/run-batch-test', [DashboardController::class, 'runBatchTest']);
Route::post('/run-performance-test', [DashboardController::class, 'runPerformanceTest']);
Route::get('/api-comparison-data', [DashboardController::class, 'getApiComparisonData']);
Route::get('/logs', [DashboardController::class, 'showLogs']);
Route::get('/dashboard-chart-data', [DashboardController::class, 'getDashboardChartData']);

// Route untuk fitur pengukuran performa
Route::get('/performance', [PerformanceMetricController::class, 'index']);
Route::post('/performance/test', [PerformanceMetricController::class, 'runPerformanceTest']);
Route::get('/performance/metrics-data', [PerformanceMetricController::class, 'getMetricsData']);

// Route untuk integrasi dengan JMeter
Route::post('/api/test', [ApiController::class, 'test']);
Route::get('/api/status', function () {
    return response()->json([
        'status' => 'online',
        'timestamp' => now()->toIso8601String(),
        'version' => config('app.version', '1.0.0')
    ]);
});

// Route untuk panduan JMeter
Route::get('/jmeter-guide', function () {
    return view('jmeter-guide');
});

// Route untuk pengujian repository studi kasus
Route::get('/repositories', [RepositoryTestController::class, 'index']);
Route::post('/repositories/test', [RepositoryTestController::class, 'runRepositoryTest']);

// Route untuk dokumentasi deployment AWS EC2
Route::get('/aws-deployment', function () {
    return view('aws-deployment');
});

// Route untuk halaman dokumentasi
Route::get('/documentation', function () {
    return view('documentation');
});

// Route untuk halaman dokumentasi fitur
Route::get('/docs/dashboard', function () {
    return view('docs.dashboard');
});

Route::get('/docs/repositories', function () {
    return view('docs.repositories');
});

Route::get('/docs/jmeter', function () {
    return view('docs.jmeter');
});

Route::get('/docs/aws', function () {
    return view('docs.aws');
});

Route::get('/docs/metrics', function () {
    return view('docs.metrics');
});

Route::get('/docs/logs', function () {
    return view('docs.logs');
});

Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');

// Route untuk halaman dokumentasi API endpoints
Route::get('/api-endpoints', [DashboardController::class, 'showApiEndpoints']);

Route::get('/test-details', [DashboardController::class, 'getTestDetails']);
Route::delete('/test-batches', [DashboardController::class, 'deleteBatch'])->name('test-batches.destroy');
