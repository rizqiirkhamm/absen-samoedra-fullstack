<?php
// Fix untuk Laravel Storage di server

echo "Membuat symlink untuk Laravel Storage...\n";

// Path untuk domain absen.maindisamoedra.com
$domainPath = __DIR__; // Path saat ini, sesuaikan jika berbeda

// Periksa apakah direktori storage/app/public/attendances ada
$sourcePath = $domainPath . '/storage/app/public';
$destinationPath = $domainPath . '/public/storage';

if (!file_exists($sourcePath)) {
    mkdir($sourcePath, 0755, true);
    echo "Dibuat: $sourcePath\n";
}

if (!file_exists($sourcePath . '/attendances')) {
    mkdir($sourcePath . '/attendances', 0755, true);
    echo "Dibuat: $sourcePath/attendances\n";
}

// Hapus symlink atau folder storage yang ada jika ada
if (file_exists($destinationPath)) {
    if (is_link($destinationPath)) {
        unlink($destinationPath);
        echo "Symlink lama dihapus.\n";
    } else {
        // Jika direktori biasa, hapus atau pindahkan kontennya
        echo "Folder storage ditemukan. Memindahkan file...\n";

        // Salin konten dari folder destinasi ke sumber jika ada
        if (file_exists($destinationPath . '/attendances')) {
            if (!is_dir($sourcePath . '/attendances')) {
                mkdir($sourcePath . '/attendances', 0755, true);
            }

            $files = glob($destinationPath . '/attendances/*');
            foreach ($files as $file) {
                $name = basename($file);
                copy($file, $sourcePath . '/attendances/' . $name);
                echo "File dipindahkan: " . $name . "\n";
            }
        }

        // Hapus folder storage
        system('rm -rf ' . escapeshellarg($destinationPath));
        echo "Folder lama dihapus.\n";
    }
}

// Buat symlink baru
if (symlink($sourcePath, $destinationPath)) {
    echo "Symlink berhasil dibuat: $sourcePath -> $destinationPath\n";
} else {
    echo "GAGAL membuat symlink! Coba jalankan manual: ln -s $sourcePath $destinationPath\n";

    // Coba metode alternatif
    system('ln -s ' . escapeshellarg($sourcePath) . ' ' . escapeshellarg($destinationPath));
    if (is_link($destinationPath)) {
        echo "Symlink berhasil dibuat menggunakan perintah sistem.\n";
    } else {
        echo "GAGAL membuat symlink bahkan dengan perintah sistem.\n";
    }
}

// Periksa hasil
if (is_link($destinationPath)) {
    echo "\nBerhasil! Symlink sudah dibuat dengan benar.\n";
    echo "Gambar seharusnya sekarang dapat diakses di URL: https://absen.maindisamoedra.com/storage/attendances/[filename]\n";
} else {
    echo "\nGAGAL membuat symlink. Silakan coba secara manual:\n";
    echo "1. SSH ke server\n";
    echo "2. Jalankan: cd " . $domainPath . "\n";
    echo "3. Jalankan: rm -rf public/storage\n";
    echo "4. Jalankan: ln -s storage/app/public public/storage\n";
}

echo "\nSelesai!\n";
