<?php
require_once '../config.php';

// Récupérer tous les articles
$query = $pdo->prepare('
    SELECT id, titre_navigation, slug, meta_description, date_creation 
    FROM articles 
    ORDER BY date_creation DESC
');
$query->execute();
$articles = $query->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guerre en Iran - Articles et Actualités</title>
    <meta name="description" content="Découvrez nos articles de fond sur la guerre en Iran: analyses, reportages, chronologies et documents inédits.">
    <link rel="stylesheet" href="/assets/style.css">
    <link rel="stylesheet" href="/assets/articles-list.css">
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
                    <input type="text" id="searchInput" placeholder="Rechercher un article..." class="search-input">
                    <span class="search-icon">🔍</span>
                </div>
                <div class="sort-controls">
                    <select id="sortSelect" class="sort-select">
                        <option value="recent">Plus récents</option>
                        <option value="old">Plus anciens</option>
                        <option value="alphabetic">Alphabétique (A → Z)</option>
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
                        <article class="article-list-item" data-title="<?php echo htmlspecialchars($article['titre_navigation']); ?>" data-date="<?php echo $article['date_creation']; ?>">
                            <div class="article-list-content">
                                <h2>
                                    <a href="/pages/article.php?slug=<?php echo urlencode($article['slug']); ?>">
                                        <?php echo htmlspecialchars($article['titre_navigation']); ?>
                                    </a>
                                </h2>
                                <div class="article-list-meta">
                                    <span class="category">Actualités</span>
                                    <span class="date">
                                        <?php echo date('d F Y', strtotime($article['date_creation'])); ?>
                                    </span>
                                </div>
                                <p class="article-list-excerpt">
                                    <?php echo htmlspecialchars($article['meta_description']); ?>
                                </p>
                                <a href="/pages/article.php?slug=<?php echo urlencode($article['slug']); ?>" class="read-more-link">
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
                    <h4>À propos</h4>
                    <p>Un site d'information dédié à la couverture de la guerre en Iran avec rigueur et transparence.</p>
                </div>
                <div class="footer-section">
                    <h4>Liens utiles</h4>
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
            const sortSelect = document.getElementById('sortSelect');
            const container = document.getElementById('articlesContainer');
            const articles = Array.from(container.querySelectorAll('.article-list-item'));

            function filterAndSort() {
                const searchTerm = searchInput.value.toLowerCase();
                const sortType = sortSelect.value;

                // Filter
                const filtered = articles.filter(article => {
                    const title = article.dataset.title.toLowerCase();
                    const excerpt = article.querySelector('.article-list-excerpt').textContent.toLowerCase();
                    return title.includes(searchTerm) || excerpt.includes(searchTerm);
                });

                // Sort
                filtered.sort((a, b) => {
                    switch(sortType) {
                        case 'recent':
                            return new Date(b.dataset.date) - new Date(a.dataset.date);
                        case 'old':
                            return new Date(a.dataset.date) - new Date(b.dataset.date);
                        case 'alphabetic':
                            return a.dataset.title.localeCompare(b.dataset.title);
                        default:
                            return 0;
                    }
                });

                // Remove all articles
                articles.forEach(article => container.removeChild(article));

                // Add sorted articles
                if (filtered.length === 0) {
                    container.innerHTML = '<div class="no-results">Aucun article ne correspond à votre recherche.</div>';
                } else {
                    filtered.forEach(article => container.appendChild(article));
                }
            }

            searchInput.addEventListener('input', filterAndSort);
            sortSelect.addEventListener('change', filterAndSort);
        });
    </script>
</body>
</html>
