<?php

namespace App\Service;

use App\Entity\Pokemon;
use App\Repository\PokemonRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonTcgService
{
    private const API_URL = 'https://api.pokemontcg.io/v2';
    private string $apiKey;
    private HttpClientInterface $httpClient;
    private PokemonRepository $pokemonRepository;

    public function __construct(
        HttpClientInterface $httpClient,
        PokemonRepository $pokemonRepository,
        string $apiKey
    ) {
        $this->httpClient = $httpClient;
        $this->pokemonRepository = $pokemonRepository;
        $this->apiKey = $apiKey;
    }

    public function importCards(): void
    {
        // Supprimer toutes les cartes existantes
        $this->pokemonRepository->removeAll();

        $response = $this->httpClient->request('GET', self::API_URL . '/cards', [
            'headers' => [
                'X-Api-Key' => $this->apiKey
            ]
        ]);

        $data = $response->toArray();

        foreach ($data['data'] as $cardData) {
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
            $pokemon->setImages($cardData['images'] ?? []);
            $pokemon->setTcgplayer($cardData['tcgplayer'] ?? []);
            $pokemon->setCardmarket($cardData['cardmarket'] ?? []);

            $this->pokemonRepository->save($pokemon, false);
        }

        $this->pokemonRepository->getEntityManager()->flush();
    }
}
