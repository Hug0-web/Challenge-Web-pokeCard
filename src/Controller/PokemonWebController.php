<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PokemonWebController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(PokemonRepository $pokemonRepository, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = 50; // Augmenté de 20 à 50
        $offset = ($page - 1) * $limit;

        $pokemons = $pokemonRepository->findBy([], ['name' => 'ASC'], $limit, $offset);
        $totalPokemons = $pokemonRepository->count([]);
        $totalPages = ceil($totalPokemons / $limit);

        return $this->render('pokemon/index.html.twig', [
            'pokemons' => $pokemons,
            'currentPage' => $page,
            'totalPages' => $totalPages
        ]);
    }

    #[Route('/pokemon/{id}', name: 'pokemon_detail')]
    public function detail(string $id, PokemonRepository $pokemonRepository): Response
    {
        $pokemon = $pokemonRepository->find($id);

        if (!$pokemon) {
            throw $this->createNotFoundException('Pokemon not found');
        }

        return $this->render('pokemon/detail.html.twig', [
            'pokemon' => $pokemon
        ]);
    }
}
