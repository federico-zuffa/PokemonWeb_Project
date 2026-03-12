# 🔴 Mon PasserеlleDex — Documentation

**Application web de consultation de Pokémon**  
Federico Zuffa | Classe Passerelle | 2026

---

## Table des matières

- [Introduction](#introduction)
- [Étude d'opportunité](#étude-dopportunité)
- [Analyse fonctionnelle](#analyse-fonctionnelle)
- [Analyse organique](#analyse-organique)
- [Tests et protocoles](#tests-et-protocoles)
- [Améliorations possibles](#améliorations-possibles)
- [Conclusion](#conclusion)
- [Bibliographie](#bibliographie)

---

## Introduction

Un Pokédex est un objet utilisé par les protagonistes de l'univers Pokémon afin de consulter et répertorier tous les Pokémon rencontrés sur le chemin. Comme dit le proverbe, **ATTRAPEZ LES TOUS !**

Ce projet est une application web développée en PHP qui permet de consulter, rechercher et organiser les Pokémon en exploitant les données de l'API publique [PokéAPI](https://pokeapi.co). Il constitue une introduction concrète à la consommation d'API REST depuis un serveur web.

Les API REST permettent à deux systèmes de communiquer via HTTP. Dans ce projet, PHP interroge PokéAPI pour récupérer dynamiquement les données de chaque Pokémon : statistiques, types, sprites, chaînes d'évolution. Cette architecture est identique à celle utilisée dans des applications réelles (dashboards, apps mobiles, sites e-commerce).

---

## Étude d'opportunité

### Pourquoi un Pokédex en PHP ?

Le Pokédex est un excellent support pour aborder des notions techniques concrètes et réutilisables :

- **Consommation d'API externe** : PokéAPI propose un endpoint REST documenté et gratuit retournant du JSON. Apprendre à l'interroger avec `file_get_contents()` ou cURL, puis à désérialiser les réponses, est une compétence fondamentale.
- **Gestion de session** : Le système de favoris repose sur `$_SESSION`, permettant de persister des données entre pages sans base de données.
- **Architecture modulaire** : La séparation en fichiers de fonctions (`api.php`, `utils.php`) et en includes (`header.php`, `filtres.php`) introduit les bonnes pratiques de séparation des responsabilités.
- **Dynamisme côté serveur** : Les filtres par type, par génération, l'affichage des évolutions et le bouton aléatoire illustrent la puissance de PHP sans JavaScript.

### Description de l'existant

| Catégorie | Description |
|-----------|-------------|
| Sites officiels | Bulbapedia, Serebii — complets mais statiques, sans personnalisation |
| Applications front-end | Pokédex en JavaScript (React, Vue) côté navigateur — notre projet effectue les appels côté serveur |
| Projets académiques | La plupart se contentent de données statiques ou d'une base locale — notre approche temps réel est plus représentative du contexte professionnel |

### Les plus du projet

- Données toujours à jour grâce à PokéAPI en temps réel, sans maintenance de base de données
- Gestion des favoris en session PHP sans inscription ni base de données
- Filtres par type et génération entièrement côté serveur, sans JavaScript
- Architecture modulaire facilitant la maintenance et l'ajout de fonctionnalités

---

## Analyse fonctionnelle

### Lexique de l'application

| Terme | Définition |
|-------|-----------|
| **Pokémon** | Entité récupérée dynamiquement depuis PokéAPI, identifiée par un nom ou un numéro |
| **Sprite** | Image d'un Pokémon, disponible en PNG statique ou GIF animé |
| **Type** | Caractéristique élémentaire (feu, eau, plante…), utilisée comme critère de filtre |
| **Génération** | Groupe de Pokémon introduits dans une version spécifique (Gen 1 = #001 à #151, etc.) |
| **Chaîne d'évolution** | Séquence des formes successives, récupérée via un troisième appel API |
| **Favori** | Pokémon sauvegardé dans la session PHP pour un accès rapide |
| **Session** | Mécanisme PHP (`$_SESSION`) pour conserver des données entre les pages |

### Fonctionnalités

- 🔍 Barre de recherche par nom ou numéro
- 📋 Carte de détail : sprite, types, taille, poids, expérience, statistiques, chaîne d'évolution
- ⭐ Bouton d'ajout/suppression aux favoris
- 🎲 Bouton Pokémon aléatoire (via `rand()`)
- 🏷️ Filtres par type (18 types) et par génération (9 générations)
- 🎬 Bascule entre sprites PNG et GIF animés
- 🗂️ Grille de tous les Pokémon avec liens de navigation
- ❤️ Page dédiée aux favoris avec suppression individuelle ou globale

### Principes d'utilisation

L'application fonctionne **entièrement sans JavaScript**. Toutes les interactions passent par des formulaires GET/POST et des liens HTML. Chaque action soumet une requête au serveur PHP qui interroge PokéAPI si nécessaire, génère le HTML et le renvoie au navigateur.

---

## Analyse organique

### Structure des fichiers

```
PokeWeb_Project/
├── index.php           ← Contrôleur principal
├── favoris.php         ← Page de gestion des favoris
├── styleClaude.css
├── fonctions/
│   ├── api.php         ← Appels à PokéAPI
│   └── utils.php       ← Fonctions utilitaires
└── includes/
    ├── header.php      ← En-tête commune
    └── filtres.php     ← Filtres par type et génération
```

### Description des fichiers

#### `fonctions/api.php` — Appels à PokéAPI

| Fonction | Description |
|----------|-------------|
| `getPokemon($nom)` | Appelle `/api/v2/pokemon/{nom}`, retourne un tableau PHP ou `null` si 404 |
| `getPokemonSpecies($id)` | Récupère les métadonnées de l'espèce, notamment l'URL de la chaîne d'évolution |
| `getEvolutionChain($url)` | Appelle l'URL de chaîne d'évolution, retourne un arbre récursif |
| `extraireEvolutions($chainLink)` | Parcourt récursivement l'arbre et retourne un tableau plat de noms |
| `getItem($itemName)` | Appelle `/api/v2/item/{nom}` (prévu pour une extension future) |

#### `fonctions/utils.php` — Fonctions utilitaires

| Fonction | Description |
|----------|-------------|
| `nomStat($stat)` | Traduit les noms de stats anglais (`hp`, `attack`…) en français |
| `getPokemonParGeneration($generation)` | Retourne la plage d'IDs `['debut' => N, 'fin' => M]` |
| `getSpriteUrl($numero, $format)` | Construit l'URL du sprite GitHub selon le format (`png` ou `gif`) |
| `getFormatSprite()` | Lit `$_GET['sprite']` et retourne `'gif'` ou `'png'` (png par défaut) |

#### `includes/header.php`

Contient le bloc `<header>`, la barre de recherche (formulaire GET) et les boutons secondaires (aléatoire, PNG/GIF, lien favoris). Inclus via `require_once` dans `index.php` et `favoris.php`. Les paramètres GET actifs sont conservés dans des champs `hidden`.

#### `includes/filtres.php`

Affiche les boutons de filtre par type (18 types colorés) et par génération (Gen 1 à Gen 9). Chaque bouton est un lien `<a href>` ajoutant le paramètre à l'URL. Le bouton actif est mis en évidence par la classe CSS `actif`.

#### `index.php` — Contrôleur principal

Orchestre l'application dans cet ordre :
1. `session_start()` + `require_once` des dépendances
2. Traitement POST (ajout/suppression favoris) avec redirection PRG
3. Redirection aléatoire (`rand(1, 1025)` + `header Location`)
4. Inclusion de `header.php` et `filtres.php`
5. Si `$_GET['pokemon']` : appel API + affichage de la carte détaillée
6. Affichage de la grille selon le filtre actif (type, génération, ou tous)

#### `favoris.php` — Page favoris

Page de consultation et gestion des favoris stockés en `$_SESSION`. Pour chaque favori, un appel `getPokemon()` récupère l'ID et le sprite. Propose suppression individuelle et globale, suivies d'une redirection PRG.

---

## Tests et protocoles

Tests réalisés manuellement sous XAMPP (Apache + PHP) sur macOS.

| N° | Description | Résultat attendu | Validation |
|----|-------------|-----------------|-----------|
| 1 | Rechercher `pikachu` | Carte détaillée avec stats | ✅ OK |
| 2 | Rechercher un ID (ex: 25) | Même résultat que par nom | ✅ OK |
| 3 | Rechercher un nom invalide | Message d'erreur rouge | ✅ OK |
| 4 | Cliquer Pokémon aléatoire | Redirection vers un Pokémon | ✅ OK |
| 5 | Ajouter un Pokémon aux favoris | Bouton change en ⭐ Retirer | ✅ OK |
| 6 | Consulter la page favoris | Grille des favoris sauvegardés | ✅ OK |
| 7 | Supprimer un favori individuel | Pokémon retiré de la liste | ✅ OK |
| 8 | Vider tous les favoris | Liste vide + message | ✅ OK |
| 9 | Filtre par type `fire` | Grille de Pokémon de type Feu | ✅ OK |
| 10 | Filtre par génération 1 | Pokémon #001 à #151 | ✅ OK |
| 11 | Passer en mode GIF | Sprites animés dans la grille | ✅ OK |
| 12 | Chaîne d'évolution Bulbasaur | Bulbasaur → Ivysaur → Venusaur | ✅ OK |
| 13 | Chaîne d'évolution Eevee | Eevee + 8 évolutions | ✅ OK |
| 14 | Rechargement après favori | Pas de re-soumission (PRG) | ✅ OK |

---

## Améliorations possibles

- **Mise en cache des réponses API** : Réduire les appels HTTP répétés via un cache fichier ou base de données
- **Pagination de la grille** : Utiliser `limit` et `offset` de PokéAPI plutôt que de charger 1025 Pokémon d'un coup
- **Persistance des favoris** : Utiliser `setcookie()` ou une base SQL pour conserver les favoris après fermeture du navigateur
- **Affichage des capacités** : Ajouter un onglet listant les moves de chaque Pokémon
- **Comparaison de Pokémon** : Page côte-à-côte avec barres de stats pour comparer visuellement
- **Internationalisation** : Afficher les noms en français en filtrant `languages[]` sur `'fr'` via PokéAPI
- **Système de jeu** : Simuler des combats Pokémon (nécessiterait plus d'expérience PHP ou l'utilisation de JavaScript)

---

## Conclusion

Ce projet a permis de transformer un sujet ludique en une véritable application web dynamique, en maîtrisant des notions directement transférables à un contexte professionnel.

La consommation de l'API REST PokéAPI est le cœur du projet : chaque affichage repose sur des requêtes HTTP dont les réponses JSON sont désérialisées pour construire dynamiquement le HTML — architecture identique à celle d'une application consommant un service météo, Stripe ou Google Maps.

La gestion des sessions PHP pour les favoris illustre la persistance d'état sans base de données, point de départ de tout système d'authentification ou panier e-commerce. L'architecture modulaire préfigure les architectures utilisées dans des frameworks modernes.

Ce Pokédex est une introduction concrète et motivante aux fondamentaux du développement web back-end, et un petit accomplissement personnel autour d'un univers ayant bercé l'enfance de son auteur.

---

## Remerciements

Merci à l'équipe d'enseignement pour l'accompagnement dans la découverte du développement web PHP, et aux camarades pour leurs échanges tout au long du projet.

---

## Bibliographie

- [PokéAPI — Documentation officielle](https://pokeapi.co/docs/v2)
- [MDN Web Docs](https://developer.mozilla.org)
- [PHP.net — Documentation officielle PHP](https://www.php.net/manual/fr/)
- [PokeAPI GitHub — Sprites](https://github.com/PokeAPI/sprites)
- [Pierre-Giraud — Filtres PHP](https://www.pierre-giraud.com/php-mysql-apprendre-coder-cours/presentation-filtre/)
- Andy's Tech Tutorials — *Pokemon API Tutorial | For Beginners*
- Elk SoftWare — *How to get all data from PokéAPI v2 - PHP Tutorial*
- rioredwards — *Coding Challenge: PHP Pokedex*
- ProgrammingKnowledge — *PHP REST API Tutorial* ([playlist](https://www.youtube.com/playlist?list=PLS1QulWo1RIYWjdoEC1WbT8W3XGGWVXfW))
- Aide au débogage et relecture de code : Claude (Anthropic) — aucun code généré
