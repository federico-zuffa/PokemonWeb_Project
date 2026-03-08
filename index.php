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

<header>
    <h1><a href="index.php"> Mon Pokedex </a></h1>
    <p>Recherchez un Pokémon par nom ou numéro</p>
</header>

<!-- Formulaire de recherche -->
<div class="search-bar">
    <form method="GET" action="" style="display:flex; gap:10px;">
        <input
            type="text"
            name="pokemon"
            placeholder="Ex: pikachu, 25, bulbasaur..."
            value="<?php if(isset($_GET['pokemon'])) { echo $_GET['pokemon']; } ?>"
        >
        <button type="submit">Rechercher</button>
    </form>
</div>
<div class="search-bar">
    <form method="GET" action="">
        <?php
            if(!empty($_GET['pokemon'])) {
                echo '<input type="hidden" name="pokemon" value="' . htmlspecialchars($_GET["pokemon"]) . '">';
            }
            if(isset($_GET['sprite']) && $_GET['sprite'] === 'gif') {
                echo "<button type='submit' name='sprite' value='png'>Afficher les PNG</button>";
            } else {
                echo "<button type='submit' name='sprite' value='gif'>Afficher les GIF</button>";
            }
        ?>
    </form>
</div>



<?php

// Fonction pour appeler l'API PokeAPI
function getPokemon($nom) {
    $nom = strtolower(trim($nom));
    $url = "https://pokeapi.co/api/v2/pokemon/" . urlencode($nom);

    // Appel à l'API avec file_get_contents
    $json = @file_get_contents($url);

    if ($json == false) {
        return null; // Pokémon introuvable
    }

    return json_decode($json, true);
}

function getItem($itemName) {
    $itemName = strtolower(trim($itemName));
    $url = "https://pokeapi.co/api/v2/item/" . urlencode($itemName);

    $json = @file_get_contents($url);

    if ($json == false) {
        return null; // Item introuvable
    }
    return json_decode($json, true);
}

// Noms des statistiques en français
function nomStat($stat) {
    $noms = array(
        'hp'              => 'PV',
        'attack'          => 'Attaque',
        'defense'         => 'Défense',
        'special-attack'  => 'Att. Spé.',
        'special-defense' => 'Déf. Spé.',
        'speed'           => 'Vitesse',
    );
    if(isset($noms[$stat])) {
        return $noms[$stat];
    } else {
        return $stat;
    }
}

// Si l'utilisateur a soumis une recherche
if (!empty($_GET['pokemon'])) {
    $data = getPokemon($_GET['pokemon']);

    if ($data == null) {
        echo '<p class="error">❌ Pokémon introuvable. Vérifiez l\'orthographe ou le numéro.</p>';
    } else {
        $nom    = $data['name'];
        $id     = $data['id'];
        $sprite = $data['sprites']['front_default'];
        $poids  = $data['weight'] / 10; // en kg
        $taille = $data['height'] / 10; // en m
        $types  = $data['types'];
        $stats  = $data['stats'];
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

            <!-- Taille et poids -->
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
                    <strong><?php if(isset($data['base_experience'])) { echo $data['base_experience']; } else { echo '?'; } ?></strong>
                </div>
            </div>

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

// Afficher les Pokémon en grille
$listeJson = @file_get_contents("https://pokeapi.co/api/v2/pokemon?limit=1025");
if ($listeJson) {
    $liste = json_decode($listeJson, true);
    ?>
    <div class="grid-section">
        <h2>Les Pokémon</h2>
        <div class="pokemon-grid">
            <?php foreach ($liste['results'] as $index => $p) {
                $numero = $index + 1;

                if (isset($_GET['sprite']) && $_GET['sprite'] === 'gif') {
                    $spriteUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/" . $numero . ".gif";
                } else {
                    $spriteUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/" . $numero . ".png";
                }
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
?>

</body>
</html>