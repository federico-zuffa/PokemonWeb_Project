<?php
// ============================================================
// fonctions/api.php
// Contient toutes les fonctions qui appellent l'API PokeAPI
// ============================================================

// Récupère les données d'un Pokémon par nom ou numéro
function getPokemon($nom) {
    $nom = strtolower(trim($nom));
    $url = "https://pokeapi.co/api/v2/pokemon/" . urlencode($nom);
    $json = @file_get_contents($url);
    if ($json == false) {
        return null;
    }
    return json_decode($json, true);
}

// Récupère les données d'un objet
function getItem($itemName) {
    $itemName = strtolower(trim($itemName));
    $url = "https://pokeapi.co/api/v2/item/" . urlencode($itemName);
    $json = @file_get_contents($url);
    if ($json == false) {
        return null;
    }
    return json_decode($json, true);
}

// Récupère les infos de l'espèce (nécessaire pour la chaîne d'évolution)
function getPokemonSpecies($id) {
    $url = "https://pokeapi.co/api/v2/pokemon-species/" . $id;
    $json = @file_get_contents($url);
    if ($json == false) {
        return null;
    }
    return json_decode($json, true);
}

// Récupère la chaîne d'évolution à partir de son URL
function getEvolutionChain($url) {
    $json = @file_get_contents($url);
    if ($json == false) {
        return null;
    }
    return json_decode($json, true);
}

// Parcourt récursivement la chaîne d'évolution et retourne un tableau de noms
function extraireEvolutions($chainLink) {
    $evolution = array();
    $evolution[] = $chainLink['species']['name'];
    foreach ($chainLink['evolves_to'] as $evoSuivante) {
        $sousEvolution = extraireEvolutions($evoSuivante);
        foreach ($sousEvolution as $sousEvo) {
            $evolution[] = $sousEvo;
        }
    }
    return $evolution;
}
