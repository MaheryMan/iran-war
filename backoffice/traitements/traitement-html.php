<?php

$contenu = $_POST['content'] ?? '';
$titre = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

require_once __DIR__ . '/../includes/article_repository.php';

$description = $titre . ' - ' . $description;

$resultat = insererArticle($titre, $contenu, $description);

if ($resultat !== true) {
    $message = is_string($resultat) ? $resultat : "Une erreur est survenue lors de l'enregistrement.";
    header('Location: /articles?error=' . rawurlencode($message));
    exit;
}

header('Location: /articles?success=' . rawurlencode('Article cree avec succes.'));
exit;