
<?php
$tinyMceApiKey = getenv('TINYMCE_API_KEY') ?: 'no-api-key';
$tinyMceApiKey = htmlspecialchars($tinyMceApiKey, ENT_QUOTES, 'UTF-8');
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
    <form action="/traitements/traitement-html.php" method="post">
        <label for="title">Titre de l'article:</label><br>
        <input type="text" id="title" name="title" required><br><br>

        <label for="content">Contenu de l'article:</label><br>
        <textarea id="content" name="content" required></textarea><br><br>

        <input type="submit" value="Créer l'article">
    </form>

    <script>
  tinymce.init({
    selector: '#content',
    height: 500,
    menubar: true,

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
</script>
</body>     
</html>