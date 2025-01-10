<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Entity\Pokemon;
use App\Repository\PokemonRepository;

class PokemonImportProcessor implements ProcessorInterface
{
    private const API_URL = 'https://api.pokemontcg.io/v2';
    private const MAX_CARDS = 250;

    public function __construct(
        private HttpClientInterface $httpClient,
        private EntityManagerInterface $entityManager,
        private PokemonRepository $pokemonRepository
    ) {}

    private function importPokemonCards(int $page = 1, int $pageSize = 250): int
    {
        $response = $this->httpClient->request('GET', self::API_URL . '/cards', [
            'query' => [
                'page' => $page,
                'pageSize' => $pageSize
            ]
        ]);

        $responseData = $response->toArray();
        $totalImported = 0;

        foreach ($responseData['data'] as $cardData) {
            if (!isset($cardData['id']) || empty($cardData['id'])) {
                continue;
            }

          
            if ($totalImported >= self::MAX_CARDS) {
                break;
            }

            $pokemon = new Pokemon();
            $pokemon->setId($cardData['id'] ?? null);
            $pokemon->setName($cardData['name'] ?? '');
            $pokemon->setSupertype($cardData['supertype'] ?? '');
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

            $this->entityManager->persist($pokemon);
            $totalImported++;
        }

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $responseData['totalPages'] ?? 1;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Pokemon|null
    {
        
        $this->pokemonRepository->removeAll();

        $page = 1;
        $totalImported = 0;
        $totalPages = 1;

        do {
            $totalPages = $this->importPokemonCards($page);
            $page++;

            if ($totalImported >= self::MAX_CARDS) {
                break;
            }
        } while ($page <= $totalPages);

        return null;
    }
}
