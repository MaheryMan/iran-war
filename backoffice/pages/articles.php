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
    <title>Backoffice - Liste des articles</title>
</head>
<body>
    <h1>Liste des articles</h1>
    <p>
        <a href="/pages/create-article.php">Créer un article</a>
    </p>

    <?php if ($errorMessage !== ''): ?>
        <p style="color:#b00020;background:#ffe6e9;padding:10px;border:1px solid #ffb3bd;max-width:900px;">
            <?php echo $errorMessage; ?>
        </p>
    <?php endif; ?>

    <?php if ($successMessage !== ''): ?>
        <p style="color:#0b5d1e;background:#e7f8eb;padding:10px;border:1px solid #b4e3bf;max-width:900px;">
            <?php echo $successMessage; ?>
        </p>
    <?php endif; ?>

    <?php if (empty($articles)): ?>
        <p>Aucun article pour le moment.</p>
    <?php else: ?>
        <table border="1" cellpadding="8" cellspacing="0" style="border-collapse:collapse;max-width:1100px;width:100%;">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Titre</th>
                    <th>Slug</th>
                    <th>Date creation</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo (int) $article['id']; ?></td>
                        <td><?php echo htmlspecialchars($article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($article['slug'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars((string) $article['date_creation'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/pages/edit-article.php?id=<?php echo (int) $article['id']; ?>">Modifier</a>
                            |
                            <form
                                action="/traitements/traitement-delete-article.php"
                                method="post"
                                style="display:inline;"
                                onsubmit="return confirm('Supprimer cet article ? Cette action est irreversible.');"
                            >
                                <input type="hidden" name="id" value="<?php echo (int) $article['id']; ?>">
                                <button
                                    type="submit"
                                    style="background:none;border:0;color:#b00020;cursor:pointer;padding:0;text-decoration:underline;"
                                >
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
