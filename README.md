# balafon module [igk/bviewParser]
@C.A.D. BONDJE DOUE
allow to use .bview file as source of loading project's view. 


### miltiline string with backtick 
` 
information du jour 
`

## how to use?
- create in a project's view folder a view file that will have ".bview" extension 
- write document with "bview language syntaxe"


## bview language syntax

```json
div.main > section > nav{
    ul{
        li.list-item#first{
            - first item 
        }
        /** define class and id */
        li.item-item#second{
            - second item
            /* active attribute */
            @active
            /* set node attribute with array */
            transform:[
               /* here selection not allowed */ 
            ]
            /* set node attribute with nil|null */ 
            filter: nil

        } 
        li.item{
            input{
                type:text
                /* activate muliple attribute */
                @disabled @readonly
            }
        }
        li.item{
            /* conditional node */
            *if: {{ $raw->active }}
            - this item is active
            
        }
        li.item{
            /* build with json data*/
            transform: json({
                "info"=>"true",
                "litteral"=>"ok"
            }),
            /* load bhtml litteral */
            - bhtml(<div> loading some data directly</div>)

            /* with html */
            - html(<div></div>)
            /* with xml */
            - xml(<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><path d="M336 176h40a40 40 0 0140 40v208a40 40 0 01-40 40H136a40 40 0 01-40-40V216a40 40 0 0140-40h40" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M176 272l80 80 80-80M256 48v288"/></svg>)
        }
    }
}
```




## Features 

## release 
- 1.0


## Know Issues


# Documentation Balafon - Fichiers .bview

## Table des matières

- [Introduction](#introduction)
- [Installation du module bviewParser](#installation-du-module-bviewparser)
- [Priorité de traitement](#priorité-de-traitement)
- [Syntaxe des nœuds](#syntaxe-des-nœuds)
- [Exemples pratiques](#exemples-pratiques)
- [Configuration](#configuration)
- [Commandes CLI](#commandes-cli)
- [Débogage](#débogage)
- [Migration](#migration)
- [Ressources](#ressources)

---

## Introduction

Les fichiers `.bview` (Balafon View) sont des fichiers de vue spéciaux dans le framework Balafon qui utilisent une syntaxe simplifiée et structurée pour définir l'interface utilisateur.

### Avantages des fichiers .bview

- ✅ **Syntaxe concise** : Moins de code à écrire
- ✅ **Lisibilité améliorée** : Structure claire et hiérarchique
- ✅ **Priorité automatique** : Chargés en priorité sur les `.phtml`
- ✅ **Parsing optimisé** : Traitement efficace par le module dédié
- ✅ **Facile à apprendre** : Proche de la syntaxe HTML

### Quand utiliser .bview

| Utilisez .bview pour | Utilisez .phtml pour |
|---------------------|---------------------|
| Pages de contenu statique | Logique PHP complexe |
| Templates de présentation | Manipulation de données |
| Structures HTML pures | Conditions et boucles |
| Prototypes rapides | Requêtes base de données |
| Landing pages | Génération dynamique |

---

## Installation du module bviewParser

Les fichiers `.bview` sont traités par le module système **`igk/bviewParser`**.

### Méthode 1 : Via Balafon CLI (Recommandée)

```bash
# Installation automatique
balafon --module:install igk/bviewParser
```

Cette commande :
- Télécharge automatiquement le module depuis GitHub
- L'installe dans le répertoire approprié
- Configure les dépendances
- Active le module automatiquement

### Méthode 2 : Installation manuelle

```bash
# Cloner le dépôt Git
cd /path/to/balafon/modules
git clone https://github.com/goukenn/balafon-module-igk-bviewParser.git igk-bviewParser
```

### Méthode 3 : Via le CLI interne du projet

```bash
# Depuis la racine de votre projet
igk/bin/balafon --module:install igk/bviewParser
```

### Vérification de l'installation

```bash
# Lister les modules installés
balafon --module:list

# Vérifier le statut du module
balafon --module:status igk/bviewParser
```

### Dépôt GitHub

**URL :** [https://github.com/goukenn/balafon-module-igk-bviewParser.git](https://github.com/goukenn/balafon-module-igk-bviewParser.git)

---

## Priorité de traitement

### Règle importante

> **Si le fichier `.bview` existe, SEULEMENT lui sera chargé.**

Il n'y a **AUCUNE fusion** ou combinaison entre `.bview` et `.phtml`.

### Comportement du PageLayout

```
Requête → /notebook/default
    ↓
PageLayout cherche Views/default.bview
    ↓
    ├─ default.bview EXISTE ?
    │   ├─ OUI → Charge default.bview via igk/bviewParser
    │   │         └─ STOP (default.phtml n'est jamais consulté)
    │   │
    │   └─ NON → Cherche default.phtml
    │             └─ Charge default.phtml si existe
    ↓
Rendu de la page
```

### Exemple de structure

```
Views/
├── default.bview   ← SEULEMENT celui-ci sera chargé
└── default.phtml   ← TOTALEMENT IGNORÉ (jamais lu ni exécuté)
```

### Processus de traitement complet

```
1. Requête HTTP → /notebook/default
2. PageLayout cherche Views/default.bview
3. Si trouvé :
   ├─ Charge le module igk/bviewParser
   ├─ Parse la syntaxe .bview
   ├─ Génère le code HTML/PHP
   └─ Rend la vue
4. Si non trouvé :
   └─ Charge Views/default.phtml
```

---

## Syntaxe des nœuds

### Format général

```
nom_du_noeud[.classe1.classe2....][#identifiant][@name][(args)]
```

### Composants de la syntaxe

| Élément | Description | Obligatoire | Exemple |
|---------|-------------|-------------|---------|
| `nom_du_noeud` | Nom de la balise HTML | ✅ Oui | `div`, `section`, `article` |
| `.classe1.classe2` | Classes CSS multiples | ❌ Non | `.container.center` |
| `#identifiant` | Attribut ID | ❌ Non | `#main-header` |
| `@name` | Attribut name | ❌ Non | `@username` |
| `(args)` | Arguments/attributs supplémentaires | ❌ Non | `(type="text")` |

### Exemples de base

#### 1. Nœud simple

```bview
div{
    - Contenu simple
}
```

**Génère :**
```html
<div>Contenu simple</div>
```

#### 2. Nœud avec classe

```bview
div.container{
    - Contenu dans un container
}
```

**Génère :**
```html
<div class="container">Contenu dans un container</div>
```

#### 3. Nœud avec plusieurs classes

```bview
div.container.center.large{
    - Contenu avec plusieurs classes
}
```

**Génère :**
```html
<div class="container center large">Contenu avec plusieurs classes</div>
```

#### 4. Nœud avec identifiant

```bview
header#main-header{
    - En-tête principal
}
```

**Génère :**
```html
<header id="main-header">En-tête principal</header>
```

#### 5. Nœud avec classe et identifiant

```bview
nav.navbar#top-nav{
    - Navigation principale
}
```

**Génère :**
```html
<nav class="navbar" id="top-nav">Navigation principale</nav>
```

#### 6. Nœud avec attribut name

```bview
input@username{
}
```

**Génère :**
```html
<input name="username">
```

#### 7. Nœud avec arguments

```bview
input@email(type="email" placeholder="Votre email"){
}
```

**Génère :**
```html
<input name="email" type="email" placeholder="Votre email">
```

#### 8. Combinaison complète

```bview
input.form-control.large#user-email@email(type="email" required placeholder="Email"){
}
```

**Génère :**
```html
<input class="form-control large" id="user-email" name="email" type="email" required placeholder="Email">
```

### Règles de syntaxe

#### Contenu textuel

Utilisez le tiret `-` pour précéder le contenu textuel :

```bview
h1{
    - Mon titre
}
p{
    - Mon paragraphe avec du <strong>texte enrichi</strong>
}
```

#### Commentaires

```bview
/* Commentaire sur une ligne */

/* Commentaire
   sur plusieurs
   lignes */

div.container{
    /* Commentaire interne */
    - Contenu
}
```

#### Hiérarchie imbriquée

```bview
main.page{
    header.site-header{
        h1.title{
            - Titre du site
        }
    }
    section.content{
        article.post{
            - Contenu de l'article
        }
    }
    footer.site-footer{
        - © 2025
    }
}
```

---

## Exemples pratiques

### Exemple 1 : Formulaire de connexion

```bview
/* login.bview */
main.login-page{
    div.container.center{
        form#login-form.auth-form(action="/login" method="POST"){
            h2{
                - Connexion
            }
            div.form-group{
                label(for="username"){
                    - Nom d'utilisateur
                }
                input.form-control#username@username(type="text" required){
                }
            }
            div.form-group{
                label(for="password"){
                    - Mot de passe
                }
                input.form-control#password@password(type="password" required){
                }
            }
            button.btn.btn-primary(type="submit"){
                - Se connecter
            }
        }
    }
}
```

### Exemple 2 : Page de profil

```bview
/* profile.bview */
main.profile-page{
    div.container{
        header.profile-header#user-header{
            div.avatar-container{
                img.avatar.large(src="/img/avatar.jpg" alt="Avatar"){
                }
            }
            h1.user-name{
                - Jean Dupont
            }
        }
        section.profile-content{
            article.info-section#personal-info{
                h2{
                    - Informations personnelles
                }
                dl.info-list{
                    dt{
                        - Email
                    }
                    dd{
                        - jean.dupont@example.com
                    }
                    dt{
                        - Téléphone
                    }
                    dd{
                        - +33 6 12 34 56 78
                    }
                }
            }
        }
    }
}
```

### Exemple 3 : Tableau de données

```bview
/* data-table.bview */
section.data-section{
    div.container.wide{
        h2.section-title{
            - Liste des sites
        }
        table.data-table.striped#sites-table{
            thead{
                tr{
                    th{
                        - Nom
                    }
                    th{
                        - Site
                    }
                    th{
                        - TVA
                    }
                }
            }
            tbody{
                tr.data-row{
                    td.site-name{
                        - Google
                    }
                    td.site-url{
                        a(href="https://google.com" target="_blank"){
                            - https://google.com
                        }
                    }
                    td.site-vat{
                        - BE0123456789
                    }
                }
            }
        }
    }
}
```

### Exemple 4 : Navigation

```bview
/* navigation.bview */
nav.main-nav#primary-navigation{
    div.nav-container{
        a.logo(href="/"){
            - Mon Site
        }
        ul.nav-menu{
            li.nav-item.active{
                a.nav-link(href="/"){
                    - Accueil
                }
            }
            li.nav-item{
                a.nav-link(href="/about"){
                    - À propos
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
```

### Exemple 5 : Carte (Card)

```bview
/* product-card.bview */
div.card.shadow#product-card(data-product-id="123"){
    div.card-header{
        h3.card-title{
            - Nom du produit
        }
    }
    div.card-body{
        img.product-image(src="/img/product.jpg" alt="Produit"){
        }
        p.card-description{
            - Description du produit avec toutes ses caractéristiques.
        }
        div.price-container{
            span.price{
                - 99,99 €
            }
        }
    }
    div.card-footer{
        button.btn.add-to-cart(data-action="add-cart"){
            - Ajouter au panier
        }
    }
}
```

### Exemple 6 : Extension (fichier du projet)

```bview
/* extension.bview */
main.section{
    - load and render extension
    div.container{
        h1{
            - building loading extension
        }
        code{
            - yo code <i>extension_name</i>
        }
        - Setup :
        ul{
            li{
                - choose <span><b>New Language Support</b></span>
            }  
            li{
                - configure
            }
        }
    }
}
```

---

## Configuration

### Configuration du module dans le contrôleur

```php
// SiteNoteBookController.php
class SiteNoteBookController extends ApplicationController{
    public function __construct(){
        parent::__construct();
        
        // S'assurer que le module bviewParser est chargé
        igk_require_module('igk/bviewParser');
    }
}
```

### Configuration globale

```php
<?php
// Dans .global.php ou le fichier d'initialisation
igk_require_module('igk/bviewParser');
```

### Vérification du chargement

```php
<?php
// Vérifier si le module est disponible
if (igk_is_module_loaded('igk/bviewParser')) {
    echo "✅ Module bviewParser chargé";
} else {
    echo "❌ Module non disponible";
    echo "Installez-le avec : balafon --module:install igk/bviewParser";
}
```

### Options de configuration avancées

```php
<?php
// Dans Data/configs.dev.php ou config.php

return [
    'bviewParser' => [
        // Activer le cache des fichiers parsés
        'cache_enabled' => true,
        
        // Répertoire de cache
        'cache_dir' => 'Data/cache/bview',
        
        // Mode debug
        'debug' => false,
        
        // Afficher les erreurs de parsing
        'show_errors' => true,
        
        // Préfixe des classes générées
        'class_prefix' => '',
        
        // Minifier la sortie HTML
        'minify' => false
    ]
];
```

### Mode debug

```php
<?php
// Activer le mode debug
igk_set_env('IGK_BVIEW_DEBUG', true);

// Afficher les logs de parsing
igk_set_env('IGK_BVIEW_VERBOSE', true);
```

---

## Commandes CLI

### Installation et gestion des modules

```bash
# Installer le module bviewParser
balafon --module:install igk/bviewParser

# Installer plusieurs modules
balafon --module:install igk/bviewParser igk/io/GraphQl

# Lister tous les modules
balafon --module:list

# Afficher les informations d'un module
balafon --module:info igk/bviewParser

# Vérifier le statut
balafon --module:status igk/bviewParser

# Mettre à jour le module
balafon --module:update igk/bviewParser

# Désinstaller le module
balafon --module:uninstall igk/bviewParser

# Activer/désactiver un module
balafon --module:enable igk/bviewParser
balafon --module:disable igk/bviewParser

# Réinstaller complètement
balafon --module:reinstall igk/bviewParser
```

### Commandes spécifiques .bview

```bash
# Valider la syntaxe d'un fichier
balafon --bview:validate Views/myfile.bview

# Convertir .phtml en .bview
balafon --bview:convert Views/default.phtml

# Afficher l'AST d'un fichier
balafon --bview:ast Views/extension.bview

# Compiler tous les fichiers .bview
balafon --bview:compile Views/

# Vider le cache
balafon --cache:clear bview
```

### Recherche et aide

```bash
# Rechercher des modules
balafon --module:search bview

# Aide sur les modules
balafon --help module

# Aide spécifique au module
balafon --module:help igk/bviewParser

# Liste des commandes bview
balafon --help bview
```

---

## Débogage

### Problèmes courants et solutions

#### Module non trouvé

```bash
# Erreur
Error: Module 'igk/bviewParser' not found

# Solutions
balafon --module:list                    # Vérifier l'installation
balafon --module:install igk/bviewParser # Réinstaller
ls -la Lib/igk/Lib/Modules/              # Vérifier les permissions
```

#### Fichier .bview non parsé

```bash
# Vérifier le statut
balafon --module:status igk/bviewParser

# Activer le module
balafon --module:enable igk/bviewParser

# Vider le cache
balafon --cache:clear
```

#### Erreur de syntaxe

```bview
/* Fichier avec erreur */
div.container{
    h1{
        - Titre
    /* } manquant */
}
```

**Activer le debug :**

```bash
export IGK_BVIEW_DEBUG=1
# Recharger la page pour voir l'erreur détaillée
```

### Logs et débogage avancé

```php
<?php
// Afficher les logs du parser
$logs = igk_get_module_logs('igk/bviewParser');
print_r($logs);

// Consulter le fichier de log
// Data/logs/bviewParser.log
```

### Messages d'erreur courants

| Erreur | Cause | Solution |
|--------|-------|----------|
| `Unclosed brace at line X` | Accolade manquante `}` | Vérifier les accolades |
| `Malformed attributes` | Guillemets mal fermés | Vérifier la syntaxe des attributs |
| `Unexpected token` | Caractère invalide | Vérifier la syntaxe générale |
| `Module not loaded` | Module non installé | Installer avec `--module:install` |

---

## Migration

### Stratégie de migration .phtml vers .bview

#### Étape 1 : Identifier les vues à migrer

```
Views/
├── about.phtml        ← Simple, candidat pour .bview
├── contact.phtml      ← Simple, candidat pour .bview
├── dashboard.phtml    ← Complexe, garder en .phtml
└── search.phtml       ← Complexe, garder en .phtml
```

#### Étape 2 : Créer le fichier .bview

```bash
# Créer le nouveau fichier .bview
touch Views/about.bview
```

```bview
/* about.bview */
main.about-page{
    div.container{
        h1{
            - À propos de nous
        }
        section.content{
            p{
                - Nous sommes une équipe passionnée...
            }
        }
    }
}
```

#### Étape 3 : Tester

```bash
# Accéder à l'URL pour tester
# http://localhost/notebook/about
```

#### Étape 4 : Supprimer l'ancien fichier

```bash
# Une fois validé, supprimer le .phtml
rm Views/about.phtml
```

### Script de migration automatique

```bash
#!/bin/bash
# migrate-to-bview.sh

echo "=== Migration vers .bview ==="

# 1. Installer le module
echo "Installation du module bviewParser..."
balafon --module:install igk/bviewParser

# 2. Lister les fichiers .phtml simples
echo "Recherche des fichiers éligibles..."
find Views/ -name "*.phtml" -type f > /tmp/phtml_files.txt

# 3. Afficher les candidats
echo "Fichiers candidats à la migration :"
while IFS= read -r file; do
    # Vérifier si le fichier contient peu de logique PHP
    php_count=$(grep -c "<?php" "$file")
    if [ "$php_count" -le 1 ]; then
        echo "  → $file (peut être converti)"
    fi
done < /tmp/phtml_files.txt

# 4. Nettoyer
rm /tmp/phtml_files.txt
echo "Migration terminée !"
```

### Checklist de migration

- [ ] Installer le module `igk/bviewParser`
- [ ] Identifier les vues simples (statiques)
- [ ] Créer le fichier `.bview` équivalent
- [ ] Tester le rendu de la nouvelle vue
- [ ] Vérifier l'absence d'erreurs de parsing
- [ ] Comparer le rendu HTML (avant/après)
- [ ] Supprimer le fichier `.phtml` d'origine
- [ ] Commit avec message clair

### Bonnes pratiques de migration

```markdown
## Commit de migration

**Avant :**
- Views/about.phtml (150 lignes PHP/HTML)

**Après :**
- Views/about.bview (45 lignes, syntaxe .bview)

**Avantages :**
- 70% de réduction du code
- Meilleure lisibilité
- Maintenance simplifiée
```

---

## Bonnes pratiques

### 1. Nommage des fichiers

```
✅ Bon
Views/
├── home.bview
├── about.bview
└── contact.bview

❌ Éviter
Views/
├── page1.bview
├── temp.bview
└── test123.bview
```

### 2. Structure hiérarchique claire

```bview
/* ✅ Bon : Indentation cohérente */
main.page{
    section.hero{
        h1{
            - Titre
        }
    }
    section.content{
        p{
            - Paragraphe
        }
    }
}

/* ❌ Éviter : Indentation incohérente */
main.page{
section.hero{
h1{
- Titre
}}}
```

### 3. Classes CSS sémantiques

```bview
/* ✅ Bon : Classes descriptives */
article.blog-post.featured{
    header.post-header{
        h1.post-title{
            - Titre de l'article
        }
    }
}

/* ❌ Éviter : Classes génériques */
div.box1{
    div.top{
        div.title{
            - Titre
        }
    }
}
```

### 4. Commentaires explicites

```bview
/* about.bview
 * Description : Page À propos de l'entreprise
 * Auteur : C.A.D. BONDJE DOUE
 * Date : 2025-10-16
 */

main.about-page{
    /* Section héro avec image de fond */
    section.hero-section{
        - Contenu
    }
    
    /* Présentation de l'équipe */
    section.team-section{
        - Contenu
    }
}
```

### 5. Séparation des préoccupations

```
✅ Bon : Un fichier .bview par page/composant
Views/
├── home.bview
├── about.bview
├── contact.bview
└── components/
    ├── header.bview
    └── footer.bview

❌ Éviter : Tout dans un seul fichier
Views/
└── all-pages.bview (contient toutes les pages)
```

### 6. Ne jamais garder les deux versions

```
❌ ERREUR : Garder les deux
Views/
├── home.bview   ← Sera utilisé
└── home.phtml   ← Code mort, inutile

✅ CORRECT : Un seul fichier
Views/
└── home.bview   ← Seul fichier
```

---

## Architecture du parser

### Fonctionnement interne

```
Fichier .bview
    ↓
igk/bviewParser::parse()
    ↓
1. Tokenization (analyse lexicale)
   - Identifie les tokens : noms, classes, IDs, etc.
    ↓
2. Parsing (analyse syntaxique)
   - Construit l'arbre syntaxique (AST)
    ↓
3. Validation
   - Vérifie la syntaxe et les règles
    ↓
4. Génération HTML/PHP
   - Transforme l'AST en code exécutable
    ↓
5. Rendu final
   - Exécute et affiche le HTML
```

### Exemple de transformation

**Entrée (.bview) :**
```bview
main.section{
    div.container{
        h1{
            - Titre
        }
    }
}
```

**AST (représentation interne) :**
```
Node: main
  - classes: ["section"]
  - children:
    - Node: div
      - classes: ["container"]
      - children:
        - Node: h1
          - content: "Titre"
```

**Sortie (PHP généré) :**
```php
$main = $t->main()->setClass('section');
$div = $main->div()->setClass('container');
$h1 = $div->h1();
$h1->Content = 'Titre';
```

**Rendu final (HTML) :**
```html
<main class="section">
    <div class="container">
        <h1>Titre</h1>
    </div>
</main>
```

---

## Comparaison .bview vs .phtml

### Exemple : Formulaire de contact

#### Version .phtml

```php
<?php
$form = $t->form();
$form->setAttributes([
    'id' => 'contact-form',
    'class' => 'contact-form',
    'action' => '/contact',
    'method' => 'POST'
]);

$div1 = $form->div()->setClass('form-group');
$label1 = $div1->label();
$label1->setAttributes(['for' => 'name']);
$label1->Content = 'Nom';
$input1 = $div1->input();
$input1->setAttributes([
    'id' => 'name',
    'name' => 'name',
    'type' => 'text',
    'class' => 'form-control',
    'required' => true
]);

$div2 = $form->div()->setClass('form-group');
$label2 = $div2->label();
$label2->setAttributes(['for' => 'email']);
$label2->Content = 'Email';
$input2 = $div2->input();
$input2->setAttributes([
    'id' => 'email',
    'name' => 'email',
    'type' => 'email',
    'class' => 'form-control',
    'required' => true
]);

$button = $form->button();
$button->setAttributes([
    'type' => 'submit',
    'class' => 'btn btn-primary'
]);
$button->Content = 'Envoyer';
?>
```

**Lignes de code : 45**

#### Version .bview

```bview
/* contact-form.bview */
form#contact-form.contact-form(action="/contact" method="POST"){
    div.form-group{
        label(for="name"){
            - Nom
        }
        input.form-control#name@name(type="text" required){
        }
    }
    div.form-group{
        label(for="email"){
            - Email
        }
        input.form-control#email@email(type="email" required){
        }
    }
    button.btn.btn-primary(type="submit"){
        - Envoyer
    }
}
```

**Lignes de code : 20**

### Gains

| Métrique | .phtml | .bview | Gain |
|----------|--------|--------|------|
| Lignes de code | 45 | 20 | **-56%** |
| Caractères | ~1200 | ~500 | **-58%** |
| Lisibilité | Moyenne | Excellente | **++++** |
| Maintenabilité | Difficile | Facile | **++++** |

---

## Ressources

### Liens officiels

- **Dépôt GitHub :** [https://github.com/goukenn/balafon-module-igk-bviewParser.git](https://github.com/goukenn/balafon-module-igk-bviewParser.git)
- **Documentation Balafon :** [https://balafon.igkdev.com](https://balafon.igkdev.com)
- **Framework Balafon :** [https://github.com/goukenn/igkdev-balafon](https://github.com/goukenn/igkdev-balafon)

### Support et communauté

```bash
# Reporter un bug
https://github.com/goukenn/balafon-module-igk-bviewParser/issues

# Afficher les informations système
balafon --info

# Demander de l'aide
balafon --support
```

### Commandes d'aide

```bash
# Aide générale sur les modules
balafon --help module

# Aide spécifique au module bviewParser
balafon --module:help igk/bviewParser

# Liste des commandes disponibles pour bview
balafon --help bview

# Version du module
balafon --module:version igk/bviewParser
```

### Exemple de projet

Le projet **SiteNoteBook** contient un exemple de fichier `.bview` :

```
SiteNoteBook/
└── Views/
    └── extension.bview   ← Exemple fonctionnel
```

---

## Conclusion

Les fichiers `.bview` représentent une évolution moderne dans le développement avec Balafon, offrant une syntaxe épurée, concise et élégante pour les vues HTML.

### Points clés

1. **Installation simple** : Une commande CLI suffit
   ```bash
   balafon --module:install igk/bviewParser
   ```

2. **Priorité automatique** : Les `.bview` sont chargés en priorité sur les `.phtml`

3. **Syntaxe puissante** : Format `nom[.classes][#id][@name][(args)]`

4. **Parsing optimisé** : Le module `igk/bviewParser` gère efficacement la conversion

5. **Compatibilité** : Coexiste pacifiquement avec les fichiers `.phtml`

6. **Productivité** : Jusqu'à 60% de réduction du code

### Quand utiliser

-### Quand utiliser

- ✅ **Utilisez .bview** pour les pages statiques, les templates de présentation et les structures HTML pures
- ✅ **Utilisez .phtml** pour la logique PHP complexe, les requêtes de base de données et les vues dynamiques
- ⚠️ **Ne gardez jamais les deux versions** du même fichier

### Prochaines étapes

1. **Installer le module** : `balafon --module:install igk/bviewParser`
2. **Créer votre premier fichier** : `touch Views/test.bview`
3. **Tester la syntaxe** : Créer une page simple
4. **Migrer progressivement** : Convertir les vues statiques
5. **Profiter de la simplicité** : Développer plus rapidement

### Exemple rapide de démarrage

```bview
/* test.bview - Votre première page .bview */
main.test-page{
    div.container{
        h1{
            - Bienvenue dans le monde .bview !
        }
        p{
            - Si vous voyez cette page, tout fonctionne parfaitement.
        }
        ul{
            li{ - Syntaxe simple et claire }
            li{ - Moins de code à écrire }
            li{ - Meilleure lisibilité }
        }
    }
}
```

Accédez à `http://localhost/votre-projet/test` et admirez le résultat !

---

## Annexes

### A. Référence complète de la syntaxe

#### A.1. Éléments de base

| Syntaxe | Description | Exemple |
|---------|-------------|---------|
| `element` | Balise HTML | `div`, `span`, `main` |
| `element.class` | Avec classe | `div.container` |
| `element#id` | Avec ID | `header#main` |
| `element@name` | Avec attribut name | `input@email` |
| `element(attrs)` | Avec attributs | `a(href="/")` |

#### A.2. Combinaisons

| Syntaxe | Résultat HTML |
|---------|---------------|
| `div` | `<div></div>` |
| `div.box` | `<div class="box"></div>` |
| `div.box.large` | `<div class="box large"></div>` |
| `div#main` | `<div id="main"></div>` |
| `div.box#main` | `<div class="box" id="main"></div>` |
| `input@email` | `<input name="email">` |
| `input@email(type="email")` | `<input name="email" type="email">` |
| `div.box#main@container` | `<div class="box" id="main" name="container"></div>` |

#### A.3. Contenu

| Syntaxe | Description |
|---------|-------------|
| `- texte` | Contenu textuel |
| `- texte <b>gras</b>` | Contenu avec HTML |
| `{ - texte }` | Contenu dans un bloc |

#### A.4. Attributs spéciaux

| Type | Syntaxe | Exemple |
|------|---------|---------|
| Booléens | `(required)` | `input(required)` |
| Data-* | `(data-id="123")` | `div(data-id="123")` |
| Aria-* | `(aria-label="Menu")` | `button(aria-label="Menu")` |
| Multiples | `(a="1" b="2")` | `div(data-x="1" data-y="2")` |

### B. Exemples de cas d'usage courants

#### B.1. Layouts de page

```bview
/* layout.bview - Layout principal */
main.app-layout{
    header.app-header{
        nav.navbar{
            a.logo(href="/"){
                - MonSite
            }
            ul.nav-menu{
                li{ a(href="/"){ - Accueil } }
                li{ a(href="/about"){ - À propos } }
                li{ a(href="/contact"){ - Contact } }
            }
        }
    }
    
    section.app-content{
        /* Le contenu sera inséré ici */
    }
    
    footer.app-footer{
        div.container{
            p{ - © 2025 MonSite. Tous droits réservés. }
        }
    }
}
```

#### B.2. Composants réutilisables

```bview
/* button.bview - Composant bouton */
button.btn.btn-primary(type="button"){
    - Cliquez-moi
}

/* card.bview - Composant carte */
div.card{
    div.card-header{
        h3.card-title{ - Titre }
    }
    div.card-body{
        p{ - Contenu de la carte }
    }
    div.card-footer{
        button.btn{ - Action }
    }
}
```

#### B.3. Formulaires complexes

```bview
/* registration-form.bview */
form.registration-form#reg-form(action="/register" method="POST"){
    fieldset{
        legend{ - Informations personnelles }
        
        div.form-row{
            div.form-group.col-6{
                label(for="firstname"){ - Prénom }
                input.form-control#firstname@firstname(type="text" required){
                }
            }
            div.form-group.col-6{
                label(for="lastname"){ - Nom }
                input.form-control#lastname@lastname(type="text" required){
                }
            }
        }
        
        div.form-group{
            label(for="email"){ - Email }
            input.form-control#email@email(type="email" required){
            }
        }
        
        div.form-group{
            label(for="password"){ - Mot de passe }
            input.form-control#password@password(type="password" required minlength="8"){
            }
        }
    }
    
    fieldset{
        legend{ - Adresse }
        
        div.form-group{
            label(for="address"){ - Rue }
            input.form-control#address@address(type="text"){
            }
        }
        
        div.form-row{
            div.form-group.col-4{
                label(for="zip"){ - Code postal }
                input.form-control#zip@zip(type="text"){
                }
            }
            div.form-group.col-8{
                label(for="city"){ - Ville }
                input.form-control#city@city(type="text"){
                }
            }
        }
    }
    
    div.form-actions{
        button.btn.btn-primary(type="submit"){
            - S'inscrire
        }
        a.btn.btn-secondary(href="/login"){
            - Annuler
        }
    }
}
```

#### B.4. Grilles et listes

```bview
/* product-grid.bview */
section.products-section{
    div.container{
        h2.section-title{
            - Nos produits
        }
        
        div.product-grid{
            div.product-item{
                img.product-image(src="/img/product1.jpg" alt="Produit 1"){
                }
                h3.product-title{ - Produit 1 }
                p.product-price{ - 29,99 € }
                button.btn.add-cart{ - Ajouter }
            }
            
            div.product-item{
                img.product-image(src="/img/product2.jpg" alt="Produit 2"){
                }
                h3.product-title{ - Produit 2 }
                p.product-price{ - 39,99 € }
                button.btn.add-cart{ - Ajouter }
            }
            
            div.product-item{
                img.product-image(src="/img/product3.jpg" alt="Produit 3"){
                }
                h3.product-title{ - Produit 3 }
                p.product-price{ - 49,99 € }
                button.btn.add-cart{ - Ajouter }
            }
        }
    }
}
```

#### B.5. Modales et dialogues

```bview
/* modal.bview */
div.modal#confirm-modal(role="dialog" aria-labelledby="modal-title"){
    div.modal-overlay{
    }
    div.modal-container{
        div.modal-header{
            h2#modal-title.modal-title{
                - Confirmation
            }
            button.modal-close(aria-label="Fermer"){
                - ×
            }
        }
        div.modal-body{
            p{
                - Êtes-vous sûr de vouloir effectuer cette action ?
            }
        }
        div.modal-footer{
            button.btn.btn-secondary(data-action="cancel"){
                - Annuler
            }
            button.btn.btn-danger(data-action="confirm"){
                - Confirmer
            }
        }
    }
}
```

### C. Comparaison avec d'autres syntaxes

#### C.1. .bview vs HTML

**HTML :**
```html
<main class="page">
    <div class="container center">
        <h1 id="title">Mon Titre</h1>
        <form action="/submit" method="POST">
            <input type="text" name="username" required>
        </form>
    </div>
</main>
```

**.bview :**
```bview
main.page{
    div.container.center{
        h1#title{ - Mon Titre }
        form(action="/submit" method="POST"){
            input@username(type="text" required){
            }
        }
    }
}
```

**Gain : -40% de caractères**

#### C.2. .bview vs Pug/Jade

**Pug :**
```pug
main.page
  .container.center
    h1#title Mon Titre
    form(action="/submit" method="POST")
      input(type="text" name="username" required)
```

**.bview :**
```bview
main.page{
    div.container.center{
        h1#title{ - Mon Titre }
        form(action="/submit" method="POST"){
            input@username(type="text" required){
            }
        }
    }
}
```

**Différences :**
- .bview utilise des accolades `{}` au lieu de l'indentation
- .bview utilise `@` pour l'attribut name
- .bview utilise `-` pour le contenu textuel

#### C.3. .bview vs Haml

**Haml :**
```haml
%main.page
  .container.center
    %h1#title Mon Titre
    %form{action: "/submit", method: "POST"}
      %input{type: "text", name: "username", required: true}
```

**.bview :**
```bview
main.page{
    div.container.center{
        h1#title{ - Mon Titre }
        form(action="/submit" method="POST"){
            input@username(type="text" required){
            }
        }
    }
}
```

**Avantages .bview :**
- Syntaxe plus proche du CSS (`.class`, `#id`)
- Accolades explicites (pas de dépendance à l'indentation)
- `@name` plus intuitif pour les formulaires

### D. FAQ (Foire aux questions)

#### Q1 : Puis-je utiliser du PHP dans un fichier .bview ?

**R :** Non, les fichiers `.bview` sont des templates HTML purs sans logique PHP. Pour la logique, utilisez `.phtml`.

#### Q2 : Comment passer des variables à un fichier .bview ?

**R :** Les variables doivent être préparées dans le contrôleur et injectées lors du rendu. Pour des besoins dynamiques complexes, préférez `.phtml`.

#### Q3 : Puis-je imbriquer des fichiers .bview ?

**R :** Cela dépend des fonctionnalités du module `igk/bviewParser`. Consultez la documentation du module pour les directives d'inclusion.

#### Q4 : Les fichiers .bview sont-ils plus performants ?

**R :** Le parsing initial peut avoir un léger overhead, mais si le cache est activé, les performances sont comparables voire meilleures.

#### Q5 : Puis-je utiliser des frameworks CSS avec .bview ?

**R :** Oui, totalement ! Bootstrap, Tailwind, Bulma, etc. fonctionnent parfaitement :

```bview
/* Avec Bootstrap */
div.container{
    div.row{
        div.col-md-6{
            - Colonne 1
        }
        div.col-md-6{
            - Colonne 2
        }
    }
}

/* Avec Tailwind */
div.flex.items-center.justify-center{
    button.px-4.py-2.bg-blue-500.text-white{
        - Cliquez
    }
}
```

#### Q6 : Comment gérer les conditions (if/else) ?

**R :** Les fichiers `.bview` ne supportent pas la logique conditionnelle. Utilisez `.phtml` pour ces cas :

```php
<?php
// mypage.phtml
if ($user->isLoggedIn()) {
    include 'Views/dashboard.bview';
} else {
    include 'Views/login.bview';
}
?>
```

#### Q7 : Puis-je générer des listes dynamiques avec .bview ?

**R :** Non directement. Utilisez `.phtml` pour les boucles :

```php
<?php
// products.phtml
foreach ($products as $product) {
    // Rendre un template pour chaque produit
}
?>
```

#### Q8 : Les fichiers .bview supportent-ils les commentaires conditionnels IE ?

**R :** Consultez la documentation du module `igk/bviewParser` pour les fonctionnalités spécifiques.

#### Q9 : Comment déboguer un fichier .bview qui ne s'affiche pas ?

**R :**
```bash
# 1. Vérifier que le module est chargé
balafon --module:status igk/bviewParser

# 2. Activer le mode debug
export IGK_BVIEW_DEBUG=1

# 3. Consulter les logs
cat Data/logs/bviewParser.log

# 4. Valider la syntaxe
balafon --bview:validate Views/myfile.bview
```

#### Q10 : Puis-je utiliser .bview avec un système de composants ?

**R :** Oui, si le module supporte les includes. Créez des composants réutilisables :

```
Views/
├── components/
│   ├── header.bview
│   ├── footer.bview
│   └── sidebar.bview
└── pages/
    └── home.bview
```

### E. Glossaire

| Terme | Définition |
|-------|------------|
| **AST** | Abstract Syntax Tree - Arbre de syntaxe abstraite généré lors du parsing |
| **bview** | Balafon View - Format de fichier de vue spécifique à Balafon |
| **PageLayout** | Composant du contrôleur qui gère le chargement des vues |
| **Parser** | Analyseur syntaxique qui transforme `.bview` en HTML/PHP |
| **Token** | Unité lexicale identifiée lors de l'analyse (classe, ID, attribut, etc.) |
| **Tokenization** | Processus d'analyse lexicale qui découpe le code en tokens |
| **Module** | Extension du framework Balafon (ex: `igk/bviewParser`) |
| **CLI** | Command Line Interface - Interface en ligne de commande |
| **Balafon** | Framework PHP pour le développement d'applications web |

### F. Changelog du module bviewParser

#### Version 1.0.0 (Hypothétique)
- ✅ Support de base de la syntaxe `.bview`
- ✅ Parsing des classes, IDs, names et attributs
- ✅ Génération HTML
- ✅ Intégration avec le PageLayout

#### Version 1.1.0 (Hypothétique)
- ✅ Support du cache
- ✅ Mode debug amélioré
- ✅ Messages d'erreur plus clairs
- ✅ Performance optimisée

#### Version 1.2.0 (Hypothétique)
- ✅ Support des includes
- ✅ Validation de syntaxe en ligne de commande
- ✅ Conversion automatique `.phtml` → `.bview`
- ✅ Support des composants

**Note :** Consultez le dépôt GitHub pour les versions réelles et les notes de version officielles.

### G. Contribution au projet

Le module `igk/bviewParser` est open source. Vous pouvez contribuer :

#### Signaler un bug

1. Allez sur [https://github.com/goukenn/balafon-module-igk-bviewParser/issues](https://github.com/goukenn/balafon-module-igk-bviewParser/issues)
2. Cliquez sur "New Issue"
3. Décrivez le bug avec :
   - Version du module
   - Version de Balafon
   - Fichier `.bview` problématique
   - Message d'erreur
   - Comportement attendu vs observé

#### Proposer une amélioration

1. Fork le dépôt
2. Créez une branche pour votre fonctionnalité
3. Développez et testez
4. Soumettez une Pull Request

#### Écrire de la documentation

La documentation est toujours améliorable ! N'hésitez pas à proposer :
- Des exemples supplémentaires
- Des tutoriels
- Des traductions
- Des corrections

### H. Exemples avancés du projet SiteNoteBook

#### H.1. Adaptation pour le projet SiteNoteBook

```bview
/* sites-list.bview - Liste des sites du notebook */
main.sites-page{
    div.container{
        header.page-header{
            h1.page-title{
                - Notebook de sites
            }
            div.actions{
                a.btn.btn-primary(href="/notebook/sites/add"){
                    - Ajouter un site
                }
                a.btn.btn-secondary(href="/notebook/export"){
                    - Exporter
                }
            }
        }
        
        section.search-section{
            form.search-form(action="/notebook/sites" method="GET"){
                div.form-group{
                    input.form-control.search-input@q(
                        type="text" 
                        placeholder="site, téléphone, TVA ou nom"
                        maxlength="230"
                    ){
                    }
                    button.btn.btn-search(type="submit"){
                        - Rechercher
                    }
                }
            }
        }
        
        section.results-section{
            table.sites-table.table-striped{
                thead{
                    tr{
                        th{ - Titre }
                        th{ - Site }
                        th{ - TVA }
                        th{ - Actions }
                    }
                }
                tbody{
                    /* Les résultats seraient injectés dynamiquement */
                    tr.no-results{
                        td(colspan="4"){
                            - Aucun résultat trouvé
                        }
                    }
                }
            }
        }
    }
}
```

#### H.2. Page de catégories

```bview
/* categories.bview - Gestion des catégories */
main.categories-page{
    div.container{
        h1{
            - Catégories de sites
        }
        
        div.categories-grid{
            div.category-card(data-category="entertaiments"){
                div.category-icon{
                    - 🎬
                }
                h3.category-name{
                    - Divertissement
                }
                p.category-count{
                    - 12 sites
                }
            }
            
            div.category-card(data-category="news"){
                div.category-icon{
                    - 📰
                }
                h3.category-name{
                    - Actualités
                }
                p.category-count{
                    - 8 sites
                }
            }
            
            div.category-card(data-category="shopping"){
                div.category-icon{
                    - 🛒
                }
                h3.category-name{
                    - Shopping
                }
                p.category-count{
                    - 15 sites
                }
            }
            
            div.category-card(data-category="storage"){
                div.category-icon{
                    - 💾
                }
                h3.category-name{
                    - Stockage
                }
                p.category-count{
                    - 5 sites
                }
            }
        }
    }
}
```

#### H.3. Dashboard utilisateur

```bview
/* dashboard.bview - Tableau de bord */
main.dashboard-page{
    div.container{
        header.dashboard-header{
            h1{
                - Tableau de bord
            }
            p.welcome{
                - Bienvenue dans votre notebook de sites
            }
        }
        
        section.stats-section{
            div.stats-grid{
                div.stat-card.stat-primary{
                    div.stat-icon{
                        - 🌐
                    }
                    div.stat-info{
                        h3.stat-value{
                            - 42
                        }
                        p.stat-label{
                            - Sites enregistrés
                        }
                    }
                }
                
                div.stat-card.stat-success{
                    div.stat-icon{
                        - 📁
                    }
                    div.stat-info{
                        h3.stat-value{
                            - 8
                        }
                        p.stat-label{
                            - Catégories
                        }
                    }
                }
                
                div.stat-card.stat-info{
                    div.stat-icon{
                        - ✅
                    }
                    div.stat-info{
                        h3.stat-value{
                            - 38
                        }
                        p.stat-label{
                            - Sites actifs
                        }
                    }
                }
                
                div.stat-card.stat-warning{
                    div.stat-icon{
                        - ⏸️
                    }
                    div.stat-info{
                        h3.stat-value{
                            - 4
                        }
                        p.stat-label{
                            - Sites désactivés
                        }
                    }
                }
            }
        }
        
        section.recent-section{
            h2.section-title{
                - Derniers sites ajoutés
            }
            div.recent-list{
                /* Liste dynamique à implémenter en .phtml */
            }
        }
    }
}
```

#### H.4. Page d'authentification

```bview
/* auth.bview - Page de connexion/inscription */
main.auth-page{
    div.auth-container{
        div.auth-box{
            div.auth-header{
                img.auth-logo(src="/img/logo.svg" alt="Logo"){
                }
                h1.auth-title{
                    - Site Notebook
                }
                p.auth-subtitle{
                    - Gérez vos sites web préférés
                }
            }
            
            div.auth-tabs{
                button.tab-btn.active(data-tab="login"){
                    - Connexion
                }
                button.tab-btn(data-tab="register"){
                    - Inscription
                }
            }
            
            div.tab-content.active#login-tab{
                form.auth-form(action="/auth/login" method="POST"){
                    div.form-group{
                        label(for="login-email"){
                            - Email
                        }
                        input.form-control#login-email@email(
                            type="email" 
                            required 
                            placeholder="votre@email.com"
                        ){
                        }
                    }
                    
                    div.form-group{
                        label(for="login-password"){
                            - Mot de passe
                        }
                        input.form-control#login-password@password(
                            type="password" 
                            required 
                            placeholder="••••••••"
                        ){
                        }
                    }
                    
                    div.form-options{
                        label.checkbox{
                            input(type="checkbox"){
                            }
                            - Se souvenir de moi
                        }
                        a.forgot-link(href="/auth/forgot"){
                            - Mot de passe oublié ?
                        }
                    }
                    
                    button.btn.btn-primary.btn-block(type="submit"){
                        - Se connecter
                    }
                }
            }
            
            div.tab-content#register-tab{
                form.auth-form(action="/auth/register" method="POST"){
                    div.form-group{
                        label(for="register-name"){
                            - Nom complet
                        }
                        input.form-control#register-name@name(
                            type="text" 
                            required 
                            placeholder="Jean Dupont"
                        ){
                        }
                    }
                    
                    div.form-group{
                        label(for="register-email"){
                            - Email
                        }
                        input.form-control#register-email@email(
                            type="email" 
                            required 
                            placeholder="votre@email.com"
                        ){
                        }
                    }
                    
                    div.form-group{
                        label(for="register-password"){
                            - Mot de passe
                        }
                        input.form-control#register-password@password(
                            type="password" 
                            required 
                            minlength="8"
                            placeholder="••••••••"
                        ){
                        }
                    }
                    
                    div.form-group{
                        label.checkbox{
                            input(type="checkbox" required){
                            }
                            - J'accepte les conditions d'utilisation
                        }
                    }
                    
                    button.btn.btn-primary.btn-block(type="submit"){
                        - S'inscrire
                    }
                }
            }
        }
    }
}
```

### I. Intégration avec les styles Balafon

#### I.1. Utilisation avec default.pcss

Les fichiers `.bview` fonctionnent parfaitement avec les styles définis dans `Styles/default.pcss` :

```bview
/* utilisant les classes du projet SiteNoteBook */
main.page{
    header.fith{
        /* Utilise la classe 'fith' définie dans default.pcss */
        - En-tête
    }
    
    section.fith.page{
        div.igk-col-sm-3-3.fitw{
            /* Classes Balafon du système de grille */
            - Contenu
        }
    }
}
```

#### I.2. Support des thèmes

```bview
/* Compatible avec light.theme.pcss et dark.theme.pcss */
div.container(data-theme="dark"){
    header{
        /* Le thème dark sera appliqué automatiquement */
        h1{
            - Mon titre
        }
    }
}
```

### J. Cas d'usage spécifiques au projet

#### J.1. Import/Export de sites

```bview
/* import-export.bview */
main.import-export-page{
    div.container{
        h1{
            - Import / Export de sites
        }
        
        div.action-grid{
            section.import-section{
                h2{
                    - Importer des sites
                }
                form.import-form(action="/notebook/import" method="POST" enctype="multipart/form-data"){
                    div.form-group{
                        label{
                            - Sélectionner un fichier JSON
                        }
                        input.form-control@json(type="file" accept="application/json"){
                        }
                    }
                    button.btn.btn-primary(type="submit"){
                        - Importer
                    }
                }
            }
            
            section.export-section{
                h2{
                    - Exporter des sites
                }
                p{
                    - Téléchargez tous vos sites au format JSON
                }
                a.btn.btn-success(href="/notebook/export"){
                    - Télécharger l'export
                }
            }
        }
    }
}
```

#### J.2. Profils et autorisations

```bview
/* profiles.bview - Gestion des profils */
main.profiles-page{
    div.container{
        h1{
            - Gestion des profils
        }
        
        div.profiles-list{
            div.profile-card{
                div.profile-header{
                    h3{
                        - Utilisateur
                    }
                    span.profile-badge.badge-user{
                        - User
                    }
                }
                div.profile-permissions{
                    h4{
                        - Permissions
                    }
                    ul.permissions-list{
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Voir
                        }
                    }
                }
            }
            
            div.profile-card{
                div.profile-header{
                    h3{
                        - Administrateur
                    }
                    span.profile-badge.badge-admin{
                        - Admin
                    }
                }
                div.profile-permissions{
                    h4{
                        - Permissions
                    }
                    ul.permissions-list{
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Voir
                        }
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Éditer
                        }
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Supprimer
                        }
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Tableau de bord
                        }
                        li.permission-item{
                            span.permission-icon{
                                - ✓
                            }
                            - Exporter tout
                        }
                    }
                }
            }
        }
    }
}
```

---

## Licence

Ce document est fourni à titre informatif. Le module `igk/bviewParser` et le framework Balafon ont leurs propres licences respectives. Consultez les dépôts officiels pour plus d'informations.

---

## Crédits

- **Auteur du framework Balafon :** C.A.D. BONDJE DOUE
- **Module igk/bviewParser :** [Voir le dépôt GitHub](https://github.com/goukenn/balafon-module-igk-bviewParser)
- **Documentation :** Basée sur le projet SiteNoteBook et les spécifications Balafon
- **Contributeurs :** Communauté Balafon

---

## Contact et support

- **Issues GitHub :** [https://github.com/goukenn/balafon-module-igk-bviewParser/issues](https://github.com/goukenn/balafon-module-igk-bviewParser/issues)
- **Documentation Balafon :** [https://balafon.igkdev.com](https://balafon.igkdev.com)
- **Email :** bondje.doue@gmail.com (pour les questions générales sur Balafon)
- **Forum communautaire :** Consultez le site officiel pour les liens vers les forums

---

## Remerciements

Merci à tous les contributeurs et utilisateurs du framework Balafon qui ont participé à l'amélioration continue de cet outil. Un remerciement spécial à C.A.D. BONDJE DOUE pour la création et la maintenance du framework Balafon.

---

## Annexe K : Patterns de développement recommandés

### K.1. Organisation des fichiers de vue

```
Views/
├── layouts/              # Layouts réutilisables
│   ├── main.bview
│   ├── admin.bview
│   └── auth.bview
├── pages/               # Pages principales
│   ├── home.bview
│   ├── about.bview
│   └── contact.bview
├── components/          # Composants réutilisables
│   ├── header.bview
│   ├── footer.bview
│   ├── navigation.bview
│   └── sidebar.bview
├── partials/            # Fragments de vue
│   ├── breadcrumb.bview
│   ├── pagination.bview
│   └── alert.bview
└── forms/              # Formulaires complexes
    ├── login-form.bview
    ├── register-form.bview
    └── contact-form.bview
```

### K.2. Convention de nommage

#### Fichiers
- **Pages** : `nom-page.bview` (exemple: `about-us.bview`)
- **Composants** : `nom-composant.bview` (exemple: `user-card.bview`)
- **Layouts** : `nom-layout.bview` (exemple: `main-layout.bview`)
- **Formulaires** : `nom-form.bview` (exemple: `login-form.bview`)

#### Classes CSS
```bview
/* Suivre la convention BEM ou similaire */
div.block{
    div.block__element{
        div.block__element--modifier{
            - Contenu
        }
    }
}

/* Exemple concret */
div.card{
    div.card__header{
        h3.card__title.card__title--large{
            - Titre
        }
    }
    div.card__body{
        p.card__text{
            - Contenu
        }
    }
}
```

### K.3. Pattern de composant réutilisable

```bview
/* components/button.bview */
/* Usage: Bouton standard réutilisable */
button.btn.btn-component(type="button" data-component="true"){
    span.btn-icon{
        /* Icône optionnelle */
    }
    span.btn-text{
        - Texte du bouton
    }
}
```

### K.4. Pattern de carte de contenu

```bview
/* components/content-card.bview */
article.content-card{
    header.content-card__header{
        img.content-card__image(src="" alt=""){
        }
        div.content-card__meta{
            span.content-card__date{
                - Date
            }
            span.content-card__category{
                - Catégorie
            }
        }
    }
    
    div.content-card__body{
        h3.content-card__title{
            - Titre
        }
        p.content-card__excerpt{
            - Extrait du contenu...
        }
    }
    
    footer.content-card__footer{
        a.content-card__link(href="#"){
            - Lire la suite
        }
        div.content-card__actions{
            button.btn-icon(aria-label="Partager"){
                - 🔗
            }
            button.btn-icon(aria-label="Favoris"){
                - ⭐
            }
        }
    }
}
```

### K.5. Pattern de navigation responsive

```bview
/* components/navigation.bview */
nav.main-navigation(role="navigation"){
    div.nav-container{
        div.nav-brand{
            a.nav-logo(href="/"){
                img(src="/img/logo.svg" alt="Logo"){
                }
            }
        }
        
        button.nav-toggle(
            aria-label="Menu" 
            aria-expanded="false" 
            data-toggle="navigation"
        ){
            span.nav-toggle__bar{
            }
            span.nav-toggle__bar{
            }
            span.nav-toggle__bar{
            }
        }
        
        div.nav-menu#main-menu{
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
                li.nav-item.nav-item--dropdown{
                    button.nav-link(aria-haspopup="true"){
                        - Produits
                        span.nav-arrow{
                            - ▼
                        }
                    }
                    ul.nav-dropdown{
                        li{
                            a.nav-dropdown-link(href="/products/1"){
                                - Produit 1
                            }
                        }
                        li{
                            a.nav-dropdown-link(href="/products/2"){
                                - Produit 2
                            }
                        }
                    }
                }
                li.nav-item{
                    a.nav-link(href="/contact"){
                        - Contact
                    }
                }
            }
            
            div.nav-actions{
                a.btn.btn-outline(href="/login"){
                    - Connexion
                }
                a.btn.btn-primary(href="/register"){
                    - S'inscrire
                }
            }
        }
    }
}
```

### K.6. Pattern de formulaire avec validation visuelle

```bview
/* forms/validated-form.bview */
form.validated-form(novalidate data-validate="true"){
    div.form-group{
        label.form-label(for="email"){
            - Email
            span.required{
                - *
            }
        }
        div.input-wrapper{
            input.form-control#email@email(
                type="email" 
                required 
                placeholder="exemple@email.com"
                aria-describedby="email-error"
            ){
            }
            span.input-icon.input-icon--success{
                - ✓
            }
            span.input-icon.input-icon--error{
                - ✗
            }
        }
        p.form-help{
            - Entrez une adresse email valide
        }
        p.form-error#email-error(role="alert"){
            - Veuillez entrer une adresse email valide
        }
    }
    
    div.form-group{
        label.form-label(for="password"){
            - Mot de passe
            span.required{
                - *
            }
        }
        div.input-wrapper{
            input.form-control#password@password(
                type="password" 
                required 
                minlength="8"
                aria-describedby="password-help password-error"
            ){
            }
            button.input-toggle(type="button" aria-label="Afficher le mot de passe"){
                - 👁️
            }
        }
        p.form-help#password-help{
            - Minimum 8 caractères
        }
        div.password-strength{
            div.strength-bar{
                div.strength-bar__fill(data-strength="0"){
                }
            }
            p.strength-text{
                - Faible
            }
        }
        p.form-error#password-error(role="alert"){
            - Le mot de passe doit contenir au moins 8 caractères
        }
    }
    
    div.form-actions{
        button.btn.btn-primary(type="submit"){
            - Soumettre
        }
        button.btn.btn-secondary(type="reset"){
            - Réinitialiser
        }
    }
}
```

### K.7. Pattern de grille responsive

```bview
/* layouts/responsive-grid.bview */
section.grid-section{
    div.container{
        h2.section-title{
            - Nos services
        }
        
        div.grid.grid-responsive{
            /* Grid: 1 col sur mobile, 2 sur tablette, 4 sur desktop */
            div.grid-item{
                div.service-card{
                    div.service-icon{
                        - 🎨
                    }
                    h3.service-title{
                        - Design
                    }
                    p.service-description{
                        - Création de designs modernes et attractifs
                    }
                }
            }
            
            div.grid-item{
                div.service-card{
                    div.service-icon{
                        - 💻
                    }
                    h3.service-title{
                        - Développement
                    }
                    p.service-description{
                        - Développement d'applications web robustes
                    }
                }
            }
            
            div.grid-item{
                div.service-card{
                    div.service-icon{
                        - 📱
                    }
                    h3.service-title{
                        - Mobile
                    }
                    p.service-description{
                        - Applications mobiles natives et hybrides
                    }
                }
            }
            
            div.grid-item{
                div.service-card{
                    div.service-icon{
                        - 🚀
                    }
                    h3.service-title{
                        - Marketing
                    }
                    p.service-description{
                        - Stratégies marketing digitales efficaces
                    }
                }
            }
        }
    }
}
```

### K.8. Pattern d'accessibilité

```bview
/* accessible-content.bview */
main.accessible-content(role="main"){
    /* Skip link pour l'accessibilité */
    a.skip-link(href="#main-content"){
        - Aller au contenu principal
    }
    
    nav(aria-label="Fil d'Ariane"){
        ol.breadcrumb{
            li.breadcrumb-item{
                a(href="/"){
                    - Accueil
                }
            }
            li.breadcrumb-item{
                a(href="/blog"){
                    - Blog
                }
            }
            li.breadcrumb-item(aria-current="page"){
                - Article actuel
            }
        }
    }
    
    article#main-content.article{
        header.article-header{
            h1.article-title{
                - Titre de l'article
            }
            div.article-meta{
                time.article-date(datetime="2025-10-16"){
                    - 16 octobre 2025
                }
                address.article-author{
                    - Par 
                    a(href="/author/john" rel="author"){
                        - John Doe
                    }
                }
            }
        }
        
        div.article-content{
            /* Contenu avec structure sémantique */
            section(aria-labelledby="section1-title"){
                h2#section1-title{
                    - Section 1
                }
                p{
                    - Contenu de la section...
                }
            }
            
            section(aria-labelledby="section2-title"){
                h2#section2-title{
                    - Section 2
                }
                p{
                    - Contenu de la section...
                }
                
                figure{
                    img(src="/img/diagram.png" alt="Diagramme montrant le processus en 3 étapes"){
                    }
                    figcaption{
                        - Figure 1: Processus en trois étapes
                    }
                }
            }
        }
        
        aside.article-aside(aria-label="Informations complémentaires"){
            h3{
                - Articles liés
            }
            ul{
                li{
                    a(href="/article-1"){
                        - Article lié 1
                    }
                }
                li{
                    a(href="/article-2"){
                        - Article lié 2
                    }
                }
            }
        }
    }
}
```

### K.9. Pattern de chargement et états

```bview
/* states-loading.bview */
div.content-container(data-state="loading"){
    /* État de chargement */
    div.loading-state{
        div.spinner{
            div.spinner-circle{
            }
        }
        p.loading-text{
            - Chargement en cours...
        }
    }
    
    /* État vide */
    div.empty-state{
        div.empty-icon{
            - 📭
        }
        h3.empty-title{
            - Aucun contenu disponible
        }
        p.empty-description{
            - Il n'y a pas encore de contenu à afficher
        }
        button.btn.btn-primary{
            - Ajouter du contenu
        }
    }
    
    /* État d'erreur */
    div.error-state{
        div.error-icon{
            - ⚠️
        }
        h3.error-title{
            - Une erreur est survenue
        }
        p.error-description{
            - Impossible de charger le contenu
        }
        button.btn.btn-secondary{
            - Réessayer
        }
    }
    
    /* État avec contenu */
    div.content-state{
        /* Le contenu réel sera ici */
    }
}
```

### K.10. Pattern de notifications/toasts

```bview
/* notifications.bview */
div.notifications-container(aria-live="polite" aria-atomic="true"){
    div.notification.notification-success(role="alert"){
        div.notification-icon{
            - ✓
        }
        div.notification-content{
            h4.notification-title{
                - Succès
            }
            p.notification-message{
                - L'opération a été effectuée avec succès
            }
        }
        button.notification-close(aria-label="Fermer"){
            - ×
        }
    }
    
    div.notification.notification-error(role="alert"){
        div.notification-icon{
            - ✗
        }
        div.notification-content{
            h4.notification-title{
                - Erreur
            }
            p.notification-message{
                - Une erreur est survenue
            }
        }
        button.notification-close(aria-label="Fermer"){
            - ×
        }
    }
    
    div.notification.notification-warning(role="alert"){
        div.notification-icon{
            - ⚠
        }
        div.notification-content{
            h4.notification-title{
                - Attention
            }
            p.notification-message{
                - Veuillez vérifier vos informations
            }
        }
        button.notification-close(aria-label="Fermer"){
            - ×
        }
    }
    
    div.notification.notification-info(role="status"){
        div.notification-icon{
            - ℹ
        }
        div.notification-content{
            h4.notification-title{
                - Information
            }
            p.notification-message{
                - Nouvelles fonctionnalités disponibles
            }
        }
        button.notification-close(aria-label="Fermer"){
            - ×
        }
    }
}
```

---

## Annexe L : Snippets et raccourcis

### L.1. Snippets VS Code recommandés

Créez un fichier `.vscode/bview.code-snippets` :

```json
{
  "BView Container": {
    "prefix": "bv-container",
    "body": [
      "div.container{",
      "\t$0",
      "}"
    ],
    "description": "Container div"
  },
  "BView Form Group": {
    "prefix": "bv-form-group",
    "body": [
      "div.form-group{",
      "\tlabel(for=\"${1:id}\"){",
      "\t\t- ${2:Label}",
      "\t}",
      "\tinput.form-control#${1:id}@${3:name}(type=\"${4:text}\" ${5:required}){",
      "\t}",
      "}"
    ],
    "description": "Form group with label and input"
  },
  "BView Card": {
    "prefix": "bv-card",
    "body": [
      "div.card{",
      "\tdiv.card-header{",
      "\t\th3.card-title{",
      "\t\t\t- ${1:Title}",
      "\t\t}",
      "\t}",
      "\tdiv.card-body{",
      "\t\t$0",
      "\t}",
      "\tdiv.card-footer{",
      "\t\t",
      "\t}",
      "}"
    ],
    "description": "Card component"
  },
  "BView Button": {
    "prefix": "bv-btn",
    "body": [
      "button.btn.btn-${1:primary}(type=\"${2:button}\"){",
      "\t- ${3:Text}",
      "}"
    ],
    "description": "Button element"
  },
  "BView Navigation": {
    "prefix": "bv-nav",
    "body": [
      "nav.main-nav{",
      "\tul.nav-menu{",
      "\t\tli.nav-item{",
      "\t\t\ta.nav-link(href=\"${1:/}\"){",
      "\t\t\t\t- ${2:Home}",
      "\t\t\t}",
      "\t\t}",
      "\t\t$0",
      "\t}",
      "}"
    ],
    "description": "Navigation menu"
  }
}
```

### L.2. Emmet pour .bview

Bien que les fichiers `.bview` aient leur propre syntaxe, vous pouvez créer des alias similaires à Emmet :

```
div.container.center → div.container.center{ }
ul>li*3 → Créer 3 éléments li dans un ul
```

---

## Annexe M : Checklist de mise en production

### M.1. Avant la mise en production

- [ ] **Module installé** : `balafon --module:status igk/bviewParser`
- [ ] **Cache activé** : Vérifier `bviewParser.cache_enabled = true`
- [ ] **Debug désactivé** : `IGK_BVIEW_DEBUG = false`
- [ ] **Fichiers validés** : Exécuter `balafon --bview:validate Views/**/*.bview`
- [ ] **Pas de fichiers dupliqués** : Vérifier qu'il n'existe pas de .bview ET .phtml pour la même vue
- [ ] **Tests effectués** : Toutes les pages .bview sont testées
- [ ] **Performance** : Compiler les vues : `balafon --bview:compile Views/`
- [ ] **Minification** : Activer si nécessaire : `bviewParser.minify = true`
- [ ] **Logs vérifiés** : Pas d'erreurs dans `Data/logs/bviewParser.log`
- [ ] **Documentation** : README.md mis à jour avec les informations sur les vues

### M.2. Optimisations recommandées

```php
<?php
// config.production.php
return [
    '.igk.bviewparser' => [ 
        'debug' => false,
        'info' => '1.0'
    ]
];
```

### M.3. Monitoring

```bash
# Surveiller les erreurs de parsing
tail -f Data/logs/bviewParser.log

# Vérifier la taille du cache
du -sh Data/cache/bview/

# Nettoyer le cache régulièrement
balafon --cache:clear bview
```

---

**Dernière mise à jour :** 16 octobre 2025  
**Version du document :** 1.0  
**Langage :** Français

---

*Cette documentation complète a été générée pour aider les développeurs à comprendre et utiliser efficacement les fichiers `.bview` dans leurs projets Balafon. Pour toute question, suggestion d'amélioration, ou contribution, n'hésitez pas à participer sur le dépôt GitHub officiel.*

**Bon développement avec Balafon et les fichiers .bview ! 🚀**


- Mis à jour 


# Documentation Balafon - Fichiers .bview - Syntaxe Complète et Officielle

## Table des matières

- [Introduction](#introduction)
- [Installation du module bviewParser](#installation-du-module-bviewparser)
- [Priorité de traitement](#priorité-de-traitement)
- [Syntaxe complète des nœuds](#syntaxe-complète-des-nœuds)
- [Arguments `()` vs Attributs `[]`](#arguments--vs-attributs-)
- [Arguments JSON et tableaux](#arguments-json-et-tableaux)
- [Syntaxe compacte sur une ligne](#syntaxe-compacte-sur-une-ligne)
- [Variables et contexte](#variables-et-contexte)
- [Fonctions de rendu personnalisées](#fonctions-de-rendu-personnalisées)
- [Comportement réel du parser](#comportement-réel-du-parser)
- [Opérateur de hiérarchie `>`](#opérateur-de-hiérarchie-)
- [Exemples pratiques](#exemples-pratiques)
- [Configuration](#configuration)
- [Commandes CLI](#commandes-cli)
- [Débogage](#débogage)
- [Migration](#migration)
- [Patterns de développement](#patterns-de-développement)
- [Ressources](#ressources)

---

## Introduction

Les fichiers `.bview` (Balafon View) sont des fichiers de vue spéciaux dans le framework Balafon qui utilisent une syntaxe simplifiée et structurée pour définir l'interface utilisateur.

### Avantages des fichiers .bview

- ✅ **Syntaxe concise** : Moins de code à écrire
- ✅ **Syntaxe sur une ligne** : Pour les éléments simples
- ✅ **Variables de contexte** : Accès aux données et au contrôleur
- ✅ **Évaluation dynamique** : Support de `{{ $variable }}`
- ✅ **Lisibilité améliorée** : Structure claire et hiérarchique
- ✅ **Priorité automatique** : Chargés en priorité sur les `.phtml`
- ✅ **Parsing optimisé** : Traitement efficace par le module dédié
- ✅ **Support SVG natif** : Gestion intelligente des éléments SVG
- ✅ **Fonctions personnalisées** : Création de composants réutilisables
- ✅ **Arguments JSON** : Support natif des structures de données complexes
- ✅ **Facile à apprendre** : Proche de la syntaxe HTML et CSS

---

## Variables et contexte

### Contexte d'exécution

Lors du traitement d'un fichier `.bview`, le parser fournit un **contexte d'exécution** qui contient :

1. **`$raw`** : Les données passées à la vue
2. **`$ctrl`** : Le contrôleur qui initie la transformation

### Syntaxe d'interpolation : `{{ }}`

Pour insérer des variables dans le contenu, utilisez la syntaxe `{{ $variable }}`.

```bview
div{ - Texte avec {{ $variable }} interpolée }
```

### Accès aux variables du contexte

#### 1. Via `$raw` (données brutes)

`$raw` est un objet/tableau contenant toutes les données passées à la vue.

```bview
/* Accès à une propriété de $raw */
div{ - Valeur : {{ $raw->x }} }

/* Accès à un élément de tableau */
div{ - Valeur : {{ $raw['key'] }} }
```

#### 2. Via variable directe (raccourci)

Les variables du contexte sont automatiquement disponibles directement :

```bview
/* Équivalent à $raw->x */
div{ - Valeur : {{ $x }} }

/* Équivalent à $raw->name */
h1{ - Bonjour {{ $name }} }
```

**Règle :** Si `x` fait partie des données passées dans le contexte, alors `{{ $x }}` est identique à `{{ $raw->x }}`.

#### 3. Via `$ctrl` (contrôleur)

```bview
/* Accéder au nom du contrôleur */
div{ - Controller : {{ $ctrl->getName() }} }

/* Accéder à une méthode du contrôleur */
div{ - Base URL : {{ $ctrl->getBaseUrl() }} }
```

### Exemples d'utilisation des variables

#### Exemple 1 : Variables simples

**Données passées au contexte :**
```php
<?php
$data = [
    'x' => 42,
    'name' => 'Jean Dupont',
    'email' => 'jean@example.com'
];
```

**Code .bview :**
```bview
div.user-info{
    /* Accès via $raw */
    p{ - ID : {{ $raw->x }} }
    
    /* Accès direct (raccourci) */
    p{ - Nom : {{ $name }} }
    p{ - Email : {{ $email }} }
}
```

**Output HTML :**
```html
<div class="user-info">
    <p>ID : 42</p>
    <p>Nom : Jean Dupont</p>
    <p>Email : jean@example.com</p>
</div>
```

#### Exemple 2 : Objet complexe

**Données passées au contexte :**
```php
<?php
$data = (object)[
    'user' => (object)[
        'id' => 123,
        'name' => 'Marie Martin',
        'role' => 'Admin'
    ],
    'stats' => (object)[
        'posts' => 45,
        'comments' => 189
    ]
];
```

**Code .bview :**
```bview
div.profile{
    /* Accès aux propriétés imbriquées */
    h1{ - {{ $user->name }} }
    p{ - Rôle : {{ $user->role }} }
    
    div.stats{
        span{ - Posts : {{ $stats->posts }} }
        span{ - Commentaires : {{ $stats->comments }} }
    }
}
```

**Output HTML :**
```html
<div class="profile">
    <h1>Marie Martin</h1>
    <p>Rôle : Admin</p>
    
    <div class="stats">
        <span>Posts : 45</span>
        <span>Commentaires : 189</span>
    </div>
</div>
```

#### Exemple 3 : Tableaux

**Données passées au contexte :**
```php
<?php
$data = [
    'items' => ['Pomme', 'Banane', 'Orange'],
    'count' => 3
];
```

**Code .bview :**
```bview
div.list{
    p{ - Nombre d'items : {{ $count }} }
    
    /* Note : Les boucles ne sont pas directement supportées dans .bview */
    /* Utilisez .phtml pour les structures dynamiques complexes */
}
```

#### Exemple 4 : Utilisation du contrôleur

**Code .bview :**
```bview
div.app-info{
    /* Nom du contrôleur */
    p{ - Controller : {{ $ctrl->getName() }} }
    
    /* URL de base */
    p{ - Base URL : {{ $ctrl->getBaseUrl() }} }
    
    /* Titre de l'application */
    p{ - App : {{ $ctrl->getTitle() }} }
}
```

**Output HTML (exemple) :**
```html
<div class="app-info">
    <p>Controller : SiteNoteBookController</p>
    <p>Base URL : /notebook</p>
    <p>App : Site's Notebook</p>
</div>
```

### Syntaxe complète d'interpolation

#### Variables simples

```bview
/* Variable scalaire */
div{ - {{ $name }} }

/* Variable numérique */
div{ - Prix : {{ $price }} € }

/* Variable booléenne */
div{ - Actif : {{ $active }} }
```

#### Propriétés d'objets

```bview
/* Notation objet */
div{ - {{ $user->name }} }

/* Propriétés imbriquées */
div{ - {{ $user->address->city }} }

/* Méthodes d'objets */
div{ - {{ $user->getFullName() }} }
```

#### Éléments de tableaux

```bview
/* Tableau associatif */
div{ - {{ $data['key'] }} }

/* Tableau indexé */
div{ - {{ $items[0] }} }
```

#### Expressions complexes

```bview
/* Concaténation */
div{ - Bienvenue {{ $title }} {{ $name }} }

/* Avec texte autour */
div{ - Le prix est {{ $price }} € TTC }

/* Multiples variables */
div{ - {{ $firstName }} {{ $lastName }} ({{ $age }} ans) }
```

### Variables dans les attributs

Vous pouvez également utiliser des variables dans les **arguments** et les **attributs** :

#### Dans les arguments `()`

```bview
/* Variable dans un argument */
img(src="{{ $imageUrl }}", alt="{{ $imageAlt }}")

/* Variable dans un argument de fonction */
product_card(title="{{ $productName }}", price="{{ $productPrice }}")
```

#### Dans les attributs `[]`

```bview
/* Variable dans un attribut */
div[data-id:{{ $userId }}, data-role:{{ $userRole }}]{
    - Contenu
}

/* Variable numérique */
rect[x:{{ $x }}, y:{{ $y }}, width:{{ $width }}, height:{{ $height }}]
```

### Contexte complet : `$raw` et `$ctrl`

#### Structure du contexte

```php
<?php
// Contexte passé au parser bview
$context = [
    'raw' => $data,        // Données passées à la vue
    'ctrl' => $controller  // Contrôleur qui initie la transformation
];

// Les données dans $raw sont accessibles directement
// Si $data = ['x' => 42, 'name' => 'Jean']
// Alors dans .bview :
// {{ $x }} équivaut à {{ $raw->x }}
// {{ $name }} équivaut à {{ $raw->name }}
```

#### Propriétés de `$raw`

```bview
/* Toutes les données passées sont dans $raw */
div{
    - ID : {{ $raw->id }}
    - Nom : {{ $raw->name }}
    - Email : {{ $raw->email }}
}

/* Équivalent raccourci */
div{
    - ID : {{ $id }}
    - Nom : {{ $name }}
    - Email : {{ $email }}
}
```

#### Méthodes de `$ctrl`

```bview
/* Méthodes courantes du contrôleur */
div{
    - Contrôleur : {{ $ctrl->getName() }}
    - Base URI : {{ $ctrl->getBaseUri() }}
    - Titre : {{ $ctrl->getTitle() }}
    - Version : {{ $ctrl->getVersion() }}
}

/* Méthodes utilitaires */
div{
    - URI complète : {{ $ctrl->uri('/page') }}
    - Asset URL : {{ $ctrl->assetUri('/img/logo.png') }}
}
```

### Exemples complets avec contexte

#### Exemple 1 : Carte de profil utilisateur

**Données du contexte :**
```php
<?php
$data = (object)[
    'id' => 123,
    'name' => 'Jean Dupont',
    'email' => 'jean@example.com',
    'avatar' => '/img/avatars/jean.jpg',
    'role' => 'Admin',
    'posts' => 45,
    'joined' => '2024-01-15'
];
```

**Code .bview :**
```bview
article.user-card[data-user-id:{{ $id }}]{
    div.card-header{
        img.avatar(src="{{ $avatar }}", alt="{{ $name }}")
        h2.user-name{ - {{ $name }} }
        span.user-role.role-{{ $role }}{ - {{ $role }} }
    }
    
    div.card-body{
        p.user-email{ - 📧 {{ $email }} }
        
        div.user-stats{
            span.stat{ - 📝 {{ $posts }} posts }
            span.stat{ - 📅 Membre depuis {{ $joined }} }
        }
    }
    
    div.card-footer{
        a(href="{{ $ctrl->uri('/users/' . $id) }}"){
            - Voir le profil
        }
    }
}
```

**Output HTML :**
```html
<article class="user-card" data-user-id="123">
    <div class="card-header">
        <img class="avatar" src="/img/avatars/jean.jpg" alt="Jean Dupont">
        <h2 class="user-name">Jean Dupont</h2>
        <span class="user-role role-Admin">Admin</span>
    </div>
    
    <div class="card-body">
        <p class="user-email">📧 jean@example.com</p>
        
        <div class="user-stats">
            <span class="stat">📝 45 posts</span>
            <span class="stat">📅 Membre depuis 2024-01-15</span>
        </div>
    </div>
    
    <div class="card-footer">
        <a href="/notebook/users/123">Voir le profil</a>
    </div>
</article>
```

#### Exemple 2 : Liste de produits

**Données du contexte :**
```php
<?php
$data = (object)[
    'categoryName' => 'Smartphones',
    'products' => [
        (object)['id' => 1, 'name' => 'iPhone 15', 'price' => 999],
        (object)['id' => 2, 'name' => 'Samsung Galaxy', 'price' => 899],
        (object)['id' => 3, 'name' => 'Google Pixel', 'price' => 699]
    ],
    'total' => 3
];
```

**Code .bview (structure statique avec variables) :**
```bview
section.products-section{
    header.section-header{
        h2{ - Catégorie : {{ $categoryName }} }
        span.count{ - {{ $total }} produits }
    }
    
    /* Note : Pour itérer sur $products, utilisez .phtml */
    /* Ici, exemple avec données statiques mais variables dynamiques */
    
    div.products-grid{
        /* Produit 1 */
        div.product-card[data-id:{{ $products[0]->id }}]{
            h3{ - {{ $products[0]->name }} }
            p.price{ - {{ $products[0]->price }} € }
        }
        
        /* Produit 2 */
        div.product-card[data-id:{{ $products[1]->id }}]{
            h3{ - {{ $products[1]->name }} }
            p.price{ - {{ $products[1]->price }} € }
        }
        
        /* Produit 3 */
        div.product-card[data-id:{{ $products[2]->id }}]{
            h3{ - {{ $products[2]->name }} }
            p.price{ - {{ $products[2]->price }} € }
        }
    }
}
```

#### Exemple 3 : Dashboard avec statistiques

**Données du contexte :**
```php
<?php
$data = (object)[
    'revenue' => 45892,
    'users' => 1247,
    'orders' => 342,
    'conversion' => 3.4,
    'trends' => (object)[
        'revenue' => '+12.5',
        'users' => '+8.3',
        'orders' => '0',
        'conversion' => '-2.1'
    ]
];
```

**Code .bview :**
```bview
main.dashboard{
    h1{ - Tableau de bord - {{ $ctrl->getTitle() }} }
    
    div.stats-grid{
        /* Revenus */
        div.stat-card[data-stat:revenue]{
            div.stat-icon{ - 💰 }
            div.stat-content{
                h3.stat-title{ - Revenus }
                p.stat-value{ - {{ $revenue }} € }
                p.stat-change.positive{ - {{ $trends->revenue }}% }
            }
        }
        
        /* Utilisateurs */
        div.stat-card[data-stat:users]{
            div.stat-icon{ - 👥 }
            div.stat-content{
                h3.stat-title{ - Utilisateurs }
                p.stat-value{ - {{ $users }} }
                p.stat-change.positive{ - {{ $trends->users }}% }
            }
        }
        
        /* Commandes */
        div.stat-card[data-stat:orders]{
            div.stat-icon{ - 📦 }
            div.stat-content{
                h3.stat-title{ - Commandes }
                p.stat-value{ - {{ $orders }} }
                p.stat-change.neutral{ - {{ $trends->orders }}% }
            }
        }
        
        /* Conversion */
        div.stat-card[data-stat:conversion]{
            div.stat-icon{ - 📈 }
            div.stat-content{
                h3.stat-title{ - Taux de conversion }
                p.stat-value{ - {{ $conversion }}% }
                p.stat-change.negative{ - {{ $trends->conversion }}% }
            }
        }
    }
}
```

### Échappement et sécurité

#### Échappement automatique

Par défaut, toutes les variables interpolées sont **automatiquement échappées** pour prévenir les injections XSS :

```bview
/* Variable échappée automatiquement */
div{ - {{ $userInput }} }

/* Les caractères HTML sont convertis en entités */
/* Si $userInput = "<script>alert('XSS')</script>" */
/* Output : &lt;script&gt;alert('XSS')&lt;/script&gt; */
```

#### HTML brut (non échappé)

Si vous devez insérer du HTML brut, utilisez `{!! $variable !!}` :

```bview
/* HTML brut (non échappé) - À utiliser avec précaution */
div{!! $htmlContent !!}

/* Exemple : */
/* Si $htmlContent = "<strong>Texte</strong>" */
/* Output : <strong>Texte</strong> */
```

⚠️ **Attention :** N'utilisez `{!! !!}` que pour du contenu fiable (généré par vous, pas par l'utilisateur).

### Expressions et opérations

#### Opérations simples

```bview
/* Addition */
div{ - Total : {{ $price + $tax }} € }

/* Multiplication */
div{ - Sous-total : {{ $quantity * $price }} € }

/* Concaténation */
div{ - Nom complet : {{ $firstName . ' ' . $lastName }} }
```

#### Opérateur ternaire

```bview
/* Condition ternaire */
div{ - Statut : {{ $active ? 'Actif' : 'Inactif' }} }

/* Avec classe conditionnelle */
span.badge.{{ $status == 'active' ? 'success' : 'danger' }}{
    - {{ $status }}
}
```

#### Opérateur de coalescence nulle

```bview
/* Valeur par défaut */
div{ - Nom : {{ $name ?? 'Anonyme' }} }

/* Propriété qui peut ne pas exister */
div{ - Ville : {{ $user->city ?? 'Non spécifiée' }} }
```

### Limitations et bonnes pratiques

#### ❌ Ce qui N'est PAS supporté dans .bview

```bview
/* ERREUR : Boucles */
@foreach($items as $item)  // ❌ Non supporté
    div{ - {{ $item }} }
@endforeach

/* ERREUR : Conditions */
@if($condition)  // ❌ Non supporté
    div{ - Condition vraie }
@endif

/* ERREUR : Logique complexe */
{{ $array->map(function($x) { return $x * 2; }) }}  // ❌ Trop complexe
```

#### ✅ Utilisez .phtml pour la logique

Pour les structures dynamiques complexes, utilisez `.phtml` :

```php
<?php
// mypage.phtml
foreach ($items as $item) {
    // Logique PHP complexe
    echo '<div>' . $item->name . '</div>';
}
?>
```

Ou combinez `.phtml` et `.bview` :

```php
<?php
// mypage.phtml
foreach ($products as $product) {
    // Préparer les données
    $productData = (object)[
        'id' => $product->id,
        'name' => $product->name,
        'price' => $product->price
    ];
    
    // Inclure une vue .bview pour chaque produit
    include 'Views/product-card.bview';
}
?>
```

#### ✅ Bonnes pratiques

1. **Utilisez .bview pour** : Templates statiques avec variables simples
2. **Utilisez .phtml pour** : Logique conditionnelle, boucles, manipulation de données
3. **Échappez toujours** : Les données utilisateur (échappement automatique avec `{{ }}`)
4. **Validez les données** : Avant de les passer au contexte
5. **Documentez les variables** : Indiquez quelles variables sont attendues

### Exemple de documentation des variables

```bview
/*
 * product-card.bview
 * 
 * Variables attendues dans le contexte :
 * - $id (int) : ID du produit
 * - $name (string) : Nom du produit
 * - $price (float) : Prix du produit
 * - $image (string) : URL de l'image
 * - $description (string, optionnel) : Description
 * - $inStock (bool) : Disponibilité
 * 
 * Contrôleur : $ctrl
 */

article.product-card[data-product-id:{{ $id }}]{
    div.card-image{
        img(src="{{ $image }}", alt="{{ $name }}")
    }
    
    div.card-body{
        h3.product-name{ - {{ $name }} }
        p.product-price{ - {{ $price }} € }
        
        /* Description optionnelle */
        p.product-description{ - {{ $description ?? 'Pas de description' }} }
        
        /* Badge de disponibilité */
        span.stock-badge.{{ $inStock ? 'in-stock' : 'out-of-stock' }}{
            - {{ $inStock ? 'En stock' : 'Rupture' }}
        }
    }
    
    div.card-footer{
        a.btn(href="{{ $ctrl->uri('/products/' . $id) }}"){
            - Voir les détails
        }
    }
}
```

---

## Arguments `()` vs Attributs `[]`

### Tableau comparatif : `()` vs `[]`

| Feature | Arguments `()` | Attributs `[]` |
|---------|----------------|----------------|
| **Usage** | Paramètres de fonction PHP | Attributs HTML/SVG/XML |
| **Séparateur valeur** | `=` (égal) | `:` (deux-points) |
| **Variables** | ✅ `title="{{ $name }}"` | ✅ `[data-id:{{ $id }}]` |
| **JSON/Objets** | ✅ `options={"key":"val"}` | ❌ Non supporté |

---

## Syntaxe compacte sur une ligne

```bview
/* Syntaxe compacte */
span{ - {{ $firstName }} }   span{ - {{ $lastName }} }   span{ - {{ $age }} ans }
```

**Output :**
```html
<span>Jean</span>
<span>Dupont</span>
<span>30 ans</span>
```

---

## Référence rapide

### Variables et contexte

| Syntaxe | Description | Exemple |
|---------|-------------|---------|
| `{{ $variable }}` | Variable du contexte | `{{ $name }}` |
| `{{ $raw->property }}` | Propriété de $raw | `{{ $raw->x }}` |
| `{{ $ctrl->method() }}` | Méthode du contrôleur | `{{ $ctrl->getName() }}` |
| `{{ $object->property }}` | Propriété d'objet | `{{ $user->email }}` |
| `{{ $array[0] }}` | Élément de tableau | `{{ $items[0] }}` |
| `{!! $html !!}` | HTML non échappé | `{!! $content !!}` |
| `{{ $a ?? 'default' }}` | Valeur par défaut | `{{ $name ?? 'Anonyme' }}` |
| `{{ $x ? 'A' : 'B' }}` | Ternaire | `{{ $active ? 'Oui' : 'Non' }}` |

### Contexte complet

```bview
/* Variables directes (raccourci) */
{{ $variable }}  /* équivaut à */ {{ $raw->variable }}

/* Données brutes */
{{ $raw->property }}

/* Contrôleur */
{{ $ctrl->method() }}

/* Dans arguments */
element(title="{{ $name }}")

/* Dans attributs */
element[data-id:{{ $id }}]
```

---

## Configuration

```php
<?php
// Dans Data/configs.dev.php

return [
    'bviewParser' => [
        'cache_enabled' => true,
        'debug' => false,
        'underscore_to_hyphen' => true,
        'json_support' => true,
        'variable_interpolation' => true, // Interpolation activée
        'auto_escape' => true, // Échappement automatique
        'context_variables' => ['raw', 'ctrl'] // Variables de contexte
    ]
];
```

---

## Commandes CLI

```bash
# Installer le module
balafon --module:install igk/bviewParser

# Valider la syntaxe
balafon --bview:validate Views/myfile.bview

# Compiler
balafon --bview:compile Views/

# Vider le cache
balafon --cache:clear bview
```

---

## Ressources

- **Dépôt GitHub :** [https://github.com/goukenn/balafon-module-igk-bviewParser.git](https://github.com/goukenn/balafon-module-igk-bviewParser.git)
- **Documentation Balafon :** [https://balafon.igkdev.com](https://balafon.igkdev.com)

---

**Dernière mise à jour :** 16 octobre 2025  
**Version du document :** 5.0  
**Langage :** Français

---

*Cette documentation complète couvre toute la syntaxe des fichiers `.bview` dans Balafon, incluant l'interpolation de variables avec `{{ }}`, l'accès au contexte via `$raw` et `$ctrl`, et toutes les fonctionnalités avancées du parser. Pour toute question ou contribution, visitez le dépôt GitHub officiel.*

**Bon développement avec Balafon et les fichiers .bview ! 🚀**