<?php
// Versi ekstrem aman dari artikel.php tanpa ketergantungan database
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Set variabel articles sebagai array kosong untuk mencegah error
$articles = [];

// Jika config.php ada, coba gunakan database
if (file_exists('config.php')) {
    require_once 'config.php';
    
    // Cek apakah koneksi PDO berhasil dibuat
    if (isset($pdo)) {
        try {
            $stmt = $pdo->query("SELECT * FROM articles ORDER BY created_at DESC");
            $articles = $stmt->fetchAll();
        } catch (Exception $e) {
            // Tidak melakukan apa-apa, tetap gunakan array kosong
            $articles = [];
        }
    }
}

// Jika terjadi kesalahan fatal, tetapkan articles ke array kosong
if (!isset($articles)) {
    $articles = [];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artikel - Azka Hanania Supriyadi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="artikel_specific.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">Azka Hanania S</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.html">Tentang Saya</a></li>
                    <li class="nav-item"><a class="nav-link" href="pengalaman.html">Pengalaman</a></li>
                    <li class="nav-item"><a class="nav-link" href="portofolio.html">Portofolio</a></li>
                    <li class="nav-item"><a class="nav-link" href="skill.html">Skill</a></li>
                    <li class="nav-item"><a class="nav-link" href="kontak.html">Kontak</a></li>
                    <li class="nav-item"><a class="nav-link active" href="artikel.php">Artikel</a></li>
                    <li class="nav-item">
                        <button id="theme-switcher" class="btn btn-outline-secondary ms-2"><i class="bi bi-moon-stars-fill"></i></button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5 mt-4">
        <div class="text-center mb-5 animated-fade-in">
            <h1 class="main-header">Artikel</h1>
            <p class="lead">Tulisan saya tentang berbagai topik.</p>
        </div>
        <div class="row">
            <?php if (empty($articles)): ?>
                <div class="col-12">
                    <div class="soft-card text-center">
                        <p>Belum ada artikel yang dipublikasikan.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($articles as $article): ?>
                    <div class="col-md-6 col-lg-4 mb-4 animated-fade-in">
                        <div class="soft-card h-100 article-card">
                            <div class="article-card-inner">
                                <h3 class="card-title-soft"><?= htmlspecialchars($article['title']) ?></h3>
                                <?php if (!empty($article['image'])): ?>
                                    <div class="article-image-container">
                                        <img src="uploads/<?= htmlspecialchars($article['image']) ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-card-image">
                                    </div>
                                <?php endif; ?>
                                <p><?= substr(strip_tags(html_entity_decode($article['content'])), 0, 150) ?>...</p>
                                <a href="artikel_detail.php?id=<?= $article['id'] ?>" class="btn btn-custom-gradient">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <footer class="footer">
        <span>© 2025 hanania.azka. All Rights Reserved.</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>