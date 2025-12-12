<?php
require_once 'config.php';

echo "<!DOCTYPE html><html lang='id'><head><meta charset='UTF-8'><title>Reset Password</title><link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css' rel='stylesheet'><link rel='stylesheet' href='css/style.css'></head><body><div class='container vh-100 d-flex justify-content-center align-items-center'><div class='col-md-6'><div class='soft-card text-center'>";

// Password baru yang akan kita set
$new_password = 'admin123';

// Enkripsi password
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

try {
    // Siapkan perintah SQL untuk update
    $sql = "UPDATE users SET password = :password WHERE username = 'admin'";
    $stmt = $pdo->prepare($sql);

    // Bind parameter
    $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);

    // Eksekusi statement
    if ($stmt->execute()) {
        // Cek apakah ada baris yang terpengaruh
        if ($stmt->rowCount() > 0) {
            echo "<h1 class='card-title-soft'>Sukses!</h1>";
            echo "<p class='lead'>Password untuk user 'admin' berhasil direset.</p>";
            echo "<p>Silakan login dengan password baru: <strong class='text-danger'>{$new_password}</strong></p>";
            echo "<a href='login.php' class='btn btn-primary mt-3'>Kembali ke Halaman Login</a>";
        } else {
            echo "<h1 class='card-title-soft text-warning'>Informasi</h1>";
            echo "<p class='lead'>Tidak ada user 'admin' yang ditemukan untuk direset.</p>";
            echo "<p>Apakah Anda sudah menjalankan file <code>setup.sql</code>?</p>";
            echo "<a href='login.php' class='btn btn-secondary mt-3'>Kembali ke Halaman Login</a>";
        }
    } else {
        echo "<h1 class='card-title-soft text-danger'>Error!</h1>";
        echo "<p class='lead'>Terjadi kesalahan saat mencoba mereset password.</p>";
    }
} catch (PDOException $e) {
    die("<h1 class='card-title-soft text-danger'>Error!</h1><p class='lead'>Gagal terhubung ke database: " . $e->getMessage() . "</p>");
}

echo "</div></div></div></body></html>";

// Tutup koneksi (PDO tidak perlu ditutup secara eksplisit)
unset($pdo);
?>
