@extends('layouts.app')

@section('title', 'API Endpoints Documentation')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800 mb-2">API Endpoints Documentation</h1>
    <p class="text-gray-600">Detail lengkap REST endpoints dan GraphQL queries untuk setiap skenario pengujian performa API Gateway</p>
</div>

<div class="space-y-8">
    @foreach($queryDetails as $queryId => $details)
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-2">{{ $queryId }}</h2>
            <p class="text-gray-600">{{ $details['description'] }}</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- REST API Section -->
            <div class="bg-blue-50 rounded-lg p-6 border border-blue-200">
                <h3 class="text-xl font-semibold text-blue-800 mb-4 flex items-center">
                    <i class="fas fa-link text-blue-600 mr-2"></i>
                    REST API Endpoint
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-blue-700 mb-2">HTTP Method & URL:</label>
                        <div class="bg-white rounded p-3 border border-blue-300">
                            <code class="text-sm text-gray-800 break-all">GET {{ $details['rest'] }}</code>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-700 mb-2">Request Headers:</label>
                        <div class="bg-white rounded p-3 border border-blue-300">
                            <pre class="text-sm text-gray-800"><code>Authorization: Bearer {GITHUB_TOKEN}
Accept: application/vnd.github.v3+json</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-blue-700 mb-2">Response Format:</label>
                        <div class="bg-white rounded p-3 border border-blue-300">
                            <code class="text-sm text-gray-800">JSON (GitHub REST API v3 format)</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GraphQL Section -->
            <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                <h3 class="text-xl font-semibold text-green-800 mb-4 flex items-center">
                    <i class="fas fa-code text-green-600 mr-2"></i>
                    GraphQL Query
                </h3>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-green-700 mb-2">Endpoint URL:</label>
                        <div class="bg-white rounded p-3 border border-green-300">
                            <code class="text-sm text-gray-800">POST https://api.github.com/graphql</code>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-green-700 mb-2">Request Headers:</label>
                        <div class="bg-white rounded p-3 border border-green-300">
                            <pre class="text-sm text-gray-800"><code>Authorization: Bearer {GITHUB_TOKEN}
Content-Type: application/json
Accept: application/json</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-green-700 mb-2">GraphQL Query:</label>
                        <div class="bg-white rounded p-3 border border-green-300 max-h-64 overflow-y-auto">
                            <pre class="text-sm text-gray-800 whitespace-pre-wrap">{{ $details['graphql'] }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Characteristics -->
        <div class="mt-6 bg-gray-50 rounded-lg p-4">
            <h4 class="font-semibold text-gray-700 mb-3">
                <i class="fas fa-chart-line text-gray-600 mr-2"></i>
                Karakteristik Performa
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div class="space-y-2">
                    <p><span class="font-medium text-blue-600">REST API:</span></p>
                    <ul class="text-gray-600 space-y-1 ml-4">
                        <li>• Request yang lebih sederhana</li>
                        <li>• Caching HTTP standar</li>
                        <li>• Multiple round trips untuk data terkait</li>
                        <li>• Over-fetching atau under-fetching data</li>
                    </ul>
                </div>
                <div class="space-y-2">
                    <p><span class="font-medium text-green-600">GraphQL:</span></p>
                    <ul class="text-gray-600 space-y-1 ml-4">
                        <li>• Query yang lebih kompleks</li>
                        <li>• Fetching data yang tepat sesuai kebutuhan</li>
                        <li>• Single request untuk data terkait</li>
                        <li>• Optimasi bandwidth dan latency</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Back to Dashboard -->
<div class="mt-8 text-center">
    <a href="/" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors duration-300">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali ke Dashboard
    </a>
</div>

@endsection
