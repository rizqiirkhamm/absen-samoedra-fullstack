<?php

/**
 * Queue Worker Supervisor Script
 *
 * Script ini akan memeriksa apakah queue worker sedang berjalan,
 * dan memulainya jika tidak. Script ini juga mencatat log.
 */

 

// Path ke file lock
$lockFile = __DIR__ . '/storage/queue_worker.lock';
$logFile = __DIR__ . '/storage/logs/queue_worker.log';

// Fungsi untuk menulis log
function writeLog($message)
{
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Periksa jika lock file ada, jika tidak, buat lock file
if (!file_exists($lockFile)) {
    // Buat timestamp untuk lock file
    file_put_contents($lockFile, time());
    writeLog("Queue worker tidak berjalan, memulai worker...");

    // Jalankan queue worker sebagai proses background
    exec('nohup /usr/bin/php ' . __DIR__ . '/artisan queue:work --sleep=3 --tries=3 --timeout=60 > /dev/null 2>&1 &');
    writeLog("Queue worker dimulai");
} else {
    // Periksa jika lock file sudah terlalu lama (lebih dari 15 menit)
    $lockTime = file_get_contents($lockFile);
    if ((time() - $lockTime) > (15 * 60)) {
        // Lock file terlalu lama, mungkin worker crash
        writeLog("Lock file terlalu lama, restarting worker...");
        unlink($lockFile);
        file_put_contents($lockFile, time());

        // Cari dan hentikan semua proses queue worker yang ada
        exec("pkill -f 'php artisan queue:work'");

        // Mulai ulang queue worker
        exec('nohup /usr/bin/php ' . __DIR__ . '/artisan queue:work --sleep=3 --tries=3 --timeout=60 > /dev/null 2>&1 &');
        writeLog("Queue worker direstart");
    } else {
        // Update timestamp lock file
        file_put_contents($lockFile, time());
        writeLog("Queue worker sedang berjalan");
    }
}

// Hapus tugas-tugas yang sudah selesai lebih dari 24 jam
exec('/usr/bin/php ' . __DIR__ . '/artisan queue:prune-failed --hours=24');
writeLog("Membersihkan failed jobs yang lama");
