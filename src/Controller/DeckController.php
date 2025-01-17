<?php

namespace App\Controller;

use App\Entity\Deck;
use App\Entity\Pokemon;
use App\Repository\DeckRepository;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/deck')]
class DeckController extends AbstractController
{
    #[Route('/', name: 'app_deck_index', methods: ['GET'])]
    public function index(DeckRepository $deckRepository): Response
    {
        $deck = $deckRepository->findOneBy([]) ?? new Deck();
        
        return $this->render('deck/index.html.twig', [
            'deck' => $deck,
        ]);
    }

    #[Route('/add-pokemon', name: 'app_deck_add_pokemon', methods: ['POST'])]
    public function addPokemon(
        Request $request, 
        PokemonRepository $pokemonRepository, 
        DeckRepository $deckRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        $pokemonId = $request->request->get('pokemon_id');
        
        $deck = $deckRepository->findOneBy([]) ?? new Deck();
        
        $pokemon = $pokemonRepository->find($pokemonId);
        
        if (!$pokemon) {
            return $this->json(['error' => 'Pokemon not found'], Response::HTTP_NOT_FOUND);
        }
        
        if ($deck->getPokemons()->contains($pokemon)) {
            return $this->json(['error' => 'Pokemon already in deck'], Response::HTTP_BAD_REQUEST);
        }
        
        $deck->addPokemon($pokemon);
        
        if (!$deck->getId()) {
            $entityManager->persist($deck);
        }
        
        $entityManager->flush();
        
        return $this->json([
            'message' => 'Pokemon added to deck', 
            'pokemon_count' => $deck->getPokemonCount()
        ]);
    }

    #[Route('/remove-pokemon', name: 'app_deck_remove_pokemon', methods: ['POST'])]
    public function removePokemon(
        Request $request, 
        PokemonRepository $pokemonRepository, 
        DeckRepository $deckRepository, 
        EntityManagerInterface $entityManager
    ): Response {
        $pokemonId = $request->request->get('pokemon_id');
        
        $deck = $deckRepository->findOneBy([]);
        
        if (!$deck) {
            return $this->json(['error' => 'Deck not found'], Response::HTTP_NOT_FOUND);
        }
        
        $pokemon = $pokemonRepository->find($pokemonId);
        
        if (!$pokemon) {
            return $this->json(['error' => 'Pokemon not found'], Response::HTTP_NOT_FOUND);
        }
        
        $deck->removePokemon($pokemon);
        
        $entityManager->flush();
        
        return $this->json([
            'message' => 'Pokemon removed from deck', 
            'pokemon_count' => $deck->getPokemonCount()
        ]);
    }
}
