<?php

/**
 * Auto Checkout Command
 *
 * Script ini melakukan checkout otomatis untuk karyawan yang belum checkout.
 * Dijalankan langsung oleh cron job setiap hari kerja pada jam 5 sore.
 *
 * Ini adalah script one-time yang dijalankan langsung oleh cron job,
 * bukan proses yang terus berjalan.
 *
 * Contoh setting cron job di Hostinger:
 * 0 17 * * 1-5 php /home/username/public_html/auto_checkout_command.php
 */

// Memberitahu script bahwa ini berjalan dari CLI
define('STDIN', fopen("php://stdin", "r"));

// Set time limit untuk mencegah script berjalan terlalu lama
set_time_limit(300); // 5 menit

// Load aplikasi Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Dapatkan instance Kernel
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Gunakan DB facade
use Illuminate\Support\Facades\DB;

// Log file
$logFile = __DIR__ . '/storage/logs/auto_checkout.log';

// Fungsi untuk menulis log
function writeLog($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Hanya jalankan proses jika sekarang adalah jam kerja (9-18) pada hari kerja (1-5 = Senin-Jumat)
$currentHour = (int)date('H');
$currentDay = (int)date('N'); // 1 (Senin) sampai 7 (Minggu)

if ($currentDay <= 5 && $currentHour >= 9 && $currentHour <= 18) {
    writeLog("Memulai proses auto checkout");
    try {
        // Ambil semua kehadiran yang belum checkout hari ini
        $today = now()->toDateString();
        $attendances = DB::table('attendances')
            ->whereDate('tanggal', $today)
            ->whereNotNull('check_in')
            ->whereNull('check_out')
            ->get();

        writeLog("Menemukan " . count($attendances) . " kehadiran yang belum checkout");

        foreach ($attendances as $attendance) {
            try {
                // Hanya checkout jika waktu check-in lebih dari 8 jam yang lalu
                // atau jika sekarang sudah lewat dari jam 5 sore
                $checkInTime = \Carbon\Carbon::parse($attendance->check_in);
                $current = \Carbon\Carbon::now();
                $workMinutes = $current->diffInMinutes($checkInTime);

                $isAfter5PM = $current->format('H') >= 17; // Setelah jam 5 sore
                $isWorkingMoreThan8Hours = $workMinutes >= 480; // Sudah bekerja >= 8 jam

                if ($isAfter5PM || $isWorkingMoreThan8Hours) {
                    // Hitung persentase kehadiran (8 jam = 480 menit adalah 100%)
                    $targetMinutes = 480; // 8 jam * 60 menit
                    $attendancePercentage = min(100, round(($workMinutes / $targetMinutes) * 100, 2));

                    // Lakukan update
                    DB::table('attendances')
                        ->where('id', $attendance->id)
                        ->update([
                            'check_out' => now()->format('H:i:s'),
                            'early_leave_reason' => 'Auto checkout by system',
                            'work_hours' => $workMinutes,
                            'attendance_percentage' => $attendancePercentage,
                            'auto_checkout' => true
                        ]);

                    writeLog("Auto checkout berhasil untuk ID: " . $attendance->id . " - " . $attendance->nama);
                } else {
                    writeLog("Melewati checkout untuk ID: " . $attendance->id . " - Belum waktunya (belum jam 5 dan belum 8 jam)");
                }
            } catch (\Exception $e) {
                writeLog("Error saat auto checkout ID: " . $attendance->id . " - " . $e->getMessage());
            }
        }

        writeLog("Proses auto checkout selesai");
    } catch (\Exception $e) {
        writeLog("Error utama: " . $e->getMessage());
    }
} else {
    writeLog("Melewati proses auto checkout - bukan jam/hari kerja (Hari: $currentDay, Jam: $currentHour)");
}
