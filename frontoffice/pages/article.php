<?php
require_once '../includes/article_repository.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$slug = isset($_GET['slug']) ? $_GET['slug'] : null;

// Prioriser l'ID pour la recherche (le slug peut changer)
if (!$id && !$slug) {
    header('Location: /');
    exit;
}

// Récupérer l'article par ID en priorité, sinon par slug
if ($id) {
    $article = getArticleById($id);
} else {
    $article = getArticleBySlug($slug);
}

if (!$article) {
    header('HTTP/1.0 404 Not Found');
    exit('Article non trouvé');
}

// Si le slug en URL ne correspond pas au slug en base, rediriger vers la bonne URL
if ($slug && $article['slug'] !== $slug) {
    $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d', strtotime($article['date_creation']));
    $dateParts = explode('-', $date);
    header('Location: /' . $dateParts[0] . '/' . $dateParts[1] . '/' . $dateParts[2] . '/' . $article['slug'] . '_' . $article['id'] . '.html');
    exit;
}

// Récupérer le contenu de l'article
$contenus = getArticleContentById($article['id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['titre_navigation']); ?> - Iran War</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['meta_description']); ?>">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/article.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1><a href="/" class="accueil-header">Iran War</a></h1>
                    <p class="tagline">Actualités et Analyse</p>
                </div>
                <ul class="nav-links">
                    <li><a href="/">Accueil</a></li>
                    <li><a href="/articles">Articles</a></li>
                    <li><a href="http://localhost:8081/connexion" class="btn-backoffice">Backoffice</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container">
        <article class="article-full">
            <div class="article-header-full">
                <h1><?php echo htmlspecialchars($article['titre_navigation']); ?></h1>
                <div class="article-meta-full">
                    <span class="date">Publié le <?php echo date('d F Y', strtotime($article['date_creation'])); ?></span>
                </div>
            </div>

            <div class="article-content">
                <?php 
                if (empty($contenus)): 
                    echo '<p>Contenu non disponible</p>';
                else:
                    foreach ($contenus as $contenu):
                        // Ignore seulement les <h1> issus de l'editeur: le titre est deja affiche en haut.
                        $valeur = (string) ($contenu['valeur'] ?? '');
                        if (preg_match('/^\s*<h1\b/i', $valeur) === 1) {
                            continue;
                        }
                        echo $valeur;
                    endforeach;
                endif;
                ?>
            </div>

            <div class="article-footer-full">
                <a href="/" class="btn-back">← Retour à l'accueil</a>
            </div>
        </article>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h4>À propos</h4>
                    <p>Un site d'information dédié à la couverture de la guerre en Iran avec rigueur et transparence.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens utiles</h4>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/pages/articles.php">Articles</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Iran War - All rights reserved</p>
            </div>
        </div>
    </footer>
</body>
</html>
