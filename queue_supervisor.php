<?php

/**
 * Queue Worker Supervisor Script
 *
 * Script ini akan menjalankan queue worker untuk memproses pekerjaan yang ada di antrian.
 * Script ini akan dijalankan oleh cron job setiap 5 menit dan tidak menjalankan
 * queue worker sebagai proses yang terus berjalan.
 */

// Setup path file
$logFile = __DIR__ . '/storage/logs/queue_worker.log';

// Fungsi untuk menulis log
function writeLog($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

writeLog("Memulai eksekusi queue worker");

// Jalankan artisan command untuk memproses satu job per eksekusi
// dengan opsi --once untuk memastikan berhenti setelah selesai
$phpPath = '/usr/local/bin/php'; // Updated PHP path
$command = $phpPath . ' ' . __DIR__ . '/artisan queue:work --once --sleep=3 --tries=3 --timeout=60';
writeLog("Menjalankan command: $command");

$startTime = microtime(true);
$output = [];
$returnCode = null;

// Jalankan proses dan tangkap output-nya
exec($command, $output, $returnCode);

// Hitung waktu eksekusi
$executionTime = microtime(true) - $startTime;

// Log hasil eksekusi
$outputStr = implode("\n", $output);
writeLog("Output: $outputStr");
writeLog("Return code: $returnCode");
writeLog("Waktu eksekusi: " . round($executionTime, 2) . " detik");

// Hapus tugas-tugas yang sudah selesai lebih dari 24 jam
exec($phpPath . ' ' . __DIR__ . '/artisan queue:prune-failed --hours=24');
writeLog("Membersihkan failed jobs yang lama");
writeLog("Selesai eksekusi queue worker");
