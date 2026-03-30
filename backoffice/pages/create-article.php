<?php
session_start();

$tinyMceApiKey = getenv('TINYMCE_API_KEY') ?: 'no-api-key';
$tinyMceApiKey = htmlspecialchars($tinyMceApiKey, ENT_QUOTES, 'UTF-8');
$errorMessage = isset($_GET['error']) ? htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') : '';
$successMessage = isset($_GET['success']) ? htmlspecialchars($_GET['success'], ENT_QUOTES, 'UTF-8') : '';

if (!isset($_SESSION['user_id'])) {
    header('Location: /pages/connexion.php?error=' . rawurlencode('Veuillez vous connecter pour accéder au backoffice.'));
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Formulaire de création d'article">
    <title>Creation d'un article</title>
    <script src="https://cdn.tiny.cloud/1/<?php echo $tinyMceApiKey; ?>/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
</head>
<body>
    <h1>Création d'un article</h1>
  <p>
    <a href="/pages/articles.php">Voir la liste des articles</a>
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

  <form id="articleForm" action="/traitements/traitement-html.php" method="post">
        <label for="title">Titre de l'article:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="description">Description de l'article:</label><br>
        <textarea id="description" name="description"></textarea><br><br>


        <label for="content">Contenu de l'article:</label><br>
        <textarea id="content" name="content" required></textarea><br><br>

        <input type="submit" value="Créer l'article">
    </form>

    <script>
  tinymce.init({
    selector: '#content',
    height: 500,
    menubar: true,
    image_title: true,
    automatic_uploads: true,
    // Use an absolute path because this page lives under /pages/
    images_upload_url: '/upload_handler.php',

    // Plugins principaux (ajuste selon ton besoin)
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

    // Configuration utile
    branding: false,
    browser_spellcheck: true,
    contextmenu: 'link image table',
    content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:14px }',

    // Important: synchronise automatiquement le textarea
    setup: function (editor) {
      editor.on('change input undo redo', function () {
        editor.save(); // met a jour la valeur du textarea

        // Optionnel: copie dans le champ cache
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
          alert("Avertissement : ajoute un texte alternatif (alt) a chaque image avant d'enregistrer.");
          return;
        }
      }
    });
  }
</script>
</body>     
</html>