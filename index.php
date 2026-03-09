<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Pokédex</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/svg+xml" href="./Ressources/PokeBall_icon.ico">
</head>
<body>

<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'fonctions/api.php';
require_once 'fonctions/utils.php';

// Initialisation des favoris en session
if (!isset($_SESSION['favoris'])) {
    $_SESSION['favoris'] = array();
}

// ============================================================
// GESTION DES FAVORIS (ajout / suppression via POST)
// ============================================================
if (isset($_POST['action']) && isset($_POST['pokemon_nom'])) {
    $nomFavori = $_POST['pokemon_nom'];

    if ($_POST['action'] == 'ajouter') {
        if (!in_array($nomFavori, $_SESSION['favoris'])) {
            $_SESSION['favoris'][] = $nomFavori;
        }
    }

    if ($_POST['action'] == 'supprimer') {
        $nouveauxFav = array();
        foreach ($_SESSION['favoris'] as $favori) {
            if ($favori !== $nomFavori) {
                $nouveauxFav[] = $favori;
            }
        }
        $_SESSION['favoris'] = $nouveauxFav;
    }

    // Redirection PRG : évite la re-soumission du formulaire au rechargement
    $redirect = '?';
    if (!empty($_GET['pokemon'])) {
        $redirect .= 'pokemon=' . urlencode($_GET['pokemon']) . '&';
    }
    if (!empty($_GET['type'])) {
        $redirect .= 'type=' . urlencode($_GET['type']) . '&';
    }
    if (!empty($_GET['generation'])) {
        $redirect .= 'generation=' . urlencode($_GET['generation']) . '&';
    }
    if (!empty($_GET['sprite'])) {
        $redirect .= 'sprite=' . urlencode($_GET['sprite']);
    }
    header('Location: ' . $redirect);
    exit();
}

// ============================================================
// POKÉMON ALÉATOIRE
// ============================================================
if (isset($_GET['aleatoire'])) {
    $idAleatoire = rand(1, 1025);
    header('Location: ?pokemon=' . $idAleatoire);
    exit();
}
?>

<?php require_once 'includes/header.php'; ?>
<?php require_once 'includes/filtres.php'; ?>

<?php
// ============================================================
// AFFICHAGE DE LA CARTE D'UN POKÉMON RECHERCHÉ
// ============================================================
if (!empty($_GET['pokemon'])) {
    $data = getPokemon($_GET['pokemon']);

    if ($data == null) {
        echo '<p class="error">❌ Pokémon introuvable. Vérifiez l\'orthographe ou le numéro.</p>';
    } else {
        $nom    = $data['name'];
        $id     = $data['id'];
        $poids  = $data['weight'] / 10; //(kg)
        $taille = $data['height'] / 10; //(m)
        $types  = $data['types'];
        $stats  = $data['stats'];

        $format = getFormatSprite();
        if ($format === 'gif') {
            $sprite = getSpriteUrl($id, 'gif');
        } else {
            $sprite = $data['sprites']['front_default'];
        }

        $estFavori = in_array($nom, $_SESSION['favoris']);

        // Récupération de la chaîne d'évolution
        $evolutions = array();
        $species = getPokemonSpecies($id);
        if ($species != null) {
            $chainData = getEvolutionChain($species['evolution_chain']['url']);
            if ($chainData != null) {
                $evolutions = extraireEvolutions($chainData['chain']);
            }
        }
        ?>

        <div class="pokemon-card">
            <p class="pokemon-id">#<?php echo str_pad($id, 3, '0', STR_PAD_LEFT) ?></p>
            <h2><?php echo $nom ?></h2>

            <?php if ($sprite) { ?>
                <img src="<?php echo $sprite ?>" alt="<?php echo $nom ?>">
            <?php } ?>

            <!-- Types -->
            <div class="types">
                <?php foreach ($types as $t) { ?>
                    <span class="type type-<?php echo $t['type']['name'] ?>">
                        <?php echo $t['type']['name'] ?>
                    </span>
                <?php } ?>
            </div>

            <!-- Taille, poids, expérience -->
            <div class="info-row">
                <div class="info-item">
                    <span>Taille</span>
                    <strong><?php echo $taille ?> m</strong>
                </div>
                <div class="info-item">
                    <span>Poids</span>
                    <strong><?php echo $poids ?> kg</strong>
                </div>
                <div class="info-item">
                    <span>Expérience</span>
                    <strong><?php if (isset($data['base_experience'])) { echo $data['base_experience']; } else { echo '?'; } ?></strong>
                </div>
            </div>

            <!-- Bouton favori -->
            <form method="POST" action="">
                <input type="hidden" name="pokemon_nom" value="<?php echo htmlspecialchars($nom) ?>">
                <?php if ($estFavori) { ?>
                    <button type="submit" name="action" value="supprimer" class="btn-favori btn-favori-actif">⭐ Retirer des favoris</button>
                <?php } else { ?>
                    <button type="submit" name="action" value="ajouter" class="btn-favori">☆ Ajouter aux favoris</button>
                <?php } ?>
            </form>

            <!-- Chaîne d'évolution -->
            <?php if (count($evolutions) > 1) { ?>
                <div class="evolutions">
                    <h3>Évolutions</h3>
                    <div class="evolution-chain">
                        <?php foreach ($evolutions as $index => $evoNom) { ?>
                            <?php if ($index > 0) { ?>
                                <span class="evolution-arrow">→</span>
                            <?php } ?>
                            <a href="?pokemon=<?php echo $evoNom ?>" class="evolution-item <?php if ($evoNom === $nom) { echo 'actif'; } ?>">
                                <?php
                                    $evoData = getPokemon($evoNom);
                                    $evoId = ($evoData != null) ? $evoData['id'] : 0;
                                ?>
                                <img src="<?php echo getSpriteUrl($evoId, 'png') ?>" alt="<?php echo $evoNom ?>">
                                <span><?php echo $evoNom ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>

            <!-- Statistiques -->
            <div class="stats">
                <h3>Statistiques</h3>
                <?php foreach ($stats as $stat) { ?>
                    <?php $valeur = $stat['base_stat']; ?>
                    <div class="stat">
                        <span class="stat-name"><?php echo nomStat($stat['stat']['name']) ?></span>
                        <div class="stat-bar-bg">
                            <div class="stat-bar" style="width: <?php echo min($valeur, 150) / 150 * 100 ?>%"></div>
                        </div>
                        <span class="stat-value"><?php echo $valeur ?></span>
                    </div>
                <?php } ?>
            </div>
        </div>

    <?php
    }
}

// ============================================================
// AFFICHAGE DE LA GRILLE AVEC FILTRES
// ============================================================
$format = getFormatSprite();

// --- Filtre par TYPE ---
if (!empty($_GET['type'])) {
    $typeChoisi = strtolower(trim($_GET['type']));
    $typeJson = @file_get_contents("https://pokeapi.co/api/v2/type/" . urlencode($typeChoisi));

    if ($typeJson != false) {
        $typeData = json_decode($typeJson, true);
        $pokemonsType = array();
        foreach ($typeData['pokemon'] as $entry) {
            $pokemonsType[] = $entry['pokemon']['name'];
        }
        ?>
        <div class="grid-section">
            <h2>Pokémon de type <?php echo ucfirst($typeChoisi) ?> (<?php echo count($pokemonsType) ?>)</h2>
            <div class="pokemon-grid">
                <?php foreach ($pokemonsType as $nomPokemon) {
                    $pData = getPokemon($nomPokemon);
                    if ($pData == null) { continue; }
                    $numero = $pData['id'];
                    if ($numero > 1025) { continue; }
                    $spriteUrl = getSpriteUrl($numero, $format);
                ?>
                    <a class="mini-card" href="?pokemon=<?php echo $nomPokemon ?>&type=<?php echo $typeChoisi ?>">
                        <img src="<?php echo $spriteUrl ?>" alt="<?php echo $nomPokemon ?>">
                        <p>#<?php echo str_pad($numero, 3, '0', STR_PAD_LEFT) ?></p>
                        <strong><?php echo $nomPokemon ?></strong>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php
    }

// --- Filtre par GÉNÉRATION ---
} else if (!empty($_GET['generation'])) {
    $genChoisie = $_GET['generation'];
    $plage = getPokemonParGeneration($genChoisie);

    if ($plage != null) {
        $limit = $plage['fin'] - $plage['debut'] + 1;
        $offset = $plage['debut'] - 1;
        $listeJson = @file_get_contents("https://pokeapi.co/api/v2/pokemon?limit=" . $limit . "&offset=" . $offset);

        if ($listeJson) {
            $liste = json_decode($listeJson, true);
            ?>
            <div class="grid-section">
                <h2>Génération <?php echo $genChoisie ?> — #<?php echo $plage['debut'] ?> à #<?php echo $plage['fin'] ?></h2>
                <div class="pokemon-grid">
                    <?php foreach ($liste['results'] as $index => $p) {
                        $numero = $plage['debut'] + $index;
                        $spriteUrl = getSpriteUrl($numero, $format);
                    ?>
                        <a class="mini-card" href="?pokemon=<?php echo $p['name'] ?>&generation=<?php echo $genChoisie ?>">
                            <img src="<?php echo $spriteUrl ?>" alt="<?php echo $p['name'] ?>">
                            <p>#<?php echo str_pad($numero, 3, '0', STR_PAD_LEFT) ?></p>
                            <strong><?php echo $p['name'] ?></strong>
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php
        }
    }

// --- PAS DE FILTRE : tous les Pokémon ---
} else {
    $listeJson = @file_get_contents("https://pokeapi.co/api/v2/pokemon?limit=1025");
    if ($listeJson) {
        $liste = json_decode($listeJson, true);
        ?>
        <div class="grid-section">
            <h2>Les Pokémon</h2>
            <div class="pokemon-grid">
                <?php foreach ($liste['results'] as $index => $p) {
                    $numero = $index + 1;
                    $spriteUrl = getSpriteUrl($numero, $format);
                ?>
                    <a class="mini-card" href="?pokemon=<?php echo $p['name'] ?>">
                        <img src="<?php echo $spriteUrl ?>" alt="<?php echo $p['name'] ?>">
                        <p>#<?php echo str_pad($numero, 3, '0', STR_PAD_LEFT) ?></p>
                        <strong><?php echo $p['name'] ?></strong>
                    </a>
                <?php } ?>
            </div>
        </div>
        <?php
    }
}
?>

</body>
</html>
