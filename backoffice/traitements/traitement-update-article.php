<?php

require_once __DIR__ . '/../includes/article_repository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /pages/articles.php?error=' . rawurlencode('Methode non autorisee.'));
    exit;
}

$idArticle = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$titre = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$contenu = $_POST['content'] ?? '';

if ($idArticle <= 0) {
    header('Location: /pages/articles.php?error=' . rawurlencode('Article invalide.'));
    exit;
}

$description = $titre . ' - ' . $description;
$resultat = mettreAJourArticle($idArticle, $titre, $contenu, $description);

if ($resultat !== true) {
    $message = is_string($resultat) ? $resultat : 'Une erreur est survenue lors de la modification.';
    header('Location: /pages/edit-article.php?id=' . $idArticle . '&error=' . rawurlencode($message));
    exit;
}

header('Location: /pages/edit-article.php?id=' . $idArticle . '&success=' . rawurlencode('Article modifie avec succes.'));
exit;
