<?php
// Skrip diagnosis database lebih lengkap
require_once 'config.php';

echo "<h2>Diagnosis Lengkap Database</h2>";

try {
    // 1. Cek koneksi dasar
    echo "<h3>1. Koneksi Database</h3>";
    echo "<p style='color: green;'>✅ Koneksi database berhasil dibuat</p>";

    // 2. Cek apakah database yang benar dipilih
    $result = $pdo->query("SELECT DATABASE()");
    $current_db = $result->fetchColumn();
    echo "<p>Database saat ini: $current_db</p>";
    
    // 3. Cek semua tabel yang tersedia
    echo "<h3>2. Tabel-tabel dalam database</h3>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "<p>• $table</p>";
    }
    
    // 4. Cek struktur tabel articles jika ada
    if (in_array('articles', $tables)) {
        echo "<h3>3. Struktur Tabel Articles</h3>";
        $stmt = $pdo->query("DESCRIBE articles");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $column) {
            echo "<p>• " . $column['Field'] . " (" . $column['Type'] . ")</p>";
        }
        
        // 5. Coba query yang bermasalah
        echo "<h3>4. Mencoba Query yang Bermasalah</h3>";
        try {
            $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
            $articles = $stmt->fetchAll();
            echo "<p style='color: green;'>✅ Query berhasil dijalankan</p>";
            echo "<p>Jumlah artikel yang ditemukan: " . count($articles) . "</p>";
        } catch (PDOException $e) {
            echo "<p style='color: red;'>❌ Query gagal: " . $e->getMessage() . "</p>";
        }
    } else {
        echo "<h3>3. Kesalahan</h3>";
        echo "<p style='color: red;'>❌ Tabel 'articles' tidak ditemukan dalam database</p>";
        echo "<p>Anda perlu mengimpor file SQL 'dbcv (2).sql' ke database MySQL Anda</p>";
    }
    
} catch (PDOException $e) {
    echo "<h3>Kesalahan Koneksi</h3>";
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Periksa kembali konfigurasi database di config.php</p>";
}
?>