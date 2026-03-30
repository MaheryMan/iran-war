<?php
$contenu = $_POST['content'] ?? '';
$titre = $_POST['title'] ?? '';
$description = $_POST['description'] ?? '';

include '../includes/connexDB.php';

$description = $titre.' - '.$description;

$resultat = insererArticle($titre, $contenu, $description);

if ($resultat !== true) {
    $message = is_string($resultat) ? $resultat : "Une erreur est survenue lors de l'enregistrement.";
    header('Location: /pages/index.php?error=' . rawurlencode($message));
    exit;
}

header('Location: /pages/index.php?success=' . rawurlencode("Article cree avec succes."));
exit;

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

function insererContenuByidArticle($idArticle, $contenu)
{
    global $pdo;
    $dom = new DOMDocument();
    //atao mazaka UTF8
    @$dom->loadHTML(mb_convert_encoding($contenu, 'HTML-ENTITIES', 'UTF-8'));
    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) {
        return "Contenu HTML invalide.";
    }
    $blocs = $body->childNodes;

    $ordre = 1;
    foreach ($blocs as $bloc) {
        if (!($bloc instanceof DOMElement)) {
            continue;
        }

        $tag = $bloc->tagName;
        if ($tag === 'img') {
            $alt = $bloc->getAttribute('alt');

            if (empty($alt)) {
                return "L'image doit avoir un texte alternatif (attribut alt).";
            }
        }

        $html = $dom->saveHTML($bloc);

        $sql = "INSERT INTO contenus (article_id, ordre, type_balise, valeur) VALUES (:article_id, :ordre, :type_balise, :valeur)";
        if ($tag === 'img') {
            $sql = "INSERT INTO contenus (article_id, ordre, type_balise, valeur, alt_text) VALUES (:article_id, :ordre, :type_balise, :valeur, :alt_text)";
        }

        $stmt = $pdo->prepare($sql);
        if ($tag === 'img') {
            $stmt->execute([
                'article_id' => $idArticle,
                'ordre' => $ordre,
                'type_balise' => $tag,
                'valeur' => $html,
                'alt_text' => $alt
            ]);
        } else {
            $stmt->execute([
                'article_id' => $idArticle,
                'ordre' => $ordre,
                'type_balise' => $tag,
                'valeur' => $html
            ]);
        }
        $ordre++;
    }
}

function insererArticle($titre, $contenus, $description)
{
    global $pdo;

    if (trim($titre) === '' || trim($contenus) === '' || trim($description) === '') {
        return "Le titre, la description et le contenu sont obligatoires.";
    }

    $validationAlt = verifierImagesAvecAlt($contenus);
    if ($validationAlt !== true) {
        return $validationAlt;
    }

    try {
        $pdo->beginTransaction();

        $slug = genererSlug($titre);
        $sql = "INSERT INTO articles (titre_navigation, slug, meta_description) VALUES (:titre, :slug, :meta_description)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['titre' => $titre, 'slug' => $slug, 'meta_description' => $description]);
        $idArticle = $pdo->lastInsertId();

        $resultatContenu = insererContenuByidArticle($idArticle, $contenus);
        if ($resultatContenu !== null && $resultatContenu !== true) {
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
function getArticleById($id)
{
    global $pdo;

    $sql = "SELECT * FROM articles WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllArticles()
{
    global $pdo;

    $sql = "SELECT * FROM articles ORDER BY date_creation DESC";
    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    return trim($slug, '-');
}