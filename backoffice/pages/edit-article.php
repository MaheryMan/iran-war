<?php
session_start();
header('Content-Type: text/html; charset=utf-8');
require_once __DIR__ . '/../includes/article_repository.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: /connexion?error=' . rawurlencode('Veuillez vous connecter pour acceder au backoffice.'));
    exit;
}

$tinyMceApiKey = getenv('TINYMCE_API_KEY') ?: 'no-api-key';
$tinyMceApiKey = htmlspecialchars($tinyMceApiKey, ENT_QUOTES, 'UTF-8');

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

$contenuHtml = getContenuHtmlByArticleId($idArticle);
$categories = getCategories();
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
    <meta name="description" content="Modifier l'article: <?php echo htmlspecialchars($article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?>">
    <meta name="theme-color" content="#2563eb">
    <title>Modifier un article - Backoffice Iran War</title>
    <link rel="stylesheet" href="/assets/backoffice.css">
    <script src="https://cdn.tiny.cloud/1/<?php echo $tinyMceApiKey; ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <header>
        <nav>
            <h1>Backoffice Iran War</h1>
            <ul class="nav-links">
                <li><a href="/articles">Articles</a></li>
                <li><a href="http://localhost:8080" class="btn-frontoffice">Voir le site</a></li>
                <li><a href="/traitements/deconnexion.php" role="button" class="action-link">Déconnexion</a></li>
            </ul>
        </nav>
    </header>

    <main class="container">
        <div class="breadcrumbs mb-30">
            <a href="/articles">Articles</a>
            <span>/</span>
            <span>Modifier l'article #<?php echo (int) $article['id']; ?></span>
        </div>

        <h1>Modification de l'article</h1>
        <p class="text-muted">ID: <?php echo (int) $article['id']; ?> | Créé le: <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($article['date_creation'])), ENT_QUOTES, 'UTF-8'); ?></p>

        <?php if ($errorMessage !== ''): ?>
            <div class="alert alert-error" role="alert">
                <span>⚠️</span>
                <span><?php echo $errorMessage; ?></span>
            </div>
        <?php endif; ?>

        <?php if ($successMessage !== ''): ?>
            <div class="alert alert-success" role="alert">
                <span>✓</span>
                <span><?php echo $successMessage; ?></span>
            </div>
        <?php endif; ?>

        <form id="articleForm" action="/traitements/traitement-update-article.php" method="post">
            <input type="hidden" name="id" value="<?php echo (int) $article['id']; ?>">

            <div class="form-group">
                <label for="title">Titre de l'article *</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    required
                    aria-required="true"
                    value="<?php echo htmlspecialchars((string) $article['titre_navigation'], ENT_QUOTES, 'UTF-8'); ?>"
                    maxlength="255"
                >
                <small class="text-muted">Le titre s'affichera dans la navigation</small>
            </div>

            <div class="form-group">
                <label for="description">Description - Meta description *</label>
                <textarea 
                    id="description" 
                    name="description"
                    maxlength="160"
                    rows="2"
                    aria-describedby="description-help"
                ><?php echo htmlspecialchars($descriptionEditeur, ENT_QUOTES, 'UTF-8'); ?></textarea>
                <small class="text-muted">Utilisée pour le SEO et les résultats de recherche</small>
            </div>

            <div class="form-group">
                <label for="category">Catégorie</label>
                <select id="category" name="category">
                    <option value="">Aucune catégorie</option>
                    <?php foreach ($categories as $categorie): ?>
                        <option value="<?php echo (int) $categorie['id']; ?>" <?php echo ((int) ($article['category_id'] ?? 0) === (int) $categorie['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($categorie['libelle'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="content">Contenu de l'article *</label>
                <textarea 
                    id="content" 
                    name="content" 
                    required
                    aria-required="true"
                ><?php echo htmlspecialchars($contenuHtml, ENT_QUOTES, 'UTF-8'); ?></textarea>
            </div>

            <div class="flex gap-12">
                <button type="submit" class="btn btn-primary">Mettre à jour l'article</button>
                <a href="/articles" class="btn btn-secondary">Retour</a>
            </div>
        </form>
    </main>

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
                        alert('⚠️ Attention: Ajoutez un texte alternatif (alt) à chaque image avant d\'enregistrer.');
                        return;
                    }
                }
            });
        }
    </script>
</body>
</html>
