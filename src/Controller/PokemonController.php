<?php

namespace App\Controller;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use App\Service\PokemonDB;
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
                $pokemon->setId($cardData['id'])
                    ->setName($cardData['name'] ?? 'Unknown')
                    ->setSupertype($cardData['supertype'] ?? 'Unknown')
                    ->setSubtypes($cardData['subtypes'] ?? [])
                    ->setHp($cardData['hp'] ?? null)
                    ->setTypes($cardData['types'] ?? [])
                    ->setEvolvesFrom($cardData['evolvesFrom'] ?? null)
                    ->setAbilities($cardData['abilities'] ?? [])
                    ->setAttacks($cardData['attacks'] ?? [])
                    ->setWeaknesses($cardData['weaknesses'] ?? [])
                    ->setRetreatCost($cardData['retreatCost'] ?? [])
                    ->setConvertedRetreatCost($cardData['convertedRetreatCost'] ?? null)
                    ->setSet($cardData['set'] ?? [])
                    ->setNumber($cardData['number'] ?? '')
                    ->setArtist($cardData['artist'] ?? '')
                    ->setRarity($cardData['rarity'] ?? '')
                    ->setFlavorText($cardData['flavorText'] ?? null)
                    ->setNationalPokedexNumbers($cardData['nationalPokedexNumbers'] ?? [])
                    ->setLegalities($cardData['legalities'] ?? [])
                    ->setImages([
                        'small' => $cardData['images']['small'] ?? null,
                        'large' => $cardData['images']['large'] ?? null
                    ])
                    ->setTcgplayer($cardData['tcgplayer'] ?? [])
                    ->setCardmarket($cardData['cardmarket'] ?? []);

                $pokemonRepository->save($pokemon);
                $importedIds[] = $pokemon->getId();
                $totalImported++;
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