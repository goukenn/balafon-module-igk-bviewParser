# Spécification complète des fichiers .bview - Balafon

## Vue d'ensemble

Les fichiers `.bview` (Balafon View) sont des fichiers de vue spéciaux du framework Balafon. Ils utilisent une syntaxe déclarative et concise pour définir l'interface utilisateur sans nécessiter de logique PHP complexe.

**Caractéristiques principales :**
- Syntaxe proche du CSS et HTML
- Support de l'interpolation de variables avec `{{ }}`
- Accès au contexte (`$raw`, `$ctrl`)
- Support des arguments JSON et des attributs structurés
- Priorité automatique sur les fichiers `.phtml`

---

## 1. Installation du module bviewParser

Le module `igk/bviewParser` doit être installé pour traiter les fichiers `.bview`.

### Installation via CLI

```bash
balafon --module:install igk/bviewParser
```

### Vérification

```bash
balafon --module:status igk/bviewParser
```

### Dépôt GitHub

```
https://github.com/goukenn/balafon-module-igk-bviewParser.git
```

---

## 2. Priorité de traitement

**Règle importante :** Si un fichier `.bview` existe, **SEULEMENT** lui sera chargé.

```
Requête → /notebook/default
    ↓
PageLayout cherche Views/default.bview
    ├─ Existe ? → Charge default.bview (default.phtml IGNORÉ)
    └─ N'existe pas ? → Cherche default.phtml
```

### Structure recommandée

```
Views/
├─ default.bview   ← Seul ce fichier sera chargé
└─ default.phtml   ← Totalement ignoré (ne pas garder les deux)
```

---

## 3. Syntaxe fondamentale des nœuds

### Format général

```
element.classe1.classe2#id@name(args)[attributs]
```

### Composants

| Élément | Syntaxe | Obligatoire | Exemple |
|---------|---------|-------------|---------|
| **Élément** | `div`, `span`, etc. | ✓ Oui | `div`, `button` |
| **Classes** | `.classe1.classe2` | ✗ Non | `.container.large` |
| **ID** | `#identifiant` | ✗ Non | `#main-header` |
| **Name** | `@attribut` | ✗ Non | `@email` |
| **Arguments** | `(arg="val")` | ✗ Non | `(type="text")` |
| **Attributs** | `[attr:val]` | ✗ Non | `[data-id:123]` |

### Exemples basiques

```bview
div                              → <div></div>
div.container                    → <div class="container"></div>
div.container.center             → <div class="container center"></div>
div#main                         → <div id="main"></div>
div.box#main                     → <div class="box" id="main"></div>
input@email                      → <input name="email">
button.btn(type="submit")        → <button class="btn" type="submit"></button>
```

---

## 4. Transmission de contenu aux nœuds

### Préfixe `-` : Contenu textuel

Le tiret `-` précède toujours le contenu textuel.

```bview
/* Contenu textuel simple */
p{ - Hello World }

/* Contenu textuel avec variables */
h1{ - Bienvenue {{ $name }} }

/* Contenu textuel avec HTML */
p{ - Texte avec <strong>contenu</strong> }

/* Contenu textuel avec interpolation */
span{ - ID : {{ $id }}, Email : {{ $email }} }
```

### Préfixe `+` : Nœuds simples (sans enfants)

Le plus `+` s'utilise pour les nœuds qui **n'ont pas d'enfants**. Cela évite les accolades `{}`.

```bview
/* Élément simple sans contenu */
+ img("/path/to/image.jpg")

/* Nœud simple avec classes */
+ br.separator

/* Nœud simple avec attributs */
+ input@email(type="email" placeholder="Email")

/* Nœud simple avec attributs structurés */
+ hr[class:separator, data-type:visual]
```

### Structure mixte : `+` et `-` ensemble

```bview
div.card {
    + img("/img/bg.jpg")
    
    h2.title { 
        - Mon titre
    }
    
    + hr.divider
    
    p.description { 
        - Description du produit
    }
    
    + button.btn-primary("Ajouter")
}
```

**Explication :**
- `+ img(...)` : Image simple sans enfants (pas de `{}`)
- `h2.title { - Mon titre }` : Titre avec contenu textuel
- `+ hr.divider` : Élément HR simple
- `p.description { - ... }` : Paragraphe avec contenu
- `+ button(...)` : Bouton simple

### Hiérarchie avec accolades

Lorsqu'un nœud a des enfants (d'autres nœuds ou du contenu), utilisez les accolades `{}`.

```bview
div.container {
    /* Les nœuds/contenu à l'intérieur */
    h1 { - Titre }
    
    p { 
        - Paragraphe
    }
    
    footer {
        - Pied de page
    }
}
```

---

## 5. Commentaires

### Commentaires sur une ligne

```bview
/* Commentaire simple */
```

### Commentaires multilignes

```bview
/* Commentaire
   sur plusieurs
   lignes */
```

### Commentaires spéciaux (au début du fichier)

```bview
// @description Page de contact
// @author C.A.D. BONDJE DOUE
// @date 2025-10-16
```

---

## 6. Variables et interpolation

### Syntaxe d'interpolation

Utilisez `{{ $variable }}` pour insérer des variables dans le contenu ou les attributs.

### Contexte d'exécution

Deux objets sont toujours disponibles :
- **`$raw`** : Les données brutes passées à la vue
- **`$ctrl`** : Le contrôleur qui initie la transformation

### Raccourci : Variables directes

Les variables de `$raw` sont directement accessibles :

```bview
/* Équivalent */
{{ $name }}     ←→     {{ $raw->name }}

/* Équivalent */
{{ $email }}    ←→     {{ $raw->email }}
```

### Exemples d'utilisation

```bview
/* Contenu textuel */
p{ - Nom : {{ $name }} }

/* Attributs */
img(src="{{ $imageUrl }}", alt="{{ $imageAlt }}")

/* Attributs structurés */
div[data-user-id:{{ $userId }}]{
    - Contenu
}

/* Expressions */
span{ - Prix : {{ $price * $quantity }} € }

/* Valeur par défaut */
p{ - Ville : {{ $city ?? 'Non spécifiée' }} }

/* Opérateur ternaire */
span.badge.{{ $active ? 'success' : 'danger' }}{
    - {{ $active ? 'Actif' : 'Inactif' }}
}

/* Propriétés d'objets */
p{ - Email : {{ $user->email }} }

/* Méthodes du contrôleur */
a(href="{{ $ctrl->uri('/page') }}"){
    - Lien
}
```

### Backticks simples vs Triple-backticks

#### Backticks simples `` ` ` `` : Avec interpolation

```bview
/* Variables interpolées dans backticks */
p{ - `Bonjour {{ $name }}` }

/* Objets affichés */
p{ - `Données : {{ $data }}` }

/* Output: "Données : {"i":30,"j":"basic"}" */
```

#### Triple-backticks ` ``` ` : SANS interpolation

```bview
/* Triple-backticks: pas d'interpolation */
div{
    ```bjs
    console.log('{{ $variable }}')  // Affiché littéralement, pas interpolé
    ```
}
```

### Filtres avec pipe `|`

Les filtres transforment les valeurs avec la syntaxe `{{ $variable | filtre }}`.

```bview
/* Filtre uppercase */
p{ - {{ $ctrl | uppercase }} }

/* Output: "FOREMJOBDASHBOARDCONTROLLER" */

/* Autres filtres (si disponibles) */
p{ - {{ $text | lowercase }} }
p{ - {{ $number | round }} }
p{ - {{ $string | trim }} }
```

### Accès aux données du contexte

```bview
/* $raw - données brutes */
{{ $raw->property }}
{{ $raw['key'] }}
{{ $raw->nested->property }}

/* $ctrl - contrôleur */
{{ $ctrl->getName() }}
{{ $ctrl->getBaseUri() }}
{{ $ctrl->uri('/path') }}

/* Variables directes (raccourci de $raw) */
{{ $variable }}
{{ $object->property }}
{{ $array[0] }}

/* Avec filtres */
{{ $ctrl | uppercase }}
{{ $text | lowercase }}
```

### Sécurité : Échappement

Par défaut, **toutes les variables sont automatiquement échappées** pour prévenir les injections XSS :

```bview
/* Variable échappée automatiquement */
{{ $userInput }}  /* Les caractères < > & sont convertis en entités */

/* HTML brut (non échappé) - Utilisation prudente */
{!! $htmlContent !!}  /* ⚠️ Réservé au contenu fiable uniquement */
```

---

## 7. Arguments `()` vs Attributs `[]`

### Arguments `()` : Paramètres PHP

Les arguments peuvent être :
- Des arguments nommés avec `=` (clé-valeur)
- Des arguments positionnels (valeurs seules)
- Des fonctions de rendu personnalisées

```bview
/* Argument nommé */
input(type="email" placeholder="Email" required)

/* Argument positionnel */
a.link('//google.com')

/* Paramètres mixtes */
img(src="/img/logo.png" alt="Logo")

/* Avec variables */
form(action="{{ $submitUrl }}" method="POST")
```

### Attributs `[]` : Attributs HTML/SVG/XML

Les attributs utilisent le deux-points `:` pour séparer clé et valeur.

```bview
div[data-id:123, data-type:user]

svg[xmlns:http://www.w3.org/2000/svg]

rect[x:10, y:20, width:100, height:50]

/* Attributs avec variables */
div[data-user-id:{{ $userId }}, data-role:{{ $role }}]
```

### Attributs vides `[]`

Les crochets vides `[]` indiquent l'absence d'attributs supplémentaires.

```bview
a.link('//google.com')[]{
   - Lien vers Google
}
```

### Comparaison

| Feature | Arguments `()` | Attributs `[]` |
|---------|----------------|----------------|
| Séparateur clé/valeur | `=` ou position | `:` |
| Variables | ✓ `title="{{ $x }}"` | ✓ `[id:{{ $x }}]` |
| JSON/Objets | ✓ `options={"key":"val"}` | ✗ Non supporté |
| Guillemets | Recommandés | Optionnels |
| Fonctions | ✓ `link('url')` | ✗ Non supporté |

---

## 8. JSON et arguments complexes

### JSON comme argument

```bview
/* JSON comme paramètre */
component(config={"title":"Mon titre", "icon":"star"})

/* JSON multilignes */
component(
    config={
        "name": "Product",
        "price": 99.99,
        "tags": ["sale", "featured"]
    }
)
```

### Chaînes entre backticks simples

Les backticks simples `` ` ` `` permettent d'écrire du texte multilignes **avec interpolation de variables**.

```bview
/* Backticks avec interpolation */
p{ - `Bonjour {{ $name }}, votre ID est {{ $id }}` }

/* Backticks multilignes avec variables */
div{
    - `
        Bienvenue {{ $user->name }}
        Email: {{ $user->email }}
        Membre depuis: {{ $joinDate }}
    `
}

/* Affichage d'objets complexes */
p{ - `Données : {{ $data }}` }

/* Output: "Données : {"i":30,"j":"basic"}" */
```

### Triple-backticks avec code JavaScript

Les triple-backticks ` ``` ` contiennent du **code JavaScript SANS interpolation**.

```bview
div{
    ```bjs
    console.log('Texte littéral {{ $variable }}')  // Pas d'interpolation
    ```
}

/* Les {{ }} ne sont pas traités, affichés littéralement */
```

### Différence clé

| Type | Syntaxe | Interpolation | Usage |
|------|---------|---------------|-------|
| **Backticks simples** | `` `texte` `` | ✓ Oui | Contenu textuel |
| **Triple-backticks** | ` ```bjs code``` ` | ✗ Non | Code JavaScript |

---

## 9. Opérateur de hiérarchie `>`

L'opérateur `>` crée une hiérarchie directe.

```bview
/* Hiérarchie avec > */
main > section > div {
    h1 { - Titre }
}

/* Équivalent à */
main {
    section {
        div {
            h1 { - Titre }
        }
    }
}
```

---

## 10. Syntaxe compacte sur une ligne

Plusieurs nœuds peuvent être déclarés sur une même ligne.

```bview
span{ - Prénom }   span{ - Nom }   span{ - (Age) }
```

**Output :**
```html
<span>Prénom</span>
<span>Nom</span>
<span>(Age)</span>
```

---

## 11. Exemples pratiques complets

### Exemple 1 : Lien avec fonction et filtres

```bview
a.link('//google.com')[]{
   - `hello {{ $b }} from {{ $ctrl | uppercase }}`
}
```

**Contexte :**
```php
$data = (object)[
    'b' => (object)['i' => 30, 'j' => 'basic'],
    'ctrl' => $controller  // Objet contrôleur
];
```

**Output HTML :**
```html
<a class="link" href="//google.com">hello {"i":30,"j":"basic"} from FOREMJOBDASHBOARDCONTROLLER</a>
```

**Explication :**
- `a.link('//google.com')` : Fonction `link()` avec URL comme paramètre
- `[]` : Pas d'attributs supplémentaires
- `` `hello {{ $b }}` `` : Backticks avec interpolation (affiche l'objet)
- `{{ $ctrl | uppercase }}` : Filtre `uppercase` appliqué au contrôleur

### Exemple 2 : Carte de profil utilisateur

```bview
article.user-card[data-user-id:{{ $id }}]{
    div.card-header{
        + img("/img/avatar.jpg")
        h2.user-name{ - `{{ $name }} (ID: {{ $id }})` }
    }
    
    div.card-body{
        p{ - 📧 {{ $email }} }
        p{ - Membre depuis {{ $joinDate }} }
    }
    
    div.card-footer{
        a.btn(href="{{ $ctrl->uri('/profile/' . $id) }}"){
            - Voir le profil
        }
    }
}
```

### Exemple 3 : Formulaire avec validation

```bview
form.login-form(action="/login" method="POST"){
    div.form-group{
        label(for="email"){ - Email }
        input.form-control#email@email(
            type="email"
            required
            placeholder="votre@email.com"
        ){
        }
    }
    
    div.form-group{
        label(for="password"){ - Mot de passe }
        input.form-control#password@password(
            type="password"
            required
            minlength="8"
        ){
        }
    }
    
    button.btn.btn-primary(type="submit"){
        - Se connecter
    }
}
```

### Exemple 4 : Navigation responsive

```bview
nav.navbar#main-nav{
    div.nav-container{
        a.nav-brand(href="/"){
            - MonSite
        }
        
        button.nav-toggle(aria-label="Menu"){
            + span.bar
            + span.bar
            + span.bar
        }
        
        div.nav-menu{
            ul.nav-list{
                li.nav-item{
                    a.nav-link(href="/"){
                        - Accueil
                    }
                }
                li.nav-item{
                    a.nav-link(href="/services"){
                        - Services
                    }
                }
                li.nav-item{
                    a.nav-link(href="/contact"){
                        - Contact
                    }
                }
            }
        }
    }
}
```

### Exemple 5 : Liste de sites avec variables et filtres

```bview
main.sites-page{
    div.container{
        h1{ - `Notebook de sites - {{ $ctrl | uppercase }}` }
        
        table.sites-table{
            thead{
                tr{
                    th{ - Titre }
                    th{ - Site }
                    th{ - TVA }
                }
            }
            tbody{
                tr.site-row[data-site-id:{{ $site->id }}]{
                    td{ - `{{ $site->title | uppercase }}` }
                    td{
                        a.link('{{ $site->url }}')[]{
                            - `{{ $site->url }}`
                        }
                    }
                    td{ - `{{ $site->vat }}` }
                }
            }
        }
    }
}
```

### Exemple 6 : Dashboard avec statistiques et filtres

```bview
main.dashboard{
    h1{ - `Tableau de bord - {{ $ctrl | uppercase }}` }
    
    section.stats-grid{
        div.stat-card[data-stat:revenue]{
            span.icon{ - 💰 }
            h3{ - Revenus }
            p.value{ - `{{ $revenue }}` }
            p.change.{{ $revenueTrend > 0 ? 'positive' : 'negative' }}{
                - `{{ $revenueTrend }}%`
            }
        }
        
        div.stat-card[data-stat:users]{
            span.icon{ - 👥 }
            h3{ - Utilisateurs }
            p.value{ - `{{ $userCount }}` }
            p.change.{{ $userTrend > 0 ? 'positive' : 'negative' }}{
                - `{{ $userTrend }}%`
            }
        }
        
        div.stat-card[data-stat:orders]{
            span.icon{ - 📦 }
            h3{ - Commandes }
            p.value{ - `{{ $orderCount }}` }
            p.change.neutral{ - `{{ $orderTrend }}%` }
        }
    }
}
```

---

## 12. Directives et commentaires globaux

### Directives

```bview
# @version 1.0
# @namespace components
# @import utils
```

### Commentaires globaux (spéciaux)

```bview
// @description Carte de produit réutilisable
// @author C.A.D. BONDJE DOUE
// @date 2025-10-16
```

---

## 13. Transformations en ligne (Code JavaScript)

### Syntaxe triple backticks (recommandée)

```bview
div.card{
    ```bjs
    console.log('Composant initialisé')
    ```
}
```

**Output HTML :**
```html
<div class="card">
    <script autoremove="true" language="javascript" type="text/balafonjs">
        console.log('Composant initialisé')
    </script>
</div>
```

### Caractéristiques

- **Attribut `autoremove`** : Supprime le script après exécution
- **Pas d'interpolation** : `{{ }}` n'est pas traité dans les blocs de code
- **Langage** : Utilisez `bjs` pour BalafonJS

### Backticks simples vs Triple-backticks : Récapitulatif

| Aspect | Backticks `` ` ` `` | Triple-backticks ` ``` ` |
|--------|------|------|
| **Interpolation** | ✓ Oui | ✗ Non |
| **Multilignes** | ✓ Oui | ✓ Oui |
| **Variables** | ✓ Affichées | ✗ Littérales |
| **Usage** | Contenu texte | Code JavaScript |
| **Exemple** | `` `Bonjour {{ $x }}` `` | ` ```bjs console.log('test')``` ` |

---

## 14. Configuration

### Fichier de configuration

```php
<?php
// Data/configs.dev.php

return [
    'bviewParser' => [
        'cache_enabled' => true,
        'cache_dir' => 'Data/cache/bview',
        'debug' => false,
        'show_errors' => true,
        'minify' => false,
        'variable_interpolation' => true,
        'auto_escape' => true
    ]
];
```

### Mode debug

```bash
export IGK_BVIEW_DEBUG=1
```

---

## 15. Commandes CLI

```bash
# Installer le module
balafon --module:install igk/bviewParser

# Valider la syntaxe d'un fichier
balafon --bview:validate Views/myfile.bview

# Compiler tous les fichiers
balafon --bview:compile Views/

# Vider le cache
balafon --cache:clear bview

# Lister les modules
balafon --module:list

# Vérifier le statut
balafon --module:status igk/bviewParser
```

---

## 16. Bonnes pratiques

### Organisation des fichiers

```
Views/
├─ layouts/
│  ├─ main.bview
│  └─ admin.bview
├─ pages/
│  ├─ home.bview
│  └─ about.bview
├─ components/
│  ├─ header.bview
│  ├─ footer.bview
│  └─ sidebar.bview
└─ forms/
   ├─ login-form.bview
   └─ contact-form.bview
```

### Nommage

- **Pages** : `nom-page.bview` (ex: `about-us.bview`)
- **Composants** : `nom-composant.bview` (ex: `user-card.bview`)
- **Layouts** : `nom-layout.bview` (ex: `main-layout.bview`)

### Ne jamais garder les deux versions

❌ **ERREUR** :
```
Views/
├─ home.bview   ← Sera utilisé
└─ home.phtml   ← Code mort
```

✓ **CORRECT** :
```
Views/
└─ home.bview   ← Seul fichier
```

### Structure hiérarchique claire

```bview
/* ✓ Bon : Indentation cohérente */
main.page{
    section.hero{
        h1{ - Titre }
    }
    section.content{
        p{ - Contenu }
    }
}

/* ✗ À éviter : Indentation incohérente */
main.page{
section.hero{
h1{ - Titre }
}}
```

### Documentation des variables

```bview
/*
 * product-card.bview
 * 
 * Variables attendues :
 * - $id (int) : ID du produit
 * - $name (string) : Nom du produit
 * - $price (float) : Prix
 * - $image (string) : URL de l'image
 */

article.product-card[data-id:{{ $id }}]{
    + img(src=[[:@image]], alt=[[:@name]]")
    h3{ - {{ $name }} }
    p.price{ - {{ $price }} € }
}
```

---

## 17. Comparaison : .bview vs .phtml

### Formulaire de contact

**Version .phtml :**
```php
<?php
$form = $t->form();
$form->setAttributes(['id' => 'contact-form', 'class' => 'form']);
$div = $form->div()->setClass('form-group');
$label = $div->label();
$label->setAttributes(['for' => 'name']);
$label->Content = 'Nom';
$input = $div->input();
$input->setAttributes(['id' => 'name', 'name' => 'name', 'type' => 'text']);
$button = $form->button();
$button->setAttributes(['type' => 'submit', 'class' => 'btn']);
$button->Content = 'Envoyer';
?>
```

**Lignes** : 13

---

**Version .bview :**
```bview
form#contact-form.form{
    div.form-group{
        label(for="name"){ - Nom }
        input#name@name(type="text")
    }
    button.btn(type="submit"){ - Envoyer }
}
```

**Lignes** : 6

---

**Gain : -54% de lignes de code**

---

## 18. Résumé : Tableau récapitulatif

| Élément | Syntaxe | Description |
|---------|---------|-------------|
| **Élément** | `div` | Balise HTML |
| **Classes** | `.class1.class2` | Classes CSS |
| **ID** | `#monid` | Identifiant unique |
| **Name** | `@email` | Attribut name |
| **Arguments** | `(type="text")` | Paramètres PHP |
| **Attributs** | `[data-id:123]` | Attributs HTML/SVG |
| **Contenu** | `{ - texte }` | Contenu textuel |
| **Nœud simple** | `+ element()` | Sans enfants, pas de {} |
| **Variable** | `{{ $var }}` | Interpolation |
| **Contexte** | `{{ $raw->x }}` | Données brutes |
| **Contrôleur** | `{{ $ctrl->uri() }}` | Méthode du contrôleur |
| **Commentaire** | `/* texte */` | Commentaire |
| **Code JS** | ` ``` `bjs code` ``` ` | Script BalafonJS |

## 19. Ressources

- **Dépôt GitHub** : https://github.com/goukenn/balafon-module-igk-bviewParser.git
- **Documentation Balafon** : https://balafon.igkdev.com
- **Framework Balafon** : https://github.com/goukenn/igkdev-balafon

---

## 20. Guide de démarrage rapide

### Installation

```bash
# Installer le module bviewParser
balafon --module:install igk/bviewParser

# Vérifier l'installation
balafon --module:status igk/bviewParser
```

### Créer votre première vue

```bash
# Créer un fichier .bview
touch Views/hello.bview
```

```bview
/* Views/hello.bview */
main.hello-page{
    div.container{
        h1{ - `Bonjour {{ $name }}` }
        
        p{ - Bienvenue sur mon site }
        
        div.info{
            p{ - `ID: {{ $id }}` }
            p{ - `Contrôleur: {{ $ctrl | uppercase }}` }
        }
    }
}
```

### Tester la vue

Accédez à l'URL correspondante pour voir votre première vue `.bview` en action !

---

## 21. Checklist : Bonnes pratiques

- [ ] Installation du module `igk/bviewParser`
- [ ] Un seul fichier par vue (`.bview` OU `.phtml`, jamais les deux)
- [ ] Nommage descriptif des fichiers
- [ ] Indentation cohérente (4 espaces recommandé)
- [ ] Variables documentées en commentaires
- [ ] Utilisation de `+` pour les nœuds simples sans enfants
- [ ] Backticks simples `` ` ` `` pour le contenu avec variables
- [ ] Triple-backticks ` ``` ` pour le code JavaScript
- [ ] Attributs avec `:` dans `[]`
- [ ] Arguments avec `=` dans `()`
- [ ] Sécurité : Éviter `{!! !!}` sauf pour contenu fiable
- [ ] Structure claire : Une hiérarchie logique
- [ ] Tests : Valider chaque vue après modification

---

## 22. Limitations et alternatives

### Ce qu'on NE peut PAS faire dans .bview

```bview
/* ✗ ERREUR : Boucles */
@foreach($items as $item)
    div{ - {{ $item }} }
@endforeach

/* ✗ ERREUR : Conditions */
@if($condition)
    div{ - Visible }
@endif

/* ✗ ERREUR : Logique PHP complexe */
{{ $array->map(fn($x) => $x * 2) }}
```

### Solution : Utiliser .phtml pour la logique

```php
<?php
// mypage.phtml
foreach ($items as $item) {
    // Logique complexe
}
?>
```

### Combiner .bview et .phtml

```php
<?php
// container.phtml
foreach ($products as $product) {
    // Préparer les données
    $productData = $product;
    
    // Inclure la vue .bview
    include 'Views/product-card.bview';
}
?>
```

---

## 23. Dépannage courant

### Le fichier .bview n'est pas chargé

```bash
# Vérifier l'installation du module
balafon --module:status igk/bviewParser

# Activer le module
balafon --module:enable igk/bviewParser

# Vider le cache
balafon --cache:clear bview

# Valider la syntaxe
balafon --bview:validate Views/myfile.bview
```

### Erreur : "Unclosed brace at line X"

Vérifier les accolades `{}` dans le fichier. Chaque `{` doit avoir un `}` correspondant.

### Les variables ne s'affichent pas

Vérifier que :
1. Les backticks `` ` ` `` sont utilisés pour l'interpolation
2. Les variables existent dans le contexte
3. L'échappement HTML n'affecte pas l'affichage

### Variables en majuscules : `{{ $ctrl | uppercase }}`

Utiliser les filtres avec le pipe `|` pour transformer les variables.

---

## 24. Exemples complètes : Du plus simple au plus complexe

### Niveau 1 : Simple

```bview
/* simple.bview */
div.welcome{
    h1{ - Bienvenue }
    p{ - Ceci est une page simple }
}
```

### Niveau 2 : Avec variables

```bview
/* user-info.bview */
div.user{
    h1{ - `Bonjour {{ $name }}` }
    p{ - `Email: {{ $email }}` }
}
```

### Niveau 3 : Avec arguments et filtres

```bview
/* social-link.bview */
a.social-link('{{ $url }}')[data-platform:{{ $platform | lowercase }}]{
    - `{{ $platform | uppercase }}`
}
```

### Niveau 4 : Complexe avec structures imbriquées

```bview
/* dashboard.bview */
main.dashboard[data-user:{{ $user->id }}]{
    header.dashboard-header{
        h1{ - `Tableau de bord - {{ $user->name | uppercase }}` }
    }
    
    section.stats{
        div.stat-card[data-type:revenue]{
            span.value{ - `{{ $stats->revenue }}€` }
            span.label{ - Revenus }
        }
        
        div.stat-card[data-type:users]{
            span.value{ - `{{ $stats->users }}` }
            span.label{ - Utilisateurs }
        }
    }
    
    section.actions{
        a.btn.btn-primary('{{ $ctrl->uri("/export") }}')[]{
            - Exporter
        }
        
        a.btn.btn-secondary('{{ $ctrl->uri("/settings") }}')[]{
            - Paramètres
        }
    }
    
    footer.dashboard-footer{
        p{ - `Dernière mise à jour: {{ $lastUpdate }}` }
    }
}
```

---

## 25. Performances et optimisations

### Cache

```php
<?php
// Activer le cache en production
return [
    'bviewParser' => [
        'cache_enabled' => true,
        'cache_dir' => 'Data/cache/bview'
    ]
];
```

### Précompilation

```bash
# Compiler tous les fichiers .bview
balafon --bview:compile Views/

# Cela crée des versions compilées en cache
```

### Minification

```php
<?php
// Minifier le HTML généré
return [
    'bviewParser' => [
        'minify' => true  // Production uniquement
    ]
];
```

---

## 26. Intégration avec Balafon

### Passer des données à une vue .bview

```php
<?php
// Dans le contrôleur
class MyController extends ApplicationController {
    public function myAction() {
        $data = (object)[
            'name' => 'Jean',
            'email' => 'jean@example.com',
            'items' => [1, 2, 3]
        ];
        
        // La vue myAction.bview reçoit $data en contexte
        return ['data' => $data];
    }
}
```

### Accéder au contrôleur dans la vue

```bview
/* myview.bview */
div{
    p{ - `Contrôleur: {{ $ctrl->getName() }}` }
    p{ - `Base URI: {{ $ctrl->getBaseUri() }}` }
    p{ - `Lien: {{ $ctrl->uri('/page') }}` }
}
```

---

## 27. Comparaison finale : .bview vs .phtml

### Cas d'usage : Afficher une liste de produits

**Approche .phtml (imperative) :**
```php
<?php
$ul = $t->ul();
foreach ($products as $product) {
    $li = $ul->li();
    $li->setClass('product-item');
    $li->setAttributes(['data-id' => $product->id]);
    $span = $li->span();
    $span->setClass('product-name');
    $span->Content = $product->name;
}
?>
```

**Approche .bview (declarative) :**
```bview
ul{
    li.product-item[data-id:{{ $product->id }}]{
        span.product-name{ - `{{ $product->name }}` }
    }
}
```

**Observation :** .bview est plus lisible et concis pour les structures statiques.

---

**Dernière mise à jour** : 16 octobre 2025  
**Version** : 3.0  
**Langage** : Français

*Documentation complète et à jour des fichiers .bview dans Balafon. Couvre toute la syntaxe, les variables, les filtres, les fonctions personnalisées, et toutes les bonnes pratiques pour développer avec .bview.*