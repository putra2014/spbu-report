<?php

/**
 * Test bikin file TMP pakai folder upload_tmp_dir dari php.ini
 * Buka di browser: http://localhost/spbu-report/test_tmp.php
 */

// 1️⃣ Lihat nilai upload_tmp_dir aktif
$tmpDir = ini_get('upload_tmp_dir');
echo "<h3>upload_tmp_dir: $tmpDir</h3>";

if (!$tmpDir) {
    echo "<p style='color:red;'>❌ upload_tmp_dir TIDAK DISET. PHP pakai default sistem TMP.</p>";
}

// 2️⃣ Buat nama file unik di TMP
$tmpFile = tempnam($tmpDir ?: sys_get_temp_dir(), 'php_test_');

// 3️⃣ Coba tulis isi
$success = file_put_contents($tmpFile, "Hello, this is a test file in TMP folder.\n");

if ($success !== false) {
    echo "<p style='color:green;'>✅ File tmp berhasil dibuat: $tmpFile</p>";
    echo "<p>Isi file: " . htmlspecialchars(file_get_contents($tmpFile)) . "</p>";
    echo "<p style='color:blue;'>Hapus file tmp sekarang untuk bersih-bersih...</p>";
    unlink($tmpFile);
} else {
    echo "<p style='color:red;'>❌ Gagal membuat file tmp di: $tmpFile</p>";
}
