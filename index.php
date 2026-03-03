<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Pokédex</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href=<?php $data['sprites']['front_default']?>>
</head>
<body>

<header>
    <h1>Mon Pokédex</h1>
    <p>Recherchez un Pokémon par nom ou numéro</p>
</header>

<!-- Formulaire de recherche -->
<div class="search-bar">
    <form method="GET" action="" style="display:flex; gap:10px;">
        <input
            type="text"
            name="pokemon"
            placeholder="Ex: pikachu, 25, bulbasaur..."
            value="<?= htmlspecialchars($_GET['pokemon'] ?? '') ?>"
        >
        <button type="submit">Rechercher</button>
    </form>
</div>

<?php

// Fonction pour appeler l'API PokeAPI
function getPokemon($nom) {
    $nom = strtolower(trim($nom));
    $url = "https://pokeapi.co/api/v2/pokemon/" . urlencode($nom);

    // Appel à l'API avec file_get_contents
    $json = @file_get_contents($url);

    if ($json === false) {
        return null; // Pokémon introuvable
    }

    return json_decode($json, true);
}

function getItem($itemName){
    $itemName = strtolower(trim($itemName));
    $url = "//pokeapi.co/api/v2/item/" . urlencode($itemName);

    $json = @file_get_contents($url);

    if ($json === false) {
        return null; // Pokémon introuvable
    }
    return json_decode($json, true);
}

// Noms des statistiques en français
function nomStat($stat) {
    $noms = [
        'hp'              => 'PV',
        'attack'          => 'Attaque',
        'defense'         => 'Défense',
        'special-attack'  => 'Att. Spé.',
        'special-defense' => 'Déf. Spé.',
        'speed'           => 'Vitesse',
    ];
    return $noms[$stat] ?? $stat;
}

// Si l'utilisateur a soumis une recherche
if (!empty($_GET['pokemon'])) {
    $data = getPokemon($_GET['pokemon']);

    if ($data === null) {
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
            <p class="pokemon-id">#<?= str_pad($id, 3, '0', STR_PAD_LEFT) ?></p>
            <h2><?= htmlspecialchars($nom) ?></h2>

            <?php if ($sprite): ?>
                <img src="<?= $sprite ?>" alt="<?= htmlspecialchars($nom) ?>">
            <?php endif; ?>

            <!-- Types -->
            <div class="types">
                <?php foreach ($types as $t): ?>
                    <span class="type type-<?= $t['type']['name'] ?>">
                        <?= $t['type']['name'] ?>
                    </span>
                <?php endforeach; ?>
            </div>

            <!-- Taille et poids -->
            <div class="info-row">
                <div class="info-item">
                    <span>Taille</span>
                    <strong><?= $taille ?> m</strong>
                </div>
                <div class="info-item">
                    <span>Poids</span>
                    <strong><?= $poids ?> kg</strong>
                </div>
                <div class="info-item">
                    <span>Expérience</span>
                    <strong><?= $data['base_experience'] ?? '?' ?></strong>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="stats">
                <h3>Statistiques</h3>
                <?php foreach ($stats as $stat): ?>
                    <?php $valeur = $stat['base_stat']; ?>
                    <div class="stat">
                        <span class="stat-name"><?= nomStat($stat['stat']['name']) ?></span>
                        <div class="stat-bar-bg">
                            <div class="stat-bar" style="width: <?= min($valeur, 150) / 150 * 100 ?>%"></div>
                        </div>
                        <span class="stat-value"><?= $valeur ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php
    }
}

// Afficher les 20 premiers Pokémon en grille
$listeJson = @file_get_contents("https://pokeapi.co/api/v2/pokemon?limit=500");
if ($listeJson) {
    $liste = json_decode($listeJson, true);
    ?>
    <div class="grid-section">
        <h2>Les 20 premiers Pokémon</h2>
        <div class="pokemon-grid">
            <?php foreach ($liste['results'] as $index => $p):
                $numero = $index + 1;
                $spriteUrl = "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/" . $numero . ".png";
            ?>
                <a class="mini-card" href="?pokemon=<?= $p['name'] ?>">
                    <img src="<?= $spriteUrl ?>" alt="<?= $p['name'] ?>">
                    <p>#<?= str_pad($numero, 3, '0', STR_PAD_LEFT) ?></p>
                    <strong><?= $p['name'] ?></strong>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
?>

</body>
</html>