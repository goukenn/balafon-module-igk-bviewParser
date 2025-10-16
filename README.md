# balafon module [igk/bviewParser]
@C.A.D. BONDJE DOUE
allow to use .bview file as source of loading project's view. 


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
        - © 2024
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
 * Date : 2024-10-16
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

-# Documentation Balafon - Fichiers .bview

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
        - © 2024
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
 * Date : 2024-10-16
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

-