<?php
echo "<h2>Pemeriksaan Database 'hananiacv'</h2>";

// Baca file config.php
if (!file_exists('config.php')) {
    die("<p style='color: red;'>❌ File config.php tidak ditemukan!</p>");
}

require_once 'config.php';

try {
    // Periksa koneksi
    echo "<p style='color: green;'>✅ Koneksi database berhasil dibuat</p>";
    
    // Periksa nama database yang digunakan
    $result = $pdo->query("SELECT DATABASE()");
    $current_db = $result->fetchColumn();
    echo "<p>Database saat ini: <strong>$current_db</strong></p>";
    
    if ($current_db !== DB_NAME) {
        echo "<p style='color: orange;'>⚠️ Nama database tidak sesuai konfigurasi</p>";
    }
    
    // Periksa tabel yang ada
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<h3>Tabel-tabel dalam database:</h3>";
    foreach ($tables as $table) {
        echo "<p>• $table</p>";
    }
    
    // Periksa apakah tabel 'articles' ada
    if (in_array('articles', $tables)) {
        echo "<p style='color: green;'>✅ Tabel 'articles' ditemukan</p>";
        
        // Hitung jumlah artikel
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM articles");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Jumlah artikel dalam database: <strong>" . $count['total'] . "</strong></p>";
        
        // Jika ada artikel, tampilkan beberapa kolom terpenting
        if ($count['total'] > 0) {
            $stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'articles'");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            echo "<h3>Kolom-kolom dalam tabel 'articles':</h3>";
            foreach ($columns as $column) {
                echo "<p>• $column</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Tabel 'articles' TIDAK DITEMUKAN dalam database</p>";
        echo "<p>Anda perlu mengimpor struktur tabel ke database 'hananiacv'</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<p>Periksa kembali konfigurasi database di config.php</p>";
}
?>