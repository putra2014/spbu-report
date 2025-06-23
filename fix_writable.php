<?php

/**
 * Jalankan file ini sekali via browser atau terminal:
 * php fix_writable.php
 */

$paths = [
    __DIR__ . '/writable/session',
    __DIR__ . '/writable/tmp',
    __DIR__ . '/writable/uploads',
];

foreach ($paths as $path) {
    if (!is_dir($path)) {
        if (mkdir($path, 0777, true)) {
            echo "✅ Folder dibuat: $path\n";
        } else {
            echo "❌ Gagal membuat folder: $path\n";
        }
    } else {
        echo "ℹ️  Folder sudah ada: $path\n";
    }

    // Set permission
    if (chmod($path, 0777)) {
        echo "✅ Permission di-set 0777: $path\n";
    } else {
        echo "❌ Gagal set permission: $path\n";
    }

    echo "-------------------------------\n";
}

echo "🎉 Selesai! Semua folder wajib sudah aman.\n";
