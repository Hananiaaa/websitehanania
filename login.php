<?php
require_once "config.php";

$error = '';
// Cek apakah user sudah login, jika ya, redirect ke halaman admin
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = 'Mohon masukkan username dan password.';
    } else {
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() == 1) {
                    if ($row = $stmt->fetch()) {
                        $id = $row["id"];
                        $hashed_password = $row["password"];
                        if (password_verify($password, $hashed_password)) {
                            // Password benar, mulai session baru
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $id;
                            $_SESSION["username"] = $username;
                            
                            header("location: admin.php");
                            exit;
                        } else {
                            $error = 'Password yang Anda masukkan salah.';
                        }
                    }
                } else {
                    $error = 'Tidak ada akun yang ditemukan dengan username tersebut.';
                }
            } else {
                $error = "Oops! Terjadi kesalahan. Silakan coba lagi nanti.";
            }
            unset($stmt);
        }
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-5">
            <div class="soft-card">
                <h3 class="card-title-soft text-center">Admin Login</h3>
                <?php if(!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" id="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" style="background-color: #d16081; border-color: #d16081;">Login</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <small>Lupa password? <a href="reset_admin_password.php">Reset di sini</a>.</small>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
