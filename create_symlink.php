<?php
// Buat direktori jika belum ada
if (!file_exists('public/storage')) {
    mkdir('public/storage', 0755, true);
}

// Pastikan direktori storage/app/public juga ada
if (!file_exists('storage/app/public')) {
    mkdir('storage/app/public', 0755, true);
}

// Salin file dari storage/app/public ke public/storage
$source = 'storage/app/public';
$dest = 'public/storage';

// Fungsi untuk memeriksa apakah direktori ada dan membuatnya jika belum ada
function ensureDirectoryExists($path)
{
    if (!file_exists($path)) {
        mkdir($path, 0755, true);
        echo "Created directory: $path\n";
        // Buat file .gitignore di folder untuk menjaga struktur
        file_put_contents($path . '/.gitignore', "*\n!.gitignore\n");
    } else {
        echo "Directory already exists: $path\n";
    }
}

function copyDir($src, $dst)
{
    $dir = opendir($src);
    @mkdir($dst);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            if (is_dir($src . '/' . $file)) {
                copyDir($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    closedir($dir);
}

// Buat direktori yang diinginkan jika belum ada
// Sesuaikan dengan kebutuhan sistem absensi karyawan
$dirs = [
    'employees', // Untuk foto profil karyawan
    'attendances', // Untuk bukti kehadiran
    'reports', // Untuk laporan
    'documents', // Untuk dokumen-dokumen terkait
    'imports', // Untuk file impor data
    'exports', // Untuk file ekspor data
    'temp', // Untuk file sementara
    'qrcodes', // Untuk kode QR (jika menggunakan QR untuk absensi)
    'logos', // Logo perusahaan
    'signatures', // Tanda tangan digital
    'backups', // Backup data
];

echo "Checking and creating directories if needed:\n";
foreach ($dirs as $dir) {
    // Buat direktori di public/storage
    ensureDirectoryExists($dest . '/' . $dir);

    // Buat direktori yang sama di storage/app/public
    ensureDirectoryExists($source . '/' . $dir);
}

// Salin konten dari storage/app/public jika ada yang belum disalin
if (is_dir($source) && count(scandir($source)) > 2) {
    echo "\nCopying any new files from $source to $dest...\n";
    $copied = false;

    // Hanya salin file yang belum ada
    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcPath = $source . '/' . $file;
            $destPath = $dest . '/' . $file;

            if (!file_exists($destPath)) {
                if (is_dir($srcPath)) {
                    copyDir($srcPath, $destPath);
                } else {
                    copy($srcPath, $destPath);
                }
                $copied = true;
                echo "Copied: $file\n";
            }
        }
    }
    closedir($dir);

    if (!$copied) {
        echo "No new files to copy.\n";
    }
} else {
    echo "\nSource directory is empty or doesn't exist. No files to copy.\n";
}

echo "\nCompleted. The public/storage directory now contains all the required folders.\n";
echo "Note: For a proper Laravel symlink, you can also run 'php artisan storage:link' if accessible.\n";
