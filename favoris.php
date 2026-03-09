<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Favoris — Mon Pokédex</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" type="image/svg+xml" href="./Ressources/PokeBall_icon.ico">
</head>
<body>

<?php
session_start();

require_once 'fonctions/api.php';
require_once 'fonctions/utils.php';

if (!isset($_SESSION['favoris'])) {
    $_SESSION['favoris'] = array();
}

if (isset($_POST['action']) && $_POST['action'] === 'supprimer' && isset($_POST['pokemon_nom'])) {
    $nomFavori = $_POST['pokemon_nom'];
    $nouveauxFavoris = array();
    foreach ($_SESSION['favoris'] as $favori) {
        if ($favori !== $nomFavori) {
            $nouveauxFavoris[] = $favori;
        }
    }
    $_SESSION['favoris'] = $nouveauxFavoris;
    header('Location: favoris.php');
    exit();
}

if (isset($_POST['action']) && $_POST['action'] === 'vider_tout') {
    $_SESSION['favoris'] = array();
    header('Location: favoris.php');
    exit();
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="grid-section">
    <h2>⭐ Mes Favoris (<?php echo count($_SESSION['favoris']) ?>)</h2>

    <?php if (count($_SESSION['favoris']) === 0) { ?>
        <p class="error">Vous n'avez pas encore de Pokémon en favoris.</p>
    <?php } else { ?>

        <form method="POST" action="" style="text-align:center; margin-bottom:20px;">
            <button type="submit" name="action" value="vider_tout" class="btn" style="background:#999;">🗑️ Vider tous les favoris</button>
        </form>

        <div class="pokemon-grid">
            <?php foreach ($_SESSION['favoris'] as $nomFavori) {
                $data = getPokemon($nomFavori);
                if ($data == null) { continue; }
                $numero = $data['id'];
                $spriteUrl = getSpriteUrl($numero, 'png');
            ?>
                <div class="mini-card">
                    <a href="index.php?pokemon=<?php echo $nomFavori ?>">
                        <img src="<?php echo $spriteUrl ?>" alt="<?php echo $nomFavori ?>">
                        <p>#<?php echo str_pad($numero, 3, '0', STR_PAD_LEFT) ?></p>
                        <strong><?php echo $nomFavori ?></strong>
                    </a>
                    <form method="POST" action="">
                        <input type="hidden" name="pokemon_nom" value="<?php echo htmlspecialchars($nomFavori) ?>">
                        <button type="submit" name="action" value="supprimer" class="btn-favori btn-favori-actif" style="margin-top:8px; font-size:0.8em;">✕ Retirer</button>
                    </form>
                </div>
            <?php } ?>
        </div>

    <?php } ?>
</div>

</body>
</html>
