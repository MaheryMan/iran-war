# Guide URL Rewriting - Apache + PHP

## Configuration rapide du `.htaccess`

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    
    # Ne pas réécrire les fichiers et dossiers existants
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    
    # Réécrire tout vers index.php
    RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
</IfModule>
```

## Traitement en PHP (`index.php`)

```php
<?php
// Récupérer et nettoyer l'URL
$url = isset($_GET['url']) ? trim($_GET['url'], '/') : '';
$parts = explode('/', $url);

// Router les requêtes
switch ($parts[0]) {
    case 'articles':
        include 'pages/articles.php';
        break;
    case 'article':
        if (isset($parts[1])) {
            $_GET['id'] = $parts[1];
        }
        include 'pages/article.php';
        break;
    default:
        include 'pages/articles.php';
}
?>
```

## Exemples de routes

| URL lisible | Récupération en PHP |
|---|---|
| `/articles` | `$parts[0] === 'articles'` |
| `/article/123` | `$parts[0] === 'article'`, `$parts[1] === 123` |
| `/articles/tech` | `$parts[0] === 'articles'`, `$parts[1] === 'tech'` |
| `/articles/tech/123` | `$parts[0] === 'articles'`, `$parts[1] === 'tech'`, `$parts[2] === 123` |

## Générer des liens réécrits

```php
<?php
// Au lieu de : <a href="article.php?id=123">
// Utiliser : <a href="/article/123">
?>
```

## Points importants

- **RewriteEngine On** : Active le module mod_rewrite
- **RewriteBase /** : Point de départ de la réécriture
- **!-f** et **!-d** : N'applique pas la réécriture aux fichiers et dossiers existants
- **[L,QSA]** : L = Last (arrête le traitement), QSA = Query String Append

## Vérification

Assurez-vous que :
1. ✓ Le module `mod_rewrite` est activé sur Apache
2. ✓ Le fichier `.htaccess` est dans le bon répertoire (racine du site)
3. ✓ AllowOverride est activé dans la config Apache
