<?php
session_start();
require_once __DIR__ . '/../includes/article_repository.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Veuillez vous connecter pour accéder au backoffice.'));
    exit;
}

$tinyMceApiKey = getenv('TINYMCE_API_KEY') ?: 'no-api-key';
$tinyMceApiKey = htmlspecialchars($tinyMceApiKey, ENT_QUOTES, 'UTF-8');

$idArticle = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($idArticle <= 0) {
    header('Location: /pages/articles.php?error=' . rawurlencode('Article invalide.'));
    exit;
}

$article = getArticleById($idArticle);
if (!$article) {
    header('Location: /pages/articles.php?error=' . rawurlencode("L'article est introuvable."));
    exit;
}

$contenuHtml = getContenuHtmlByArticleId($idArticle);
$descriptionEditeur = (string) ($article['meta_description'] ?? '');
$prefixDescription = (string) ($article['titre_navigation'] ?? '') . ' - ';
if (strpos($descriptionEditeur, $prefixDescription) === 0) {
    $descriptionEditeur = substr($descriptionEditeur, strlen($prefixDescription));
}

$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulaire de modification d'article">
    <title>Modifier un article</title>
    <script src="https://cdn.tiny.cloud/1/<?php echo $tinyMceApiKey; ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <h1>Modification de l'article #<?php echo (int) $article['id']; ?></h1>

    <p>
        <a href="/pages/articles.php">Retour a la liste</a>
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

    <form id="articleForm" action="/traitements/traitement-update-article.php" method="post">
        <input type="hidden" name="id" value="<?php echo (int) $article['id']; ?>">

        <label for="title">Titre de l'article:</label><br>
        <input
            type="text"
            id="title"
            name="title"
            required
            value="<?php echo htmlspecialchars((string) $article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?>"
        ><br><br>

        <label for="description">Description de l'article:</label><br>
        <textarea id="description" name="description"><?php echo htmlspecialchars($descriptionEditeur, ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

        <label for="content">Contenu de l'article:</label><br>
        <textarea id="content" name="content" required><?php echo htmlspecialchars($contenuHtml, ENT_QUOTES, 'UTF-8'); ?></textarea><br><br>

        <input type="submit" value="Mettre a jour l'article">
    </form>

    <script>
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            image_title: true,
            automatic_uploads: true,
            images_upload_url: '/upload_handler.php',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount',
                'emoticons', 'codesample', 'quickbars'
            ],
            toolbar:
                'undo redo | blocks | ' +
                'bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'link image | code | help',
            branding: false,
            browser_spellcheck: true,
            contextmenu: 'link image table',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
            setup: function (editor) {
                editor.on('change input undo redo', function () {
                    editor.save();
                });
            }
        });

        const form = document.getElementById('articleForm');
        if (form) {
            form.addEventListener('submit', function (event) {
                const editor = tinymce.get('content');
                if (!editor) {
                    return;
                }

                const html = editor.getContent();
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const images = doc.querySelectorAll('img');

                for (const img of images) {
                    const alt = (img.getAttribute('alt') || '').trim();
                    if (!alt) {
                        event.preventDefault();
                        alert("Avertissement : ajoute un texte alternatif (alt) a chaque image avant d'enregistrer.");
                        return;
                    }
                }
            });
        }
    </script>
</body>
</html>
