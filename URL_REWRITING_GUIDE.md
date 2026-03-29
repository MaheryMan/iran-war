# Guide URL Rewriting - Apache + PHP

## Qu'est-ce que l'URL Rewriting?

L'**URL Rewriting** permet de transformer une URL demandée par le client en une URL interne différente, sans que le client le sache.

### Exemple simple

```
URL du navigateur (ce qu'on voit) : /2026/03/29/mon-article_2.html
          ↓ (réécriture cachée)
URL interne (ce que PHP reçoit) : pages/article.php?id=2&slug=mon-article&date=2026-03-29
```

Le navigateur continue à afficher l'URL originale. Le serveur traite silencieusement l'autre.

---

## Pourquoi faire du URL Rewriting?

| Avantage | Exemple |
|----------|---------|
| **SEO** | `/2026/03/29/guerre-iran_2.html` est mieux que `article.php?id=2` |
| **Lisibilité** | Les URLs sont compréhensibles par les humains |
| **Flexibilité** | Changer l'architecture interne sans casser les anciens liens |
| **Sécurité** | Masquer la technologie utilisée (pas de `.php` visible) |
| **Maintenance** | Déplacer les fichiers PHP sans que les URLs publiques changent |

---

## Anatomie d'une règle de réécriture

### Structure générale dans `.htaccess`

```apache
RewriteRule ^MOTIF_ENTREE$ CIBLE_INTERNE [FLAGS]
```

### Cas d'étude: nos articles

```apache
RewriteRule ^([0-9]{4})/([0-9]{2})/([0-9]{2})/([_a-z0-9-]+)_([0-9]+)\.html$ pages/article.php?id=$5&slug=$4&date=$1-$2-$3 [L,QSA]
```

---

## Décortication de la règle

### 1. Le motif d'entrée (Pattern)

```regex
^([0-9]{4})/([0-9]{2})/([0-9]{2})/([_a-z0-9-]+)_([0-9]+)\.html$
```

Ceci définit quelles URLs doivent être réécrites. Chaque `()` crée un **groupe de capture**.

#### Symboles regex importants

| Symbole | Signification | Exemple |
|---------|---------------|---------|
| `^` | Début de la chaîne | `^/article` = commence par `/article` |
| `$` | Fin de la chaîne | `\.html$` = finit par `.html` |
| `[0-9]` | Un chiffre (0 à 9) | `[0-9]{4}` = 4 chiffres |
| `[a-z]` | Une lettre minuscule | `[a-z]+` = une ou plus |
| `-` | Un tiret littéral | `article-titre` = avec tiret dedans |
| `+` | Un ou plusieurs | `[0-9]+` = au moins 1 chiffre |
| `{n}` | Exactement n fois | `[0-9]{4}` = exactement 4 chiffres |
| `\.` | Un point littéral (échappé) | `\.html` = fichier `.html` |
| `()` | Groupe de capture | `([0-9]+)` = capturer les chiffres dans $1, $2, etc. |
| `\|` | OU logique | `jpg\|png` = `.jpg` ou `.png` |

#### Les 5 groupes dans notre cas

| # | Pattern | Capture | Ce qu'il match | Exemple |
|---|---------|---------|---|---------|
| 1 | `([0-9]{4})` | `$1` | Année (4 chiffres) | `2026` |
| 2 | `([0-9]{2})` | `$2` | Mois (2 chiffres) | `03` |
| 3 | `([0-9]{2})` | `$3` | Jour (2 chiffres) | `29` |
| 4 | `([_a-z0-9-]+)` | `$4` | Slug (lettres, chiffres, tirets) | `guerre-en-iran` |
| 5 | `([0-9]+)` | `$5` | ID (chiffres) | `2` |

### 2. La cible de réécriture (Substitution)

```apache
pages/article.php?id=$5&slug=$4&date=$1-$2-$3
```

Les variables `$1` à `$5` sont remplacées par les groupes capturés:

- `$5` = ID de l'article (`2`)
- `$4` = Slug (`guerre-en-iran`)
- `$1-$2-$3` = Date au format ISO (`2026-03-29`)

### 3. Les flags (Indicateurs)

```apache
[L,QSA]
```

| Flag | Signification | Effet |
|------|---------------|-------|
| `L` | Last | Arrête la réécriture (ne traite pas d'autres règles après) |
| `QSA` | Query String Append | Garde les paramètres GET existants |
| `R` | Redirect | Redirige le navigateur (visible dans l'URL) |
| `R=301` | Moved Permanently | Redirection permanente (bon pour SEO) |
| `NC` | No Case | Insensible à la casse (accepte majuscules/minuscules) |

---

## Conditions supplémentaires (optionnel)

Avant une règle, on peut ajouter des **conditions** pour affiner:

```apache
# N'appliquer la réécriture QUE pour les fichiers existants
RewriteCond %{REQUEST_FILENAME} -f

# N'appliquer QUE si c'est un dossier
RewriteCond %{REQUEST_FILENAME} -d

# N'appliquer QUE si le fichier n'existe pas
RewriteCond %{REQUEST_FILENAME} !-f

# N'appliquer QUE si le dossier n'existe pas
RewriteCond %{REQUEST_FILENAME} !-d
```

### Exemple: Ne pas réécrire les fichiers statiques

```apache
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]
```

Ceci signifie: "Si c'est un fichier OU un dossier réel, ne rien réécrire".

---

## Flux complet: de la requête au résultat

### Étape 1: L'utilisateur tape l'URL

```
https://localhost:8080/2026/03/29/guerre-en-iran_2.html
```

### Étape 2: Apache lit `.htaccess`

```apache
RewriteEngine On   # ← Activer le moteur
RewriteCond %{REQUEST_FILENAME} !-f  # ← N'est pas un fichier réel
RewriteCond %{REQUEST_FILENAME} !-d  # ← N'est pas un dossier réel
RewriteRule ^([0-9]{4})/([0-9]{2})/([0-9]{2})/([_a-z0-9-]+)_([0-9]+)\.html$ pages/article.php?id=$5&slug=$4&date=$1-$2-$3 [L,QSA]
```

### Étape 3: Apache applique la réécriture

```
URL entrante : /2026/03/29/guerre-en-iran_2.html
                    ↓
Groupes capturés :
  $1 = 2026
  $2 = 03
  $3 = 29
  $4 = guerre-en-iran
  $5 = 2
                    ↓
URL interne : pages/article.php?id=2&slug=guerre-en-iran&date=2026-03-29
```

### Étape 4: PHP reçoit les paramètres

```php
$_GET['id']   = 2
$_GET['slug'] = 'guerre-en-iran'
$_GET['date'] = '2026-03-29'
```

### Étape 5: Traitement dans le code PHP

```php
// Chercher l'article par ID
$query = $pdo->prepare('SELECT * FROM articles WHERE id = ?');
$query->execute([$_GET['id']]);
$article = $query->fetch();

// Afficher le contenu
echo "<h1>" . $article['titre'] . "</h1>";
```

---

## Exemples pratiques

### Exemple 1: Simple article avec ID

**Réécriture:**
```apache
RewriteRule ^article/([0-9]+)$ article.php?id=$1
```

**Fonctionnement:**
```
/article/42 → article.php?id=42
```

### Exemple 2: Catégorie + Page

**Réécriture:**
```apache
RewriteRule ^([a-z]+)/([0-9]+)$ category.php?cat=$1&page=$2
```

**Fonctionnement:**
```
/tech/5 → category.php?cat=tech&page=5
```

### Exemple 3: Front Controller (tout vers index.php)

**Réécriture:**
```apache
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php?url=$1 [L,QSA]
```

**Fonctionnement:**
```
/n-importe-quoi/path/ici → index.php?url=n-importe-quoi/path/ici
```

---

## Bonnes pratiques

1. **Tester vos regex** - Utilisez un testeur regex en ligne
2. **Toujours mettre `^` et `$`** - Pour éviter les surprises
3. **Vérifier l'ordre des règles** - Les règles s'appliquent dans l'ordre
4. **Ne pas oublier `[L]`** - Pour arrêter le traitement
5. **Exclure les fichiers/dossiers réels** - Voir les `RewriteCond`
6. **Tester en production** - Les serveurs peuvent avoir des config différentes

---

## Vérification que ça marche

### 1. Vérifier que mod_rewrite est activé

En terminal:
```bash
apache2ctl -M | grep rewrite
# ou
httpd -M | grep rewrite
```

### 2. Vérifier les logs Apache

```bash
tail -f /var/log/apache2/error.log
```

### 3. Tester une URL

```bash
curl -i http://localhost:8080/2026/03/29/mon-article_2.html
```

Doit retourner un 200, pas un 404.

---

## Dépannage courant

| Problème | Cause probable | Solution |
|----------|---|---|
| Erreur 404 | Regex ne match pas | Tester la regex |
| Boucle infinie | Règle appliquée récursivement | Ajouter `[L]` |
| Les paramètres GET disparaissent | Oublier `[QSA]` | Ajouter `[QSA]` aux flags |
| Ça ne marche pas du tout | `mod_rewrite` désactivé | Activer le module Apache |
| Ça marche en local pas en prod | AllowOverride pas activé | Modifier la config Apache |
