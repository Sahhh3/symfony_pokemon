<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PokemonsRepository;

class ListePokemonController extends AbstractController
{
    #[Route('/liste/pokemon', name: 'app_liste_pokemon')]
    public function index(PokemonsRepository $pokemonsRepository): Response
    {
        $pokemons = $pokemonsRepository->findAll();
 
        return $this->render('liste_pokemon/index.html.twig', [
            'pokemons' => $pokemons,
        ]);
    }
}
