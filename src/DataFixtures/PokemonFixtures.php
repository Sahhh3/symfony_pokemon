<?php

// src/DataFixtures/PokemonFixtures.php
namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Pokemons;

class PokemonFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // Reste du code pour charger les données
        $apiUrl = "https://pokeapi.co/api/v2/pokemon?limit=151";
        $pokemonData = file_get_contents($apiUrl);

        if ($pokemonData !== false) {
            $data = json_decode($pokemonData, true);

            foreach ($data['results'] as $pokemon) {
                $name = $pokemon['name'];

                // Ajouter une requête pour obtenir les détails du Pokémon (y compris la description)
                $pokemonDetailsUrl = $pokemon['url'];
                $pokemonDetailsData = file_get_contents($pokemonDetailsUrl);
                $pokemonDetails = json_decode($pokemonDetailsData, true);

                // Récupérer la description à partir de l'API characteristic
                if ($pokemonDetails['id'] < 31) 
                    $description = $this->getPokemonDescription($pokemonDetails['id']); // Utilisez la méthode définie ci-dessous
                else
                    $description = 'Description non disponible';

                // Récupérer les types
                $types = $pokemonDetails['types'];
                $type1 = $types[0]['type']['name'];
                $type2 = count($types) > 1 ? $types[1]['type']['name'] : null;

                $newPokemon = new Pokemons();
                $newPokemon->setNom($name);
                $newPokemon->setDescription($description);
                $newPokemon->setType1($type1);
                $newPokemon->setType2($type2);

                $manager->persist($newPokemon);
            }

            $manager->flush();
        } else {
            echo "Erreur lors de la récupération des données de l'API.\n";
        }
    }

    // Méthode pour récupérer la description du Pokémon
    private function getPokemonDescription($pokemonId)
    {
        $characteristicUrl = "https://pokeapi.co/api/v2/characteristic/{$pokemonId}/";
        $characteristicData = file_get_contents($characteristicUrl);

        if ($characteristicData !== false) {
            $characteristicDetails = json_decode($characteristicData, true);

            // Recherche de la description en français
            $description = 'Description non disponible';
            foreach ($characteristicDetails['descriptions'] as $description) {
                if ($description['language']['name'] === 'en') {
                    $descriptionEN = $description['description'];
                    break; // Sortir de la boucle dès qu'on trouve la description en français
                }
            }

            return $descriptionEN;
        } else {
            return "Description non disponible";
        }

    }
}
