<?php
/**
 * Header Navigation Réutilisable
 * Utilisé sur chaque page du site
 */
?>
<header class="header">
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><a href="/">Iran War</a></h1>
                <p class="tagline">Actualités et Analyse</p>
            </div>
            <ul class="nav-links">
                <li><a href="/" <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php' && dirname($_SERVER['PHP_SELF']) === '/') ? 'class="active"' : ''; ?>>Accueil</a></li>
                <li><a href="/pages/articles.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'articles.php') ? 'class="active"' : ''; ?>>Articles</a></li>
            </ul>
        </div>
    </nav>
</header>
