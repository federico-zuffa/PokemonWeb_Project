<?php
// ============================================================
// includes/filtres.php
// Affiche les boutons de filtre par type et par génération.
// ============================================================
?>

<div class="filtres">

    <!-- Filtre par type -->
    <div class="filtre-groupe">
        <strong>Type :</strong>
        <div class="filtre-boutons">
            <a class="filtre-btn <?php if (empty($_GET['type'])) { echo 'actif'; } ?>"
               href="?<?php if (!empty($_GET['sprite'])) { echo 'sprite=' . htmlspecialchars($_GET['sprite']) . '&'; } ?>">Tous</a>
            <?php
                $types = array('fire','water','grass','electric','psychic','ice','dragon','dark','fairy','normal','fighting','flying','poison','ground','rock','bug','ghost','steel');
                foreach ($types as $type) {
                    $actif = (isset($_GET['type']) && $_GET['type'] === $type) ? 'actif' : '';
                    $url = '?type=' . $type;
                    if (!empty($_GET['sprite'])) {
                        $url .= '&sprite=' . htmlspecialchars($_GET['sprite']);
                    }
                    echo '<a class="filtre-btn type-' . $type . ' ' . $actif . '" href="' . $url . '">' . ucfirst($type) . '</a>';
                }
            ?>
        </div>
    </div>

    <!-- Filtre par génération -->
    <div class="filtre-groupe">
        <strong>Génération :</strong>
        <div class="filtre-boutons">
            <a class="filtre-btn <?php if (empty($_GET['generation'])) { echo 'actif'; } ?>"
               href="?<?php if (!empty($_GET['sprite'])) { echo 'sprite=' . htmlspecialchars($_GET['sprite']); } ?>">Toutes</a>
            <?php
                for ($g = 1; $g <= 9; $g++) {
                    $actif = (isset($_GET['generation']) && $_GET['generation'] == $g) ? 'actif' : '';
                    $url = '?generation=' . $g;
                    if (!empty($_GET['sprite'])) {
                        $url .= '&sprite=' . htmlspecialchars($_GET['sprite']);
                    }
                    echo '<a class="filtre-btn ' . $actif . '" href="' . $url . '">Gen ' . $g . '</a>';
                }
            ?>
        </div>
    </div>

</div>
