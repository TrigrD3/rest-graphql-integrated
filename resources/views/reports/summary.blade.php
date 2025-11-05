@extends('layouts.app')

@section('title', 'Laporan Pintar - Summary')

@section('head')
<style>
    /* Minimal glassmorphism styling only */
    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.10);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-radius: 1.5rem;
        border: 1px solid rgba(255,255,255,0.18);
        transition: box-shadow 0.2s;
    }
    .glass-card:hover {
        box-shadow: 0 12px 40px 0 rgba(31, 38, 135, 0.18);
    }
    .modern-btn {
        border-radius: 9999px;
        font-weight: 600;
        transition: background 0.2s;
    }
    .modern-table th {
        background: rgba(59,130,246,0.08);
        font-weight: 700;
    }
    .modern-table tr:nth-child(even) {
        background: rgba(59,130,246,0.03);
    }
    .modern-table tr:hover {
        background: rgba(59,130,246,0.10);
    }
</style>
@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-5xl font-black text-center text-blue-900 tracking-tight">Laporan Pintar <span class="font-light">- Summary</span></h1>
</div>

{{-- Card Statistik Modern --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
    <div class="glass-card p-7 flex items-center gap-5">
        <span class="text-5xl text-blue-500"><i class="fas fa-tasks"></i></span>
        <div>
            <div class="text-xs uppercase tracking-wider text-blue-700">Total Pengujian</div>
            <div class="text-4xl font-extrabold text-blue-900">{{ $metrics['total_tests'] }}</div>
        </div>
    </div>
    <div class="glass-card p-7 flex items-center gap-5">
        <span class="text-5xl text-green-500"><i class="fas fa-check-circle"></i></span>
        <div>
            <div class="text-xs uppercase tracking-wider text-green-700">Success Rate</div>
            <div class="text-4xl font-extrabold text-green-900">{{ number_format($metrics['success_rate'], 2) }}%</div>
        </div>
    </div>
    <div class="glass-card p-7 flex items-center gap-5">
        <span class="text-5xl text-purple-500"><i class="fas fa-trophy"></i></span>
        <div>
            <div class="text-xs uppercase tracking-wider text-purple-700">REST vs GraphQL</div>
            <div class="text-lg">REST: <b class="text-blue-700">{{ number_format($metrics['rest_wins'],1) }}%</b></div>
            <div class="text-lg">GraphQL: <b class="text-green-700">{{ number_format($metrics['graphql_wins'],1) }}%</b></div>
        </div>
    </div>
    <div class="glass-card p-7 flex items-center gap-5">
        <span class="text-5xl text-yellow-500"><i class="fas fa-database"></i></span>
        <div>
            <div class="text-xs uppercase tracking-wider text-yellow-700">Cache HIT Rate</div>
            <div class="text-4xl font-extrabold text-yellow-900">{{ number_format($metrics['cache_hit_rate'], 2) }}%</div>
        </div>
    </div>
</div>

{{-- Narasi Kesimpulan Otomatis --}}
<div class="glass-card border-l-8 border-blue-600 p-8 mb-10">
    <h2 class="font-bold text-blue-700 mb-3 text-2xl flex items-center"><i class="fas fa-lightbulb mr-2"></i> Kesimpulan Otomatis</h2>
    @foreach($insight as $i => $item)
        <p class="mb-3 text-gray-800 text-lg leading-relaxed" style="text-indent:2em;">
            {!! $item !!}
            @if(Str::contains($item, 'Rekomendasi'))
                <span class="ml-2 inline-block bg-green-200 text-green-800 text-xs px-2 py-1 rounded-full align-middle">Rekomendasi</span>
            @endif
        </p>
    @endforeach
</div>

{{-- Box Alert jika ada anomali --}}
@if(Str::contains(implode(' ', $insight), 'anomali'))
<div class="glass-card bg-red-50 border-l-4 border-red-500 text-red-700 p-5 mb-10 flex items-center gap-3">
    <i class="fas fa-exclamation-triangle text-2xl"></i>
    <span><strong>Perhatian:</strong> Ditemukan anomali pada hasil pengujian. Silakan cek data mentah untuk investigasi lebih lanjut.</span>
</div>
@endif

{{-- Grafik-grafik dalam grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-14">
    <div class="glass-card p-6 flex flex-col items-center">
        <h2 class="text-lg font-bold mb-1 text-blue-700">Tren Historis Waktu Respons</h2>
        <p class="text-xs text-gray-500 mb-2 text-center">Perbandingan waktu respons REST dan GraphQL untuk setiap skenario query.</p>
        <canvas id="chartTrend" height="60" style="width:100%"></canvas>
    </div>
    <div class="glass-card p-6 flex flex-col items-center">
        <h2 class="text-lg font-bold mb-1 text-purple-700">Radar Chart Perbandingan Metrik</h2>
        <p class="text-xs text-gray-500 mb-2 text-center">Visualisasi rata-rata waktu respons, CPU, dan memory.</p>
        <canvas id="chartRadar" height="60" style="width:100%"></canvas>
    </div>
    <div class="glass-card p-6 flex flex-col items-center">
        <h2 class="text-lg font-bold mb-1 text-green-700">Perbandingan API per Query</h2>
        <p class="text-xs text-gray-500 mb-2 text-center">Rata-rata waktu respons REST vs GraphQL untuk tiap query.</p>
        <canvas id="chartResponseTime" height="60" style="width:100%"></canvas>
    </div>
    <div class="glass-card p-6 flex flex-col items-center">
        <h2 class="text-lg font-bold mb-1 text-yellow-700">Distribusi Cache HIT vs MISS</h2>
        <p class="text-xs text-gray-500 mb-2 text-center">Proporsi request yang menggunakan cache dan tidak.</p>
        <canvas id="chartCache" height="60" style="width:100%"></canvas>
    </div>
</div>

{{-- Analisis Cache --}}
<div class="mb-14">
    <h2 class="text-2xl font-bold mb-3 text-blue-800">Analisis Cache</h2>
    <div class="glass-card shadow p-7 mb-2">
        <ul class="text-base text-gray-700 space-y-1">
            <li>Total Request: <b>{{ $cacheAnalysis['total'] }}</b></li>
            <li>Cache HIT: <b>{{ $cacheAnalysis['hit'] }}</b></li>
            <li>Cache MISS: <b>{{ $cacheAnalysis['miss'] }}</b></li>
            <li>Rata-rata waktu dengan cache: <b>{{ number_format($cacheAnalysis['avg_time_with_cache'],2) }} ms</b></li>
            <li>Rata-rata waktu tanpa cache: <b>{{ number_format($cacheAnalysis['avg_time_without_cache'],2) }} ms</b></li>
            <li>Penghematan waktu: <b>{{ $cacheAnalysis['saving'] ? number_format($cacheAnalysis['saving'],2) . ' ms' : 'N/A' }}</b></li>
        </ul>
    </div>
</div>

{{-- Tabel Perbandingan Per Query --}}
<div class="mb-14">
    <h2 class="text-2xl font-bold mb-3 text-blue-800">Perbandingan Per Query</h2>
    <div class="overflow-x-auto rounded-2xl border border-blue-100 glass-card">
        <table class="min-w-full modern-table rounded-2xl">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3">Query ID</th>
                    <th class="px-4 py-3">Deskripsi</th>
                    <th class="px-4 py-3">REST Avg (ms)</th>
                    <th class="px-4 py-3">GraphQL Avg (ms)</th>
                    <th class="px-4 py-3">REST Wins</th>
                    <th class="px-4 py-3">GraphQL Wins</th>
                    <th class="px-4 py-3">Pemenang</th>
                    <th class="px-4 py-3">Detail</th>
                </tr>
            </thead>
            <tbody>
                @foreach($queryComparisons as $qc)
                <tr>
                    <td class="px-4 py-3 font-semibold">{{ $qc->query_id }}</td>
                    <td class="px-4 py-3">{{ $qc->description }}</td>
                    <td class="px-4 py-3">{{ number_format($qc->avg_rest_time,2) }}</td>
                    <td class="px-4 py-3">{{ number_format($qc->avg_graphql_time,2) }}</td>
                    <td class="px-4 py-3">{{ $qc->rest_wins }}</td>
                    <td class="px-4 py-3">{{ $qc->graphql_wins }}</td>
                    <td class="px-4 py-3 font-bold text-green-700">{{ strtoupper($qc->winner) }}</td>
                    <td class="px-4 py-3"><button class="modern-btn bg-blue-500 text-white px-3 py-1 hover:bg-blue-700 flex items-center gap-1" onclick="showDetailModal('{{ $qc->query_id }}')"><i class="fas fa-search"></i> Detail</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Analisis Per Repository Studi Kasus --}}
<div class="mb-14">
    <h2 class="text-2xl font-bold mb-3 text-blue-800">Analisis Per Repository Studi Kasus</h2>
    <div class="overflow-x-auto rounded-2xl border border-blue-100 glass-card">
        <table class="min-w-full modern-table rounded-2xl">
            <thead class="sticky top-0 z-10">
                <tr>
                    <th class="px-4 py-3">Repository</th>
                    <th class="px-4 py-3">API Type</th>
                    <th class="px-4 py-3">Rata-rata CPU (%)</th>
                    <th class="px-4 py-3">Rata-rata Memory (%)</th>
                    <th class="px-4 py-3">Rata-rata Response Time (ms)</th>
                    <th class="px-4 py-3">Total Test</th>
                </tr>
            </thead>
            <tbody>
                @foreach($repoAnalysis as $repoId => $rows)
                    @foreach($rows as $row)
                    <tr>
                        <td class="px-4 py-3 font-semibold">{{ $repoId }}</td>
                        <td class="px-4 py-3">{{ strtoupper($row->api_type) }}</td>
                        <td class="px-4 py-3">{{ number_format($row->avg_cpu,2) }}</td>
                        <td class="px-4 py-3">{{ number_format($row->avg_mem,2) }}</td>
                        <td class="px-4 py-3">{{ number_format($row->avg_time,2) }}</td>
                        <td class="px-4 py-3">{{ $row->total_test }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- Tombol Export Data & Narasi --}}
<div class="mb-14 flex flex-wrap gap-4 justify-center">
    <button onclick="exportTableToCSV('summary.csv')" class="modern-btn bg-green-600 text-white px-6 py-2 hover:bg-green-700 flex items-center gap-2"><i class="fas fa-file-csv"></i> Export ke CSV</button>
    <button onclick="window.print()" class="modern-btn bg-blue-600 text-white px-6 py-2 hover:bg-blue-700 flex items-center gap-2"><i class="fas fa-print"></i> Cetak / Export PDF</button>
    <button onclick="exportNarrativeToWord()" class="modern-btn bg-purple-600 text-white px-6 py-2 hover:bg-purple-700 flex items-center gap-2"><i class="fas fa-file-word"></i> Export Narasi ke Word</button>
</div>

{{-- Modal Detail Query --}}
<div id="detailModal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50 hidden">
    <div class="glass-card shadow-2xl p-10 w-full max-w-lg relative">
        <button onclick="closeDetailModal()" class="absolute top-3 right-3 text-gray-500 hover:text-red-600 text-2xl"><i class="fas fa-times"></i></button>
        <h3 class="text-2xl font-bold mb-4 text-blue-800 flex items-center"><i class="fas fa-search mr-2"></i> Detail Query <span id="modalQueryId" class="ml-2"></span></h3>
        <div id="modalContent" class="text-gray-800"></div>
    </div>
</div>
@endsection

@section('scripts')
{{-- Chart.js & Interaktif --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fallback data jika kosong
    const restTimes = @json($chart_data['rest_times'] ?? []);
    const graphqlTimes = @json($chart_data['graphql_times'] ?? []);
    const labels = @json($chart_data['labels'] ?? []);
    
    // Grafik Tren Historis
    new Chart(document.getElementById('chartTrend').getContext('2d'), {
        type: 'line',
        data: {
            labels: labels.length ? labels : Array.from({length: 14}, (_, i) => 'Q'+(i+1)),
            datasets: [
                {
                    label: 'REST',
                    data: restTimes.length ? restTimes : Array(14).fill(0),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(59,130,246,0.1)',
                    tension: 0.3
                },
                {
                    label: 'GraphQL',
                    data: graphqlTimes.length ? graphqlTimes : Array(14).fill(0),
                    borderColor: '#a21caf',
                    backgroundColor: 'rgba(139,92,246,0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Tren Historis Waktu Respons per Query' }
            }
        }
    });
    
    // Radar Chart Multi-Metrik
    new Chart(document.getElementById('chartRadar').getContext('2d'), {
        type: 'radar',
        data: {
            labels: ['REST Mean', 'GraphQL Mean', 'CPU Mean', 'Memory Mean'],
            datasets: [
                {
                    label: 'REST',
                    data: [
                        {{ $advancedStats['rest']['mean'] ?? 0 }},
                        0,
                        {{ $advancedStats['cpu']['mean'] ?? 0 }},
                        {{ $advancedStats['memory']['mean'] ?? 0 }}
                    ],
                    backgroundColor: 'rgba(59,130,246,0.2)',
                    borderColor: '#2563eb',
                    pointBackgroundColor: '#2563eb'
                },
                {
                    label: 'GraphQL',
                    data: [
                        0,
                        {{ $advancedStats['graphql']['mean'] ?? 0 }},
                        {{ $advancedStats['cpu']['mean'] ?? 0 }},
                        {{ $advancedStats['memory']['mean'] ?? 0 }}
                    ],
                    backgroundColor: 'rgba(139,92,246,0.2)',
                    borderColor: '#a21caf',
                    pointBackgroundColor: '#a21caf'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Radar Chart Perbandingan Metrik' }
            }
        }
    });
    
    // Grafik Perbandingan Response Time
    new Chart(document.getElementById('chartResponseTime').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels.length ? labels : Array.from({length: 14}, (_, i) => 'Q'+(i+1)),
            datasets: [
                {
                    label: 'REST Avg (ms)',
                    data: restTimes.length ? restTimes : Array(14).fill(0),
                    backgroundColor: 'rgba(59, 130, 246, 0.7)'
                },
                {
                    label: 'GraphQL Avg (ms)',
                    data: graphqlTimes.length ? graphqlTimes : Array(14).fill(0),
                    backgroundColor: 'rgba(139, 92, 246, 0.7)'
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Perbandingan Waktu Respons per Query' }
            }
        }
    });
    
    // Grafik Cache
    new Chart(document.getElementById('chartCache').getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['HIT', 'MISS'],
            datasets: [{
                data: [{{ $cacheAnalysis['hit'] ?? 0 }}, {{ $cacheAnalysis['miss'] ?? 0 }}],
                backgroundColor: ['#22c55e', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                title: { display: true, text: 'Distribusi Cache HIT vs MISS' }
            }
        }
    });
});

// Export Functions - Global Scope
function exportTableToCSV(filename) {
    try {
        let csv = [];
        let timestamp = new Date().toLocaleString('id-ID');
        
        // Header laporan
        csv.push('"Laporan Summary - API Gateway Testing"');
        csv.push('"Generated: ' + timestamp + '"');
        csv.push('""'); // Empty line
        
        // Export statistik utama
        csv.push('"=== STATISTIK UTAMA ==="');
        csv.push('"Metrik","Nilai"');
        csv.push('"Total Pengujian","{{ $metrics['total_tests'] }}"');
        csv.push('"Success Rate","{{ number_format($metrics['success_rate'], 2) }}%"');
        csv.push('"REST Wins","{{ number_format($metrics['rest_wins'], 1) }}%"');
        csv.push('"GraphQL Wins","{{ number_format($metrics['graphql_wins'], 1) }}%"');
        csv.push('"Cache Hit Rate","{{ number_format($metrics['cache_hit_rate'], 2) }}%"');
        csv.push('""'); // Empty line
        
        // Export analisis cache
        csv.push('"=== ANALISIS CACHE ==="');
        csv.push('"Metrik","Nilai"');
        csv.push('"Total Request","{{ $cacheAnalysis['total'] }}"');
        csv.push('"Cache HIT","{{ $cacheAnalysis['hit'] }}"');
        csv.push('"Cache MISS","{{ $cacheAnalysis['miss'] }}"');
        csv.push('"Rata-rata waktu dengan cache","{{ number_format($cacheAnalysis['avg_time_with_cache'],2) }} ms"');
        csv.push('"Rata-rata waktu tanpa cache","{{ number_format($cacheAnalysis['avg_time_without_cache'],2) }} ms"');
        csv.push('""'); // Empty line
        
        // Export tabel
        document.querySelectorAll('table').forEach((table, index) => {
            let tableTitle = table.closest('div').previousElementSibling;
            if (tableTitle && tableTitle.tagName === 'H2') {
                csv.push('"=== ' + tableTitle.textContent.toUpperCase() + ' ==="');
            }
            
            let rows = table.querySelectorAll('tr');
            for (let row of rows) {
                let cols = row.querySelectorAll('th,td');
                let rowData = [];
                for (let col of cols) {
                    let text = col.innerText.replace(/"/g, '""').replace(/\n/g, ' ').trim();
                    rowData.push('"' + text + '"');
                }
                if (rowData.length > 0) {
                    csv.push(rowData.join(','));
                }
            }
            csv.push('""'); // Empty line between tables
        });
        
        let csvFile = new Blob([csv.join('\n')], {type: 'text/csv;charset=utf-8;'});
        let downloadLink = document.createElement('a');
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = 'none';
        document.body.appendChild(downloadLink);
        downloadLink.click();
        document.body.removeChild(downloadLink);
        
        alert('Data berhasil diekspor ke CSV!');
    } catch (error) {
        console.error('Error exporting CSV:', error);
        alert('Gagal mengekspor data ke CSV. Silakan coba lagi.');
    }
}

function exportNarrativeToWord() {
    try {
        let content = '';
        let timestamp = new Date().toLocaleString('id-ID');
        
        // HTML document structure
        content += '<!DOCTYPE html><html><head><meta charset="utf-8">';
        content += '<title>Laporan Summary - API Gateway Testing</title>';
        content += '<style>body{font-family:Arial,sans-serif;margin:40px;} h1,h2{color:#1e40af;} .stats{background:#f3f4f6;padding:20px;margin:20px 0;border-radius:8px;} table{border-collapse:collapse;width:100%;margin:20px 0;} th,td{border:1px solid #ddd;padding:8px;text-align:left;} th{background:#e5e7eb;}</style>';
        content += '</head><body>';
        
        content += '<h1>Laporan Pintar - Summary</h1>';
        content += '<p><strong>Generated:</strong> ' + timestamp + '</p>';
        
        // Export statistik
        content += '<div class="stats">';
        content += '<h2>Statistik Utama</h2>';
        content += '<ul>';
        content += '<li>Total Pengujian: <strong>{{ $metrics['total_tests'] }}</strong></li>';
        content += '<li>Success Rate: <strong>{{ number_format($metrics['success_rate'], 2) }}%</strong></li>';
        content += '<li>REST Wins: <strong>{{ number_format($metrics['rest_wins'], 1) }}%</strong></li>';
        content += '<li>GraphQL Wins: <strong>{{ number_format($metrics['graphql_wins'], 1) }}%</strong></li>';
        content += '<li>Cache Hit Rate: <strong>{{ number_format($metrics['cache_hit_rate'], 2) }}%</strong></li>';
        content += '</ul></div>';
        
        // Export kesimpulan
        let conclusionDiv = document.querySelector('.glass-card.border-l-8.border-blue-600');
        if (conclusionDiv) {
            content += '<h2>Kesimpulan Otomatis</h2>';
            let paragraphs = conclusionDiv.querySelectorAll('p');
            paragraphs.forEach(p => {
                content += '<p>' + p.innerHTML + '</p>';
            });
        }
        
        // Export cache analysis
        content += '<h2>Analisis Cache</h2>';
        content += '<ul>';
        content += '<li>Total Request: <strong>{{ $cacheAnalysis['total'] }}</strong></li>';
        content += '<li>Cache HIT: <strong>{{ $cacheAnalysis['hit'] }}</strong></li>';
        content += '<li>Cache MISS: <strong>{{ $cacheAnalysis['miss'] }}</strong></li>';
        content += '<li>Rata-rata waktu dengan cache: <strong>{{ number_format($cacheAnalysis['avg_time_with_cache'],2) }} ms</strong></li>';
        content += '<li>Rata-rata waktu tanpa cache: <strong>{{ number_format($cacheAnalysis['avg_time_without_cache'],2) }} ms</strong></li>';
        content += '</ul>';
        
        // Export tables
        document.querySelectorAll('table').forEach((table, index) => {
            let tableTitle = table.closest('div').previousElementSibling;
            if (tableTitle && tableTitle.tagName === 'H2') {
                content += '<h2>' + tableTitle.textContent + '</h2>';
            }
            content += table.outerHTML;
        });
        
        content += '</body></html>';
        
        let blob = new Blob([content], {type: 'application/msword;charset=utf-8'});
        saveAs(blob, 'laporan_summary_' + Date.now() + '.doc');
        
        alert('Narasi berhasil diekspor ke Word!');
    } catch (error) {
        console.error('Error exporting Word:', error);
        alert('Gagal mengekspor narasi ke Word. Silakan coba lagi.');
    }
}

// Modal Functions - Global Scope
function showDetailModal(queryId) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('modalQueryId').innerText = queryId;
    document.getElementById('modalContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat detail...</div>';
    
    // Simulate loading detail data
    setTimeout(() => {
        let detailContent = '<div class="space-y-4">';
        detailContent += '<div class="bg-blue-50 p-4 rounded-lg">';
        detailContent += '<h4 class="font-bold text-blue-800">Query: ' + queryId + '</h4>';
        detailContent += '<p class="text-sm text-gray-600 mt-2">Detail lengkap untuk query ' + queryId + ' akan ditampilkan di sini setelah implementasi backend.</p>';
        detailContent += '</div>';
        detailContent += '<div class="bg-gray-50 p-4 rounded-lg">';
        detailContent += '<h5 class="font-semibold">Informasi Tambahan:</h5>';
        detailContent += '<ul class="text-sm mt-2 space-y-1">';
        detailContent += '<li>• Waktu eksekusi rata-rata</li>';
        detailContent += '<li>• Analisis bottleneck</li>';
        detailContent += '<li>• Rekomendasi optimasi</li>';
        detailContent += '</ul>';
        detailContent += '</div>';
        detailContent += '</div>';
        
        document.getElementById('modalContent').innerHTML = detailContent;
    }, 1000);
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}
</script>
@endsection 