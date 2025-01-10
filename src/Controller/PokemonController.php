<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use App\Service\PokemonTcgService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/pokemon')]
class PokemonController extends AbstractController
{
    private const API_URL = 'https://api.pokemontcg.io/v2';

    #[Route('/import', name: 'api_pokemon_import', methods: ['POST'])]
    public function importCards(
        EntityManagerInterface $entityManager,
        PokemonRepository $pokemonRepository,
        HttpClientInterface $httpClient,
        Request $request
    ): JsonResponse {
        try {
            
            $pokemonRepository->removeAll();

            $page = 1;
            $pageSize = 250; 
            $totalImported = 0;
            $importedIds = [];

            do {
                $response = $httpClient->request('GET', self::API_URL . '/cards', [
                    'query' => [
                        'page' => $page,
                        'pageSize' => $pageSize
                    ]
                ]);

                $data = $response->toArray();

                foreach ($data['data'] as $cardData) {
                
                    if ($totalImported >= 250) {
                        break 2;
                    }

                
                    if (!isset($cardData['id']) || empty($cardData['id'])) {
                        $this->addFlash('warning', "Skipping card without ID: " . json_encode($cardData));
                        continue;
                    }

                    $pokemon = new Pokemon();
                    $pokemon->setId($cardData['id']);
                    $pokemon->setName($cardData['name'] ?? 'Unknown');
                    $pokemon->setSupertype($cardData['supertype'] ?? 'Unknown');
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

                    try {
                        $pokemonRepository->save($pokemon);
                        $importedIds[] = $pokemon->getId();
                        $totalImported++;
                    } catch (\Exception $e) {
                        $this->addFlash('error', "Failed to import card {$pokemon->getId()}: " . $e->getMessage());
                    }
                }

                $this->addFlash('info', "Imported page $page: " . count($data['data']) . " cards");

                $page++;

                sleep(1);

            } while (count($data['data']) == $pageSize);

            return $this->json([
                'message' => 'Cards imported successfully', 
                'count' => $totalImported,
                'pages' => $page - 1,
                'importedIds' => $importedIds
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'error' => 'Import failed', 
                'details' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    #[Route('', name: 'api_pokemon_list', methods: ['GET'])]
    public function listCards(PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemons = $pokemonRepository->findAll();
        
        return $this->json($pokemons, 200, [], [
            'groups' => ['pokemon:read']
        ]);
    }

    #[Route('/card/{id}', name: 'api_pokemon_detail', methods: ['GET'])]
    public function detail(string $id, PokemonRepository $pokemonRepository): JsonResponse
    {
        $pokemon = $pokemonRepository->find($id);
        
        if (!$pokemon) {
    
            $allPokemonIds = $pokemonRepository->findAll();
            $existingIds = array_map(fn($p) => $p->getId(), $allPokemonIds);
            
            return $this->json([
                'error' => 'Pokemon not found', 
                'requestedId' => $id,
                'existingIds' => $existingIds
            ], 404);
        }

        return $this->json($pokemon, 200, [], [
            'groups' => ['pokemon:read']
        ]);
    }
}
