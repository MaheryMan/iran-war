<?php

require_once __DIR__ . '/../includes/article_repository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /articles?error=' . rawurlencode('Methode non autorisee.'));
    exit;
}

$idArticle = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$titre = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';
$contenu = $_POST['content'] ?? '';

if ($idArticle <= 0) {
    header('Location: /articles?error=' . rawurlencode('Article invalide.'));
    exit;
}

$description = $titre . ' - ' . $description;
$resultat = mettreAJourArticle($idArticle, $titre, $contenu, $description);

if ($resultat !== true) {
    $message = is_string($resultat) ? $resultat : 'Une erreur est survenue lors de la modification.';
    header('Location: /articles/' . $idArticle . '/editer?error=' . rawurlencode($message));
    exit;
}

header('Location: /articles/' . $idArticle . '/editer?success=' . rawurlencode('Article modifie avec succes.'));
exit;
