<?php
require_once 'config.php';

// Get article ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the specific article
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// If article not found, redirect to articles page
if (!$article) {
    header("Location: artikel.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - Azka Hanania Supriyadi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .article-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 15px;
            margin-bottom: 1.5rem;
        }
    </style>
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
                    <li class="nav-item"><a class="nav-link" href="projek.html">Project</a></li>
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="soft-card">
                    <h1 class="main-header"><?= htmlspecialchars($article['title']) ?></h1>
                    <p class="text-muted">Dipublikasikan pada: <?= date('d F Y', strtotime($article['created_at'])) ?></p>
                    <hr>

                    <?php 
                    if (!empty($article['image'])):
                        $image_path = 'uploads/' . htmlspecialchars($article['image']);
                        if (file_exists($image_path)):
                    ?>
                        <img src="<?= $image_path ?>" alt="<?= htmlspecialchars($article['title']) ?>" class="article-image">
                    <?php 
                        endif;
                    endif; 
                    ?>

                    <div class="article-content">
                        <?php // Directly output the content as it is HTML from TinyMCE ?>
                        <?= html_entity_decode($article['content']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <span>© 2025 hanania.azka. All Rights Reserved.</span>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>
</html>