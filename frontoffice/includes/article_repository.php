<?php

require_once __DIR__ . '/../config.php';

/**
 * Récupère tous les articles triés par date décroissante avec leurs catégories
 */
function getAllArticles()
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT a.id, a.titre_navigation, a.slug, a.meta_description, a.date_creation, a.category_id, c.libelle as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        ORDER BY a.date_creation DESC
    ');
    $query->execute();
    return $query->fetchAll();
}

/**
 * Récupère toutes les catégories
 */
function getCategories()
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT id, libelle, slug 
        FROM categories 
        ORDER BY libelle ASC
    ');
    $query->execute();
    return $query->fetchAll();
}

/**
 * Récupère les articles d'une catégorie spécifique
 */
function getArticlesByCategory($categoryId)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT a.id, a.titre_navigation, a.slug, a.meta_description, a.date_creation, a.category_id, c.libelle as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.category_id = ?
        ORDER BY a.date_creation DESC
    ');
    $query->execute([$categoryId]);
    return $query->fetchAll();
}

/**
 * Récupère un article par son ID avec sa catégorie
 */
function getArticleById($id)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT a.id, a.titre_navigation, a.slug, a.meta_description, a.date_creation, a.category_id, c.libelle as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.id = ?
        LIMIT 1
    ');
    $query->execute([$id]);
    return $query->fetch();
}

/**
 * Récupère un article par son slug avec sa catégorie
 */
function getArticleBySlug($slug)
{
    global $pdo;
    
    $query = $pdo->prepare('
        SELECT a.id, a.titre_navigation, a.slug, a.meta_description, a.date_creation, a.category_id, c.libelle as category_name, c.slug as category_slug
        FROM articles a
        LEFT JOIN categories c ON a.category_id = c.id
        WHERE a.slug = ?
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
