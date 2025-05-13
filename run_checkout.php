<?php

/**
 * File sederhana untuk menjalankan auto checkout
 */

// Tentukan path aplikasi
$appPath = __DIR__;

// Log file
$logFile = $appPath . '/storage/logs/auto_checkout.log';

// Tulis log
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Starting auto checkout..." . PHP_EOL, FILE_APPEND);

// Jalankan auto checkout command
$command = "cd " . escapeshellarg($appPath) . " && php artisan attendance:auto-checkout";
exec($command, $output, $returnCode);

// Tulis hasil
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Return code: $returnCode" . PHP_EOL, FILE_APPEND);
file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Output: " . implode("\n", $output) . PHP_EOL, FILE_APPEND);
