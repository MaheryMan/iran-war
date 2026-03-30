<?php
session_start();

$tinyMceApiKey = getenv('TINYMCE_API_KEY') ?: 'no-api-key';
$tinyMceApiKey = htmlspecialchars($tinyMceApiKey, ENT_QUOTES, 'UTF-8');
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';

if (!isset($_SESSION['user_id'])) {
    header('Location: /connexion?error=' . rawurlencode('Veuillez vous connecter pour acceder au backoffice.'));
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Créer un nouvel article pour le site Iran War">
    <meta name="theme-color" content="#2563eb">
    <title>Créer un article - Backoffice Iran War</title>
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
            <span>Créer un article</span>
        </div>

        <h1>Création d'un nouvel article</h1>

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

        <form id="articleForm" action="/traitements/traitement-html.php" method="post">
            <div class="form-group">
                <label for="title">Titre de l'article *</label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    required
                    aria-required="true"
                    placeholder="Ex: Les tensions au Moyen-Orient..."
                    maxlength="255"
                >
                <small class="text-muted">Le titre s'affichera dans la navigation</small>
            </div>

            <div class="form-group">
                <label for="description">Description - Meta description *</label>
                <textarea 
                    id="description" 
                    name="description"
                    placeholder="Écrivez une description courte (150-160 caractères)"
                    maxlength="160"
                    rows="2"
                    aria-describedby="description-help"
                ></textarea>
                <small class="text-muted">Utilisée pour le SEO et les résultats de recherche</small>
            </div>

            <div class="form-group">
                <label for="content">Contenu de l'article *</label>
                <textarea 
                    id="content" 
                    name="content" 
                    required
                    aria-required="true"
                    placeholder="Rédigez le contenu de votre article..."
                ></textarea>
            </div>

            <div class="flex gap-12">
                <button type="submit" class="btn btn-primary">Créer l'article</button>
                <a href="/articles" class="btn btn-secondary">Annuler</a>
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
      'link image| code | help',
    branding: false,
    browser_spellcheck: true,
    contextmenu: 'link image table',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',
    setup: function (editor) {
      editor.on('change input undo redo', function () {
        editor.save();
        const html = editor.getContent();
        const hidden = document.getElementById('content_html');
        if (hidden) hidden.value = html;
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