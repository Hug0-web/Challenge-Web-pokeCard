<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pokemon')]
class PokemonController extends AbstractController
{
    #[Route('/', name: 'api_pokemon_list', methods: ['GET'])]
    public function list(PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemons = $pokemonRepository->findAll();
        
        return $this->json($pokemons, 200, [], [
            'groups' => ['pokemon:read']
        ]);
    }

    #[Route('/{id}', name: 'api_pokemon_detail', methods: ['GET'])]
    public function detail(Pokemon $pokemon): JsonResponse
    {
        return $this->json($pokemon, 200, [], [
            'groups' => ['pokemon:read']
        ]);
    }
}
