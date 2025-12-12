<?php
require_once "config.php";

// Mulai session jika belum ada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if not logged in
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Konstanta untuk path upload
define("UPLOAD_PATH", "uploads/");

// Fungsi untuk menangani upload file gambar
function handle_upload($file_input) {
    if (isset($file_input) && $file_input["error"] == 0) {
        $allowed = ["jpg" => "image/jpeg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
        $filename = $file_input["name"];
        $filetype = $file_input["type"];
        $filesize = $file_input["size"];

        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (!array_key_exists($ext, $allowed)) {
            return ["error" => "Format file tidak valid. Hanya JPG, JPEG, PNG, GIF yang diizinkan."];
        }

        $maxsize = 5 * 1024 * 1024; // 5MB
        if ($filesize > $maxsize) {
            return ["error" => "Ukuran file terlalu besar. Maksimal 5MB."];
        }

        $new_filename = uniqid("img_", true) . "." . $ext;
        if (move_uploaded_file($file_input["tmp_name"], UPLOAD_PATH . $new_filename)) {
            return ["filename" => $new_filename];
        }
    }
    return ["error" => "Gagal mengupload file."];
}

// Handle Hapus Artikel
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    try {
        $pdo->beginTransaction();
        // Ambil nama file gambar untuk dihapus
        $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $article = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($article && !empty($article['image']) && file_exists(UPLOAD_PATH . $article['image'])) {
            unlink(UPLOAD_PATH . $article['image']);
        }

        // Hapus artikel dari database
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Error: " . $e->getMessage());
    }
    header("location: admin.php");
    exit;
}

// Handle Tambah/Edit Artikel
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $id = $_POST['id'] ?? null;
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $existing_image = $_POST['existing_image'] ?? '';
    $final_image = $existing_image;

    // Cek apakah ada file gambar baru yang diupload
    if (isset($_FILES["article_image"]) && $_FILES["article_image"]["error"] == 0) {
        $upload_result = handle_upload($_FILES["article_image"]);
        if (isset($upload_result['filename'])) {
            // Jika berhasil, hapus gambar lama (jika ada)
            if (!empty($existing_image) && file_exists(UPLOAD_PATH . $existing_image)) {
                unlink(UPLOAD_PATH . $existing_image);
            }
            $final_image = $upload_result['filename'];
        } else {
            // Handle error upload jika perlu
            // die($upload_result['error']);
        }
    }

    try {
        if (empty($id)) { // Tambah baru
            $sql = "INSERT INTO articles (title, content, image) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $final_image]);
        } else { // Update
            $sql = "UPDATE articles SET title = ?, content = ?, image = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$title, $content, $final_image, $id]);
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
    header("location: admin.php");
    exit;
}

// Ambil data untuk form edit
$edit_article = ['id' => '', 'title' => '', 'content' => '', 'image' => ''];
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT id, title, content, image FROM articles WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() == 1) {
        $edit_article = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Ambil semua artikel untuk ditampilkan di tabel
$articles = [];
try {
    $stmt = $pdo->query("SELECT id, title, created_at FROM articles ORDER BY created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Could not retrieve articles: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- TinyMCE Script -->
    <script src="https://cdn.tiny.cloud/1/5axlc3i2mlgfplw6s1nb8lhqiz0c1dyuv3g2bpz3e7ji76qs/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        tinymce.init({
            selector: 'textarea#content',
            plugins: 'code table lists image link',
            toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | indent outdent | bullist numlist | code | table | image link'
        });
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="artikel.php" target="_blank">Lihat Website</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-5">
                <div class="soft-card">
                    <h3 class="card-title-soft"><?php echo empty($edit_article['id']) ? 'Tambah' : 'Edit'; ?> Artikel</h3>
                    <form action="admin.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit_article['id']); ?>">
                        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($edit_article['image']); ?>">
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Judul</label>
                            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($edit_article['title']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Konten</label>
                            <textarea name="content" id="content" class="form-control" rows="10"><?php echo htmlspecialchars($edit_article['content']); ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="article_image" class="form-label">Gambar Artikel</label>
                            <input type="file" name="article_image" id="article_image" class="form-control">
                            <?php if (!empty($edit_article['image'])):
                                $image_path = UPLOAD_PATH . htmlspecialchars($edit_article['image']);
                                if (file_exists($image_path)):
                            ?>
                                <div class="mt-2">
                                    <small>Gambar saat ini:</small><br>
                                    <img src="<?php echo $image_path; ?>" alt="Article Image" style="max-width: 150px; border-radius: 8px;">
                                </div>
                            <?php 
                                endif;
                            endif; 
                            ?>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary" style="background-color: #d16081; border-color: #d16081;">Simpan Artikel</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="soft-card">
                    <h3 class="card-title-soft">Daftar Artikel</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Tanggal</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($articles)): ?>
                                    <?php foreach ($articles as $article): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                                        <td><?php echo date("d M Y", strtotime($article['created_at'])); ?></td>
                                        <td>
                                            <a href="admin.php?action=edit&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="admin.php?action=delete&id=<?php echo $article['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus artikel ini? Ini tidak bisa dibatalkan.')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center">Belum ada artikel.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <span>© 2025 hanania.azka. All Rights Reserved.</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>