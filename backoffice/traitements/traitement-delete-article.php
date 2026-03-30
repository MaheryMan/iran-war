<?php

require_once __DIR__ . '/../includes/article_repository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /articles?error=' . rawurlencode('Methode non autorisee.'));
    exit;
}

$idArticle = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($idArticle <= 0) {
    header('Location: /articles?error=' . rawurlencode('Article invalide.'));
    exit;
}

$resultat = supprimerArticle($idArticle);
if ($resultat !== true) {
    $message = is_string($resultat) ? $resultat : 'Une erreur est survenue lors de la suppression.';
    header('Location: /articles?error=' . rawurlencode($message));
    exit;
}

header('Location: /articles?success=' . rawurlencode('Article supprime avec succes.'));
exit;
