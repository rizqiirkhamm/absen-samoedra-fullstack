<?php
// Script khusus untuk server yang tidak mendukung perintah artisan storage:link

// Path ke direktori publik dan storage
$publicPath = __DIR__ . '/public/storage';
$storagePath = __DIR__ . '/storage/app/public';

echo "Checking paths...\n";
echo "Public path: $publicPath\n";
echo "Storage path: $storagePath\n";

// Hapus symlink yang sudah ada jika ada
if (is_link($publicPath)) {
    echo "Removing existing symlink...\n";
    unlink($publicPath);
}
// Hapus direktori public/storage jika bukan symlink
elseif (is_dir($publicPath)) {
    echo "public/storage exists but is not a symlink. Moving content to storage/app/public...\n";

    // Salin konten dari public/storage ke storage/app/public sebelum menghapus
    // Buat direktori storage/app/public jika belum ada
    if (!is_dir($storagePath)) {
        mkdir($storagePath, 0755, true);
        echo "Created directory: $storagePath\n";
    }

    // Fungsi untuk menyalin direktori
    function copyDirectoryContents($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $srcPath = $src . '/' . $file;
                $dstPath = $dst . '/' . $file;

                if (is_dir($srcPath)) {
                    copyDirectoryContents($srcPath, $dstPath);
                } else {
                    copy($srcPath, $dstPath);
                    echo "Copied: $file\n";
                }
            }
        }
        closedir($dir);
    }

    // Salin konten dari public/storage ke storage/app/public
    copyDirectoryContents($publicPath, $storagePath);

    // Hapus direktori public/storage
    function removeDirectory($dir)
    {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (is_dir($dir . "/" . $object)) {
                        removeDirectory($dir . "/" . $object);
                    } else {
                        unlink($dir . "/" . $object);
                    }
                }
            }
            rmdir($dir);
        }
    }

    removeDirectory($publicPath);
    echo "Removed directory: $publicPath\n";
}

// Buat symlink baru
try {
    echo "Creating symlink...\n";

    // Metode 1: Menggunakan symlink PHP
    if (symlink($storagePath, $publicPath)) {
        echo "Symlink created successfully using PHP symlink function.\n";
    }
    // Metode 2: Jika metode 1 gagal, coba dengan command line
    else {
        echo "PHP symlink function failed. Trying with command line...\n";

        // Pada shared hosting, symlink mungkin tidak berfungsi
        // Sebagai alternatif, buat junction directory (pada Windows) atau
        // symlink dengan perintah shell (pada Linux/Unix)
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            // Windows
            system("mklink /J \"{$publicPath}\" \"{$storagePath}\"");
        } else {
            // Linux/Unix/Mac
            system("ln -sf \"{$storagePath}\" \"{$publicPath}\"");
        }

        if (is_link($publicPath) || is_dir($publicPath)) {
            echo "Symlink created successfully using command line.\n";
        } else {
            throw new Exception("Failed to create symlink with command line too.");
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";

    // Metode 3: Jika semua metode symlink gagal, gunakan file konfigurasi .htaccess
    echo "Trying alternative method with .htaccess...\n";

    // Buat file .htaccess di public/storage
    if (!is_dir($publicPath)) {
        mkdir($publicPath, 0755, true);
    }

    $htaccessContent = "RewriteEngine On\n";
    $htaccessContent .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
    $htaccessContent .= "RewriteRule ^(.*)$ " . str_replace($_SERVER['DOCUMENT_ROOT'], '', $storagePath) . "/$1 [L]\n";

    file_put_contents("$publicPath/.htaccess", $htaccessContent);
    echo "Created .htaccess for redirection.\n";
}

echo "\nSymlink process completed. Please check if your images are now accessible.\n";
echo "URL path should be: yoursite.com/storage/attendances/your-image.png\n";
