<?php
require_once '../includes/article_repository.php';
header('Content-Type: text/html; charset=utf-8');

$articles = getAllArticles();
$categories = getCategories();

?>
<?php $version = filemtime('style.css'); ?>
<?php $version1 = filemtime('articles-list.css'); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guerre en Iran - Articles et Actualités</title>
    <meta name="description" content="Découvrez nos articles de fond sur la guerre en Iran: analyses, reportages, chronologies et documents inédits.">
    <link rel="stylesheet" href="/assets/style.css?v=<?php echo $version; ?>">
    <link rel="stylesheet" href="/assets/articles-list.css?v=<?php echo $version1; ?>">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="logo">
                    <h1><a href="/">Iran War</a></h1>
                    <p class="tagline">Actualités et Analyse</p>
                </div>
                <ul class="nav-links">
                    <li><a href="/" class="active">Accueil</a></li>
                    <li><a href="http://localhost:8081/connexion" class="btn-backoffice">Backoffice</a></li>
                </ul>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero">
            <div class="hero-content">
                <h2>Suivez l'actualité de la Guerre en Iran</h2>
                <p>Analyses approfondies, reportages exclusifs et documentations complètes</p>
                <a href="#articles" class="btn-primary">Découvrir nos articles</a>
            </div>
        </section>
    </header>

    <!-- Main Content -->
    <main class="container">
        <!-- Introduction Section -->
        <section class="intro-section">
            <h2>Bienvenue</h2>
            <p>
                Ce site vous propose une couverture complète des événements liés à la guerre en Iran. 
                Nos journalistes et analystes vous offrent des articles approfondis, 
                des analyses géopolitiques et des reportages exclusifs pour vous tenir informé 
                des derniers développements.
            </p>
        </section>

        <!-- Articles Section -->
        <section id="articles" class="articles-section">
            <h2>Nos Articles</h2>

            <!-- Filter Section -->
            <section class="filter-section">
                <div class="search-box">
                    <label for="searchInput" class="sr-only">Rechercher un article</label>
                    <input type="text" id="searchInput" placeholder="Rechercher un article..." class="search-input" aria-label="Rechercher un article">
                    <!-- image de loupe pour le champ de recherche -->
                    <span class="search-icon" aria-hidden="true"><img class="loupe" src="../assets/images/glassmagnifiermagnifyingsearchsearchingweb_123111.svg" alt="Rechercher" width="20" height="20"></span>
                </div>
                <div class="sort-controls">
                    <label for="categorySelect">Filtrer par catégorie:</label>
                    <select id="categorySelect" class="sort-select" aria-label="Filtrer par catégorie">
                        <option value="">Toutes les catégories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo (int) $category['id']; ?>">
                                <?php echo htmlspecialchars($category['libelle'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </section>

        <!-- Articles List -->
        <?php if (empty($articles)): ?>
            <div class="no-articles-large">
                <p>Aucun article disponible pour le moment. Revenez bientôt !</p>
            </div>
        <?php else: ?>
            <section class="articles-list">
                <div id="articlesContainer" class="articles-list-grid">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-list-item" data-title="<?php echo htmlspecialchars($article['titre_navigation']); ?>" data-date="<?php echo $article['date_creation']; ?>" data-category-id="<?php echo (int) ($article['category_id'] ?? 0); ?>">
                            <div class="article-list-content">
                                <h2>
                                    <a href="/<?php echo htmlspecialchars($article['category_slug'] ?? 'sans-categorie'); ?>/<?php echo date('Y/m/d', strtotime($article['date_creation'])); ?>/<?php echo htmlspecialchars($article['slug']); ?>_<?php echo $article['id']; ?>_<?php echo (int) ($article['category_id'] ?? 0); ?>.html">
                                        <?php echo htmlspecialchars($article['titre_navigation']); ?>
                                    </a>
                                </h2>
                                <div class="article-list-meta">
                                    <span class="category"><?php echo htmlspecialchars($article['category_name'] ?? 'Sans catégorie', ENT_QUOTES, 'UTF-8'); ?></span>
                                    <span class="date">
                                        <?php echo date('d F Y', strtotime($article['date_creation'])); ?>
                                    </span>
                                </div>
                                <p class="article-list-excerpt">
                                    <?php echo htmlspecialchars($article['meta_description']); ?>
                                </p>
                                <a href="/<?php echo htmlspecialchars($article['category_slug'] ?? 'sans-categorie'); ?>/<?php echo date('Y/m/d', strtotime($article['date_creation'])); ?>/<?php echo htmlspecialchars($article['slug']); ?>_<?php echo $article['id']; ?>_<?php echo (int) ($article['category_id'] ?? 0); ?>.html" class="read-more-link">
                                    Lire l'article complet →
                                </a>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
        </section>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h2>À propos</h2>
                    <p>Un site d'information dédié à la couverture de la guerre en Iran avec rigueur et transparence.</p>
                </div>
                <div class="footer-section">
                    <h2>Liens utiles</h2>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 Iran War - All rights reserved</p>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const categorySelect = document.getElementById('categorySelect');
            const container = document.getElementById('articlesContainer');
            const articles = Array.from(container.querySelectorAll('.article-list-item'));

            function filterArticles() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categorySelect.value;

                // Filter
                const filtered = articles.filter(article => {
                    // Search filter
                    const title = article.dataset.title.toLowerCase();
                    const excerpt = article.querySelector('.article-list-excerpt').textContent.toLowerCase();
                    const matchesSearch = title.includes(searchTerm) || excerpt.includes(searchTerm);
                    
                    // Category filter
                    const articleCategoryId = article.dataset.categoryId;
                    const matchesCategory = selectedCategory === '' || articleCategoryId === selectedCategory;
                    
                    return matchesSearch && matchesCategory;
                });

                // Remove all articles
                articles.forEach(article => {
                    if (container.contains(article)) {
                        container.removeChild(article);
                    }
                });

                // Add filtered articles (sorted by date, most recent first)
                if (filtered.length === 0) {
                    container.innerHTML = '<div class="no-results">Aucun article ne correspond à vos critères.</div>';
                } else {
                    filtered.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
                    filtered.forEach(article => container.appendChild(article));
                }
            }

            searchInput.addEventListener('input', filterArticles);
            categorySelect.addEventListener('change', filterArticles);
        });
    </script>
</body>
</html>
