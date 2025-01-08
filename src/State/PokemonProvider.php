<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Entity\Pokemon;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PokemonProvider implements ProviderInterface
{
    private const API_URL = 'https://api.pokemontcg.io/v2';
    private string $apiKey;
    private HttpClientInterface $httpClient;
    private EntityManagerInterface $entityManager;

    public function __construct(
        HttpClientInterface $httpClient,
        EntityManagerInterface $entityManager,
        string $pokemonTcgApiKey
    ) {
        $this->httpClient = $httpClient;
        $this->entityManager = $entityManager;
        $this->apiKey = $pokemonTcgApiKey;
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $response = $this->httpClient->request('GET', self::API_URL . '/cards', [
            'headers' => [
                'X-Api-Key' => $this->apiKey
            ]
        ]);

        $data = $response->toArray();
        $pokemons = [];

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
            $pokemon->setNumber($cardData['number']);
            $pokemon->setArtist($cardData['artist']);
            $pokemon->setRarity($cardData['rarity']);
            $pokemon->setFlavorText($cardData['flavorText'] ?? null);
            $pokemon->setNationalPokedexNumbers($cardData['nationalPokedexNumbers'] ?? []);
            $pokemon->setLegalities($cardData['legalities'] ?? []);
            $pokemon->setImages($cardData['images'] ?? []);
            $pokemon->setTcgplayer($cardData['tcgplayer'] ?? []);
            $pokemon->setCardmarket($cardData['cardmarket'] ?? []);

            $this->entityManager->persist($pokemon);
            $pokemons[] = $pokemon;
        }

        $this->entityManager->flush();

        return $pokemons;
    }
}
