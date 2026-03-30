<?php
session_start();
require_once __DIR__ . '/../includes/article_repository.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: /connexion?error=' . rawurlencode('Veuillez vous connecter pour acceder au backoffice.'));
    exit;
}

$idArticle = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($idArticle <= 0) {
    header('Location: /articles?error=' . rawurlencode('Article invalide.'));
    exit;
}

$article = getArticleById($idArticle);
if (!$article) {
    header('Location: /articles?error=' . rawurlencode("L'article est introuvable."));
    exit;
}

$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression d'article</title>
</head>
<body>
    <h1>Supprimer l'article #<?php echo (int) $article['id']; ?></h1>

    <p>
        <strong>Titre:</strong>
        <?php echo htmlspecialchars((string) $article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?>
    </p>

    <p>
        Cette action est irreversible. Veux-tu vraiment supprimer cet article ?
    </p>

    <?php if ($errorMessage !== ''): ?>
        <p style="color:#b00020;background:#ffe6e9;padding:10px;border:1px solid #ffb3bd;max-width:900px;">
            <?php echo $errorMessage; ?>
        </p>
    <?php endif; ?>

    <form action="/traitements/traitement-delete-article.php" method="post">
        <input type="hidden" name="id" value="<?php echo (int) $article['id']; ?>">
        <button type="submit" style="background:#b00020;color:#fff;border:0;padding:10px 14px;cursor:pointer;">
            Oui, supprimer
        </button>
            <a href="/articles" style="margin-left:12px;">Annuler</a>
    </form>
</body>
</html>
