<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pokemon')]
class PokemonController extends AbstractController
{
    private const API_URL = 'https://api.pokemontcg.io/v2';
    private string $apiKey;

    public function __construct(string $pokemonTcgApiKey)
    {
        $this->apiKey = $pokemonTcgApiKey;
    }

    #[Route('/import', name: 'api_pokemon_import', methods: ['POST'])]
    public function importCards(
        EntityManagerInterface $entityManager,
        PokemonRepository $pokemonRepository,
        Request $request
    ): JsonResponse {
        $httpClient = HttpClient::create();

        try {
            // Supprimer les cartes existantes
            $existingCards = $pokemonRepository->findAll();
            foreach ($existingCards as $card) {
                $entityManager->remove($card);
            }
            $entityManager->flush();

            $page = 1;
            $pageSize = 250; 
            $totalImported = 0;

            do {
                $response = $httpClient->request('GET', self::API_URL . '/cards', [
                    'query' => [
                        'page' => $page,
                        'pageSize' => $pageSize
                    ],
                    'headers' => [
                        'X-Api-Key' => $this->apiKey
                    ]
                ]);

                $data = $response->toArray();

                foreach ($data['data'] as $cardData) {
                    $pokemon = new Pokemon();
                    $pokemon->setId($cardData['id']);
                    $pokemon->setName($cardData['name']);
                    $pokemon->setSupertype($cardData['supertype']);
                    $pokemon->setSubtypes($cardData['subtypes'] ?? []);
                    $pokemon->setHp($cardData['hp'] ?? null);
                    $pokemon->setTypes($cardData['types'] ?? []);
                    $pokemon->setEvolvesFrom($cardData['evolvesFrom'] ?? null);
                    $pokemon->setAbilities($cardData['abilities'] ?? []);
                    $pokemon->setAttacks($cardData['attacks'] ?? []);
                    $pokemon->setWeaknesses($cardData['weaknesses'] ?? []);
                    $pokemon->setRetreatCost($cardData['retreatCost'] ?? []);
                    $pokemon->setConvertedRetreatCost($cardData['convertedRetreatCost'] ?? null);
                    $pokemon->setSet($cardData['set'] ?? []);
                    $pokemon->setNumber($cardData['number'] ?? '');
                    $pokemon->setArtist($cardData['artist'] ?? '');
                    $pokemon->setRarity($cardData['rarity'] ?? '');
                    $pokemon->setFlavorText($cardData['flavorText'] ?? null);
                    $pokemon->setNationalPokedexNumbers($cardData['nationalPokedexNumbers'] ?? []);
                    $pokemon->setLegalities($cardData['legalities'] ?? []);
                    $pokemon->setImages($cardData['images'] ?? []);
                    $pokemon->setTcgplayer($cardData['tcgplayer'] ?? []);
                    $pokemon->setCardmarket($cardData['cardmarket'] ?? []);

                    $entityManager->persist($pokemon);
                }

                $entityManager->flush();
                $totalImported += count($data['data']);

                $this->addFlash('info', "Imported page $page: " . count($data['data']) . " cards");

                $page++;

                sleep(1);

            } while (count($data['data']) == $pageSize);

            return $this->json([
                'message' => 'Cards imported successfully', 
                'count' => $totalImported,
                'pages' => $page - 1
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('', name: 'api_pokemon_list', methods: ['GET'])]
    public function listCards(PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemons = $pokemonRepository->findAll();
        $data = array_map(fn($pokemon) => $pokemon->toArray(), $pokemons);
        return $this->json($data);
    }

    #[Route('/{id}', name: 'api_pokemon_show', methods: ['GET'])]
    public function showCard(string $id, PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemon = $pokemonRepository->find($id);
        
        if (!$pokemon) {
            return $this->json(['error' => 'Pokemon not found'], 404);
        }

        return $this->json($pokemon->toArray());
    }
}
