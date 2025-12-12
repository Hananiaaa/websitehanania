<?php
require_once 'config.php';

echo "<style>body { font-family: sans-serif; padding: 2em; }</style>";

try {
    // Cek dulu apakah kolom sudah ada
    $stmt = $pdo->query("SHOW COLUMNS FROM `articles` LIKE 'image'");
    $exists = $stmt->rowCount() > 0;

    if ($exists) {
        echo "<h1>Sudah Terbaru!</h1>";
        echo "<p>Struktur database Anda sudah memiliki kolom 'image'. Tidak ada yang perlu dilakukan.</p>";
        echo "<a href='artikel.php'>Kembali ke Halaman Artikel</a>";
    } else {
        // Jika belum ada, jalankan perintah ALTER TABLE
        $sql = "ALTER TABLE `articles` ADD `image` VARCHAR(255) NULL DEFAULT NULL AFTER `content`";

        $pdo->exec($sql);
        echo "<h1>Sukses!</h1>";
        echo "<p>Struktur database berhasil diperbarui! Kolom 'image' telah ditambahkan ke tabel 'articles'.</p>";
        echo "<p>Halaman artikel seharusnya sudah berfungsi sekarang.</p>";
        echo "<a href='artikel.php'>Lihat Halaman Artikel</a>";
    }
} catch (PDOException $e) {
    echo "<h1>Error!</h1>";
    echo "<p>Gagal memperbarui struktur database: " . $e->getMessage() . "</p>";
}

// Tidak perlu unset($pdo) karena akan digunakan di tempat lain jika diperlukan
?>