<?php
// ============================================================
// includes/header.php
// Contient le <header> HTML, la barre de recherche et les
// boutons secondaires (aléatoire, GIF/PNG, favoris).
// Ce fichier suppose que session_start() a déjà été appelé.
// ============================================================
?>

<header>
    <h1><a href="index.php"> Mon Pokedex </a></h1>
    <p>Recherchez un Pokémon par nom ou numéro</p>
</header>

<!-- Barre de recherche -->
<div class="search-bar">
    <form method="GET" action="" style="display:flex; gap:10px;">
        <input
            type="text"
            name="pokemon"
            placeholder="Ex: pikachu, 25, bulbasaur..."
            value="<?php if (isset($_GET['pokemon'])) { echo htmlspecialchars($_GET['pokemon']); } ?>"
        >
        <?php
            if (!empty($_GET['type'])) {
                echo '<input type="hidden" name="type" value="' . htmlspecialchars($_GET['type']) . '">';
            }
            if (!empty($_GET['generation'])) {
                echo '<input type="hidden" name="generation" value="' . htmlspecialchars($_GET['generation']) . '">';
            }
            if (!empty($_GET['sprite'])) {
                echo '<input type="hidden" name="sprite" value="' . htmlspecialchars($_GET['sprite']) . '">';
            }
        ?>
        <button type="submit">Rechercher</button>
    </form>
</div>

<!-- Boutons secondaires : aléatoire, GIF/PNG, favoris -->
<div class="search-bar" style="gap:10px; flex-wrap:wrap; justify-content:center;">

    <a class="btn" href="?aleatoire=1">🎲 Pokémon aléatoire</a>

    <form method="GET" action="">
        <?php
            if (!empty($_GET['pokemon'])) {
                echo '<input type="hidden" name="pokemon" value="' . htmlspecialchars($_GET['pokemon']) . '">';
            }
            if (!empty($_GET['type'])) {
                echo '<input type="hidden" name="type" value="' . htmlspecialchars($_GET['type']) . '">';
            }
            if (!empty($_GET['generation'])) {
                echo '<input type="hidden" name="generation" value="' . htmlspecialchars($_GET['generation']) . '">';
            }
            if (isset($_GET['sprite']) && $_GET['sprite'] === 'gif') {
                echo "<button type='submit' name='sprite' value='png'>Afficher les PNG</button>";
            } else {
                echo "<button type='submit' name='sprite' value='gif'>Afficher les GIF</button>";
            }
        ?>
    </form>

    <a class="btn btn-favoris" href="favoris.php">⭐ Mes favoris (<?php echo count($_SESSION['favoris']); ?>)</a>

</div>
