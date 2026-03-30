<?php
session_start();
require_once __DIR__ . '/../includes/article_repository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Veuillez vous connecter pour accéder au backoffice.'));
    exit;
}

$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';
$articles = getAllArticles();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Gestion des articles du backoffice Iran War">
    <meta name="theme-color" content="#2563eb">
    <title>Articles - Backoffice Iran War</title>
    <link rel="stylesheet" href="/assets/backoffice.css">
</head>
<body>
    <header>
        <nav>
            <h1>Backoffice Iran War</h1>
            <ul class="nav-links">
                <li><a href="/pages/articles.php">Articles</a></li>
                <li><a href="/traitements/deconnexion.php" role="button" class="action-link">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="flex justify-between align-center mb-30">
            <h1>Liste des articles</h1>
            <a href="/pages/create-article.php" class="btn btn-primary">+ Créer un article</a>
        </div>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert alert-error" role="alert">
                <span>⚠️</span>
                <span><?php echo $errorMessage; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="alert alert-success" role="alert">
                <span>✓</span>
                <span><?php echo $successMessage; ?></span>
            </div>
        <?php endif; ?>

        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <h2>Aucun article trouvé</h2>
                <p>Commencez par créer votre premier article.</p>
                <a href="/pages/create-article.php" class="btn btn-primary">Créer un article</a>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table role="table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Titre</th>
                            <th scope="col">Slug</th>
                            <th scope="col">Date de création</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo (int) $article['id']; ?></td>
                                <td><?php echo htmlspecialchars($article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><code><?php echo htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?></code></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($article['date_creation'])), ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="/pages/edit-article.php?id=<?php echo (int) $article['id']; ?>" class="action-link">Modifier</a>
                                        <form
                                            action="/traitements/traitement-delete-article.php"
                                            method="post"
                                            style="display:inline;margin:0;"
                                            onsubmit="return confirm('Supprimer cet article ? Cette action est irréversible.');"
                                        >
                                            <input type="hidden" name="id" value="<?php echo (int) $article['id']; ?>">
                                            <button
                                                type="submit"
                                                class="btn-delete"
                                                aria-label="Supprimer l'article <?php echo htmlspecialchars($article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?>"
                                            >
                                                Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
