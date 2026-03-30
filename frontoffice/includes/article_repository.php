<?php

require_once __DIR__ . '/../config.php';

/**
 * Récupère tous les articles triés par date décroissante
 */
function getAllArticles()
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT id, titre_navigation, slug, meta_description, date_creation 
        FROM articles 
        ORDER BY date_creation DESC
    ');
    $query->execute();
    return $query->fetchAll();
}

/**
 * Récupère un article par son ID
 */
function getArticleById($id)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT id, titre_navigation, slug, meta_description, date_creation 
        FROM articles 
        WHERE id = ?
        LIMIT 1
    ');
    $query->execute([$id]);
    return $query->fetch();
}

/**
 * Récupère un article par son slug
 */
function getArticleBySlug($slug)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT id, titre_navigation, slug, meta_description, date_creation 
        FROM articles 
        WHERE slug = ?
        LIMIT 1
    ');
    $query->execute([$slug]);
    return $query->fetch();
}

/**
 * Récupère le contenu d'un article par son ID
 */
function getArticleContentById($id)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT type_balise, valeur, alt_text, ordre
        FROM contenus 
        WHERE article_id = ?
        ORDER BY ordre ASC
    ');
    $query->execute([$id]);
    return $query->fetchAll();
}

?>
