<?php

namespace App\Controller;

use App\Repository\PokemonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PokemonViewController extends AbstractController
{
    private const ITEMS_PER_PAGE = 12;

    #[Route('/', name: 'homepage')]
    public function index(Request $request, PokemonRepository $pokemonRepository): Response
    {
        $page = max(1, $request->query->getInt('page', 1));

        $queryBuilder = $pokemonRepository->createQueryBuilder('p')
            ->orderBy('p.name', 'ASC');

        $queryBuilder
            ->setFirstResult(($page - 1) * self::ITEMS_PER_PAGE)
            ->setMaxResults(self::ITEMS_PER_PAGE);

        $paginator = new Paginator($queryBuilder);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / self::ITEMS_PER_PAGE);

        return $this->render('pokemon/index.html.twig', [
            'pokemons' => $paginator,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems
        ]);
    }
}
