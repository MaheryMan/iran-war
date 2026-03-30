<?php

require_once __DIR__ . '/connexDB.php';

function verifierImagesAvecAlt($contenu)
{
    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($contenu, 'HTML-ENTITIES', 'UTF-8'));

    $images = $dom->getElementsByTagName('img');
    foreach ($images as $image) {
        if (!($image instanceof DOMElement)) {
            continue;
        }

        $alt = trim($image->getAttribute('alt'));
        if ($alt === '') {
            return "Avertissement : chaque image doit avoir un texte alternatif (attribut alt).";
        }
    }

    return true;
}

function genererSlug($titre)
{
    $slug = mb_strtolower($titre, 'UTF-8');

    $slug = str_replace(
        ['à', 'â', 'ä', 'é', 'è', 'ê', 'ë', 'î', 'ï', 'ô', 'ö', 'û', 'ü', 'ç'],
        ['a', 'a', 'a', 'e', 'e', 'e', 'e', 'i', 'i', 'o', 'o', 'u', 'u', 'c'],
        $slug
    );

    $slug = preg_replace('/[^a-z0-9]+/i', '-', $slug);

    return trim((string) $slug, '-');
}

function genererSlugUnique($titre, $articleIdExclu = null)
{
    global $pdo;

    $baseSlug = genererSlug($titre);
    if ($baseSlug === '') {
        $baseSlug = 'article';
    }

    $slug = $baseSlug;
    $index = 2;

    while (true) {
        if ($articleIdExclu === null) {
            $sql = 'SELECT id FROM articles WHERE slug = :slug LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['slug' => $slug]);
        } else {
            $sql = 'SELECT id FROM articles WHERE slug = :slug AND id != :id LIMIT 1';
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['slug' => $slug, 'id' => $articleIdExclu]);
        }

        if (!$stmt->fetch()) {
            return $slug;
        }

        $slug = $baseSlug . '-' . $index;
        $index++;
    }
}

function insererContenuByIdArticle($idArticle, $contenu)
{
    global $pdo;

    $dom = new DOMDocument();
    @$dom->loadHTML(mb_convert_encoding($contenu, 'HTML-ENTITIES', 'UTF-8'));
    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) {
        return 'Contenu HTML invalide.';
    }

    $blocs = $body->childNodes;
    $ordre = 1;

    foreach ($blocs as $bloc) {
        if (!($bloc instanceof DOMElement)) {
            continue;
        }

        $tag = $bloc->tagName;
        $alt = null;

        if ($tag === 'img') {
            $alt = trim($bloc->getAttribute('alt'));
            if ($alt === '') {
                return "L'image doit avoir un texte alternatif (attribut alt).";
            }
        }

        $html = $dom->saveHTML($bloc);

        $sql = 'INSERT INTO contenus (article_id, ordre, type_balise, valeur) VALUES (:article_id, :ordre, :type_balise, :valeur)';
        $params = [
            'article_id' => $idArticle,
            'ordre' => $ordre,
            'type_balise' => $tag,
            'valeur' => $html,
        ];

        if ($tag === 'img') {
            $sql = 'INSERT INTO contenus (article_id, ordre, type_balise, valeur, alt_text) VALUES (:article_id, :ordre, :type_balise, :valeur, :alt_text)';
            $params['alt_text'] = $alt;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $ordre++;
    }

    return true;
}

function insererArticle($titre, $contenu, $description)
{
    global $pdo;

    if (trim($titre) === '' || trim($contenu) === '' || trim($description) === '') {
        return 'Le titre, la description et le contenu sont obligatoires.';
    }

    $validationAlt = verifierImagesAvecAlt($contenu);
    if ($validationAlt !== true) {
        return $validationAlt;
    }

    try {
        $pdo->beginTransaction();

        $slug = genererSlugUnique($titre);
        $sql = 'INSERT INTO articles (titre_navigation, slug, meta_description) VALUES (:titre, :slug, :meta_description)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'slug' => $slug,
            'meta_description' => $description,
        ]);

        $idArticle = (int) $pdo->lastInsertId();
        $resultatContenu = insererContenuByIdArticle($idArticle, $contenu);
        if ($resultatContenu !== true) {
            $pdo->rollBack();
            return $resultatContenu;
        }

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return "Une erreur est survenue lors de l'enregistrement.";
    }
}

function mettreAJourArticle($idArticle, $titre, $contenu, $description)
{
    global $pdo;

    if ($idArticle <= 0) {
        return 'Article invalide.';
    }

    if (trim($titre) === '' || trim($contenu) === '' || trim($description) === '') {
        return 'Le titre, la description et le contenu sont obligatoires.';
    }

    $validationAlt = verifierImagesAvecAlt($contenu);
    if ($validationAlt !== true) {
        return $validationAlt;
    }

    try {
        $pdo->beginTransaction();

        $sql = 'SELECT id FROM articles WHERE id = :id LIMIT 1';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idArticle]);
        if (!$stmt->fetch()) {
            $pdo->rollBack();
            return "L'article est introuvable.";
        }

        $slug = genererSlugUnique($titre, $idArticle);
        $sql = 'UPDATE articles SET titre_navigation = :titre, slug = :slug, meta_description = :meta_description WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'titre' => $titre,
            'slug' => $slug,
            'meta_description' => $description,
            'id' => $idArticle,
        ]);

        $sql = 'DELETE FROM contenus WHERE article_id = :article_id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['article_id' => $idArticle]);

        $resultatContenu = insererContenuByIdArticle($idArticle, $contenu);
        if ($resultatContenu !== true) {
            $pdo->rollBack();
            return $resultatContenu;
        }

        $pdo->commit();
        return true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return "Une erreur est survenue lors de la modification.";
    }
}

function supprimerArticle($idArticle)
{
    global $pdo;

    if ($idArticle <= 0) {
        return 'Article invalide.';
    }

    try {
        $sql = 'DELETE FROM articles WHERE id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $idArticle]);

        if ($stmt->rowCount() === 0) {
            return "L'article est introuvable.";
        }

        return true;
    } catch (Throwable $e) {
        return "Une erreur est survenue lors de la suppression.";
    }
}

function getArticleById($id)
{
    global $pdo;

    $sql = 'SELECT * FROM articles WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllArticles()
{
    global $pdo;

    $sql = 'SELECT * FROM articles ORDER BY date_creation DESC';
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getContenusByArticleId($idArticle)
{
    global $pdo;

    $sql = 'SELECT type_balise, valeur, alt_text, ordre FROM contenus WHERE article_id = :id ORDER BY ordre ASC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $idArticle]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getContenuHtmlByArticleId($idArticle)
{
    $contenus = getContenusByArticleId($idArticle);
    $html = '';

    foreach ($contenus as $contenu) {
        $html .= $contenu['valeur'];
    }

    return $html;
}