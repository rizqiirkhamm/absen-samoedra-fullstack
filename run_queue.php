<?php

/**
 * File sederhana untuk menjalankan queue worker
 */

// Tentukan path aplikasi
$appPath = __DIR__;

// Log file
$logFile = $appPath . '/storage/logs/queue.log';

// Tulis log
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Starting queue worker..." . PHP_EOL, FILE_APPEND);

// Jalankan command
$command = "cd " . escapeshellarg($appPath) . " && php artisan queue:work --once --sleep=3 --tries=3 --timeout=60";
exec($command, $output, $returnCode);

// Tulis hasil
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Return code: $returnCode" . PHP_EOL, FILE_APPEND);
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Output: " . implode("\n", $output) . PHP_EOL, FILE_APPEND);
