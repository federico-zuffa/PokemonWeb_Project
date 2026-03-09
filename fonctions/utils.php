<?php
// ============================================================
// fonctions/utils.php
// Contient les fonctions utilitaires (pas d'appel API ici)
// ============================================================

// Traduit le nom technique d'une stat en français
function nomStat($stat) {
    $noms = array(
        'hp'              => 'PV',
        'attack'          => 'Attaque',
        'defense'         => 'Défense',
        'special-attack'  => 'Att. Spé.',
        'special-defense' => 'Déf. Spé.',
        'speed'           => 'Vitesse',
    );
    if (isset($noms[$stat])) {
        return $noms[$stat];
    } else {
        return $stat;
    }
}

// Retourne la plage d'IDs correspondant à une génération
function getPokemonParGeneration($generation) {
    $generations = array(
        '1' => array('debut' => 1,   'fin' => 151),
        '2' => array('debut' => 152, 'fin' => 251),
        '3' => array('debut' => 252, 'fin' => 386),
        '4' => array('debut' => 387, 'fin' => 493),
        '5' => array('debut' => 494, 'fin' => 649),
        '6' => array('debut' => 650, 'fin' => 721),
        '7' => array('debut' => 722, 'fin' => 809),
        '8' => array('debut' => 810, 'fin' => 905),
        '9' => array('debut' => 906, 'fin' => 1025),
    );
    if (isset($generations[$generation])) {
        return $generations[$generation];
    } else {
        return null;
    }
}

// Retourne l'URL du sprite selon le format choisi (png ou gif)
function getSpriteUrl($numero, $format) {
    if ($format === 'gif') {
        return "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/other/showdown/" . $numero . ".gif";
    } else {
        return "https://raw.githubusercontent.com/PokeAPI/sprites/master/sprites/pokemon/" . $numero . ".png";
    }
}

// Retourne le format de sprite actif depuis $_GET (png par défaut)
function getFormatSprite() {
    if (isset($_GET['sprite']) && $_GET['sprite'] === 'gif') {
        return 'gif';
    } else {
        return 'png';
    }
}
