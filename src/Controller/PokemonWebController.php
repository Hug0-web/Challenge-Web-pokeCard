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
        $itemsPerPage = 12;

        $totalCards = $pokemonRepository->count([]);
        $totalPages = ceil($totalCards / $itemsPerPage);

        $pokemons = $pokemonRepository->findBy(
            [], 
            ['name' => 'ASC'], 
            $itemsPerPage, 
            ($page - 1) * $itemsPerPage
        );

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
