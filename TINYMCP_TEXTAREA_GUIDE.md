# Guide TinyMCP (TinyMCE) pour un `textarea` avec retour HTML

Ce document montre comment :

1. Transformer un `textarea` en éditeur riche TinyMCE.
2. Activer un maximum de fonctionnalites via les plugins.
3. Recuperer le HTML produit par l'editeur.
4. Envoyer ce HTML au serveur (PHP).

## 1) Exemple HTML minimal

```html
<form id="articleForm" method="POST" action="save.php">
  <label for="content">Contenu</label>
  <textarea id="content" name="content"></textarea>

  <!-- Optionnel: champ cache pour forcer la valeur HTML -->
  <input type="hidden" id="content_html" name="content_html" />

  <button type="submit">Enregistrer</button>
</form>

<!-- TinyMCE (Tiny Cloud) -->
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
```

## 2) Initialisation complete de TinyMCE

```html
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
      'undo redo | blocks fontfamily fontsize | ' +
      'bold italic underline strikethrough forecolor backcolor | ' +
      'alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | removeformat | ' +
      'link image media table | code preview fullscreen | help',

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
```

## 3) Recuperer le HTML cote client

Tu peux recuperer le HTML a tout moment avec :

```js
const html = tinymce.get('content').getContent();
console.log(html);
```

## 4) Recuperer le HTML cote serveur (PHP)

Dans `save.php` :

```php
<?php
// Si editor.save() est utilise, $_POST['content'] contient deja le HTML
$content = $_POST['content'] ?? '';

// Optionnel: si tu utilises le champ cache
$contentHtml = $_POST['content_html'] ?? $content;

// Exemple: stocker en base
// $stmt = $pdo->prepare("INSERT INTO articles (content_html) VALUES (:html)");
// $stmt->execute(['html' => $contentHtml]);

echo "HTML recu:\n";
echo $contentHtml;
```

## 5) Liste rapide des fonctionnalites actives

- Mise en forme: gras, italique, soulignement, tailles, couleurs.
- Titres et paragraphes: blocs (`blocks`).
- Listes: puces et numerotation.
- Liens et medias: lien, image, media.
- Tableaux: creation et edition.
- Outils dev: affichage code HTML (`code`), preview.
- Productivite: recherche/remplacement, wordcount, quickbars.
- Confort: plein ecran, correcteur orthographique navigateur.

## 6) Notes importantes

- Si tu utilises Tiny Cloud en production, remplace `no-api-key` par ta vraie API key.
- Certains plugins peuvent dependre de ton plan Tiny Cloud.
- Le HTML renvoye doit etre filtre/echappe selon ton usage pour la securite (XSS).

---

Si tu veux, je peux aussi te fournir la version integree directement dans ton `backoffice/index.php` avec un bouton de test qui affiche le HTML genere en live.
