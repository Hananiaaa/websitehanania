<?php
// Skrip untuk mengecek koneksi database dan tabel articles
require_once 'config.php';

echo "<h2>Hasil Pengecekan Database</h2>";

try {
    // Mengecek koneksi database
    echo "<p style='color: green;'>✅ Koneksi database berhasil</p>";
    
    // Mengecek apakah tabel articles ada
    $stmt = $pdo->query("SHOW TABLES LIKE 'articles'");
    if ($stmt->rowCount() > 0) {
        echo "<p style='color: green;'>✅ Tabel 'articles' ditemukan</p>";
        
        // Mengecek jumlah artikel
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM articles");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p style='color: green;'>✅ Jumlah artikel dalam database: " . $result['count'] . "</p>";
        
        // Mencoba query yang digunakan di artikel.php
        $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
        $articles = $stmt->fetchAll();
        echo "<p style='color: green;'>✅ Query SELECT berhasil dijalankan</p>";
        
        if (count($articles) > 0) {
            echo "<p style='color: green;'>✅ Data artikel berhasil diambil</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Tidak ada artikel dalam database</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Tabel 'articles' tidak ditemukan dalam database</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}

// Menampilkan info database dari config
echo "<h3>Info Database</h3>";
echo "<p>DB_NAME: " . DB_NAME . "</p>";
echo "<p>DB_SERVER: " . DB_SERVER . "</p>";
echo "<p>DB_USERNAME: " . DB_USERNAME . "</p>";
?>