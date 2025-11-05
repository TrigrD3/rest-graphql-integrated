<?php

namespace App\Services;

class SystemMetricsService
{
    /**
     * Mengukur penggunaan CPU menggunakan rumus:
     * CPU usage = (CPUused / CPUtotal) × 100
     * 
     * @return float Persentase penggunaan CPU
     */
    public function getCpuUsage(): float
    {
        try {
            // Coba gunakan shell_exec untuk mendapatkan penggunaan CPU
            if (PHP_OS_FAMILY === 'Windows') {
                $cmd = "wmic cpu get loadpercentage /value";
                $output = shell_exec($cmd);
                if (preg_match("/LoadPercentage=(\d+)/", $output, $matches)) {
                    return (float)$matches[1];
                }
            } else {
                // Linux
                $load = sys_getloadavg();
                return $load[0] * 100;
            }
            
            // Jika gagal, gunakan fallback
            return $this->getFallbackCpuUsage();
        } catch (\Exception $e) {
            \Log::error('Error getting CPU usage', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackCpuUsage();
        }
    }
    
    /**
     * Mengukur penggunaan memory menggunakan rumus:
     * Memory usage = (Memused / Memtotal) × 100
     * 
     * @return float Persentase penggunaan memory
     */
    public function getMemoryUsage(): float
    {
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                $cmd = "wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value";
                $output = shell_exec($cmd);
                
                if (preg_match("/TotalVisibleMemorySize=(\d+)/", $output, $total_matches) &&
                    preg_match("/FreePhysicalMemory=(\d+)/", $output, $free_matches)) {
                    $total = (float)$total_matches[1];
                    $free = (float)$free_matches[1];
                    return ($total - $free) / $total * 100;
                }
            } else {
                // Linux
                $memInfo = file_get_contents('/proc/meminfo');
                if ($memInfo) {
                    preg_match("/MemTotal:\s+(\d+)/", $memInfo, $total_matches);
                    preg_match("/MemFree:\s+(\d+)/", $memInfo, $free_matches);
                    preg_match("/Buffers:\s+(\d+)/", $memInfo, $buffers_matches);
                    preg_match("/Cached:\s+(\d+)/", $memInfo, $cached_matches);
                    
                    $total = (float)($total_matches[1] ?? 0);
                    $free = (float)($free_matches[1] ?? 0);
                    $buffers = (float)($buffers_matches[1] ?? 0);
                    $cached = (float)($cached_matches[1] ?? 0);
                    
                    if ($total > 0) {
                        $used = $total - $free - $buffers - $cached;
                        return ($used / $total) * 100;
                    }
                }
            }
            
            // Jika gagal, gunakan fallback
            return $this->getFallbackMemoryUsage();
        } catch (\Exception $e) {
            \Log::error('Error getting memory usage', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->getFallbackMemoryUsage();
        }
    }
    
    /**
     * Mengukur penggunaan CPU di Windows menggunakan WMI
     * 
     * @return float Persentase penggunaan CPU
     */
    private function getWindowsCpuUsage(): float
    {
        try {
            // Periksa apakah extension COM tersedia
            if (!extension_loaded('com_dotnet')) {
                throw new \Exception('COM extension tidak tersedia');
            }
            
            // Menggunakan COM untuk mengakses WMI
            $wmi = new \COM('WinMgmts:\\\\.');
            $cpus = $wmi->ExecQuery('SELECT LoadPercentage FROM Win32_Processor');
            
            $cpuUsage = 0;
            $count = 0;
            
            foreach ($cpus as $cpu) {
                $cpuUsage += $cpu->LoadPercentage;
                $count++;
            }
            
            return $count > 0 ? $cpuUsage / $count : 0;
        } catch (\Exception $e) {
            // Fallback jika WMI tidak tersedia
            return $this->getFallbackCpuUsage();
        }
    }
    
    /**
     * Mengukur penggunaan memory di Windows menggunakan WMI
     * 
     * @return float Persentase penggunaan memory
     */
    private function getWindowsMemoryUsage(): float
    {
        try {
            // Periksa apakah extension COM tersedia
            if (!extension_loaded('com_dotnet')) {
                throw new \Exception('COM extension tidak tersedia');
            }
            
            // Menggunakan COM untuk mengakses WMI
            $wmi = new \COM('WinMgmts:\\\\.');
            $os = $wmi->ExecQuery('SELECT TotalVisibleMemorySize, FreePhysicalMemory FROM Win32_OperatingSystem');
            
            foreach ($os as $item) {
                $totalMemory = $item->TotalVisibleMemorySize;
                $freeMemory = $item->FreePhysicalMemory;
                $usedMemory = $totalMemory - $freeMemory;
                
                return ($usedMemory / $totalMemory) * 100;
            }
            
            return 0;
        } catch (\Exception $e) {
            // Fallback jika WMI tidak tersedia
            return $this->getFallbackMemoryUsage();
        }
    }
    
    /**
     * Mengukur penggunaan CPU di Linux menggunakan /proc/stat
     * 
     * @return float Persentase penggunaan CPU
     */
    private function getLinuxCpuUsage(): float
    {
        // Jika berjalan di Windows tapi ingin mengukur untuk Linux (AWS EC2)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->getFallbackCpuUsage();
        }
        
        $prevStat = $this->getLinuxCpuStat();
        usleep(100000); // Tunggu 100ms
        $currentStat = $this->getLinuxCpuStat();
        
        // Hitung penggunaan CPU
        $prevTotal = array_sum($prevStat);
        $currentTotal = array_sum($currentStat);
        
        $prevIdle = $prevStat['idle'] + $prevStat['iowait'];
        $currentIdle = $currentStat['idle'] + $currentStat['iowait'];
        
        $totalDiff = $currentTotal - $prevTotal;
        $idleDiff = $currentIdle - $prevIdle;
        
        if ($totalDiff === 0) {
            return 0;
        }
        
        return 100 * (1 - ($idleDiff / $totalDiff));
    }
    
    /**
     * Mendapatkan statistik CPU dari /proc/stat
     * 
     * @return array Statistik CPU
     */
    private function getLinuxCpuStat(): array
    {
        $stat = file_get_contents('/proc/stat');
        $lines = explode("\n", $stat);
        $cpuLine = $lines[0];
        $cpuData = explode(' ', preg_replace('/\s+/', ' ', $cpuLine));
        
        return [
            'user' => (int)$cpuData[1],
            'nice' => (int)$cpuData[2],
            'system' => (int)$cpuData[3],
            'idle' => (int)$cpuData[4],
            'iowait' => (int)$cpuData[5],
            'irq' => (int)$cpuData[6],
            'softirq' => (int)$cpuData[7],
            'steal' => (int)$cpuData[8],
            'guest' => (int)$cpuData[9],
            'guest_nice' => (int)$cpuData[10],
        ];
    }
    
    /**
     * Mengukur penggunaan memory di Linux menggunakan /proc/meminfo
     * 
     * @return float Persentase penggunaan memory
     */
    private function getLinuxMemoryUsage(): float
    {
        // Jika berjalan di Windows tapi ingin mengukur untuk Linux (AWS EC2)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->getFallbackMemoryUsage();
        }
        
        $memInfo = file_get_contents('/proc/meminfo');
        $lines = explode("\n", $memInfo);
        
        $memTotal = 0;
        $memFree = 0;
        $memBuffers = 0;
        $memCached = 0;
        
        foreach ($lines as $line) {
            if (preg_match('/^MemTotal:\s+(\d+)/', $line, $matches)) {
                $memTotal = (int)$matches[1];
            } elseif (preg_match('/^MemFree:\s+(\d+)/', $line, $matches)) {
                $memFree = (int)$matches[1];
            } elseif (preg_match('/^Buffers:\s+(\d+)/', $line, $matches)) {
                $memBuffers = (int)$matches[1];
            } elseif (preg_match('/^Cached:\s+(\d+)/', $line, $matches)) {
                $memCached = (int)$matches[1];
            }
        }
        
        if ($memTotal === 0) {
            return 0;
        }
        
        // Rumus: (Total - Free - Buffers - Cached) / Total * 100
        $memUsed = $memTotal - $memFree - $memBuffers - $memCached;
        return ($memUsed / $memTotal) * 100;
    }
    
    /**
     * Fallback untuk pengukuran CPU jika metode utama gagal
     * 
     * @return float Persentase penggunaan CPU
     */
    private function getFallbackCpuUsage(): float
    {
        try {
            // Gunakan PHP untuk mengukur beban CPU
            $startTime = microtime(true);
            $startCycles = 0;
            for ($i = 0; $i < 1000000; $i++) {
                $startCycles++;
            }
            $endTime = microtime(true);
            $duration = $endTime - $startTime;
            
            // Hitung perkiraan beban CPU berdasarkan waktu eksekusi
            $baselineDuration = 0.1; // Waktu baseline untuk 1 juta iterasi
            $load = ($duration / $baselineDuration) * 100;
            
            return min(max($load, 0), 100);
        } catch (\Exception $e) {
            return 50.0; // Nilai default jika semua metode gagal
        }
    }
    
    /**
     * Fallback untuk pengukuran memory jika metode utama gagal
     * 
     * @return float Persentase penggunaan memory
     */
    private function getFallbackMemoryUsage(): float
    {
        try {
            // Gunakan memory_get_usage untuk mendapatkan penggunaan memori PHP
            $used = memory_get_usage(true);
            $limit = $this->getMemoryLimit();
            
            if ($limit > 0) {
                return min(($used / $limit) * 100, 100);
            }
            
            // Jika tidak bisa mendapatkan limit, gunakan persentase dari penggunaan saat ini
            $peak = memory_get_peak_usage(true);
            return $peak > 0 ? min(($used / $peak) * 100, 100) : 50.0;
        } catch (\Exception $e) {
            return 50.0; // Nilai default jika semua metode gagal
        }
    }
    
    /**
     * Mendapatkan batas memory PHP dalam bytes
     * 
     * @return int Batas memory dalam bytes
     */
    private function getMemoryLimit(): int
    {
        $memoryLimit = ini_get('memory_limit');
        
        if ($memoryLimit === '-1') {
            return PHP_INT_MAX;
        }
        
        $unit = strtoupper(substr($memoryLimit, -1));
        $value = (int)substr($memoryLimit, 0, -1);
        
        switch ($unit) {
            case 'G':
                return $value * 1024 * 1024 * 1024;
            case 'M':
                return $value * 1024 * 1024;
            case 'K':
                return $value * 1024;
            default:
                return (int)$memoryLimit;
        }
    }
} 