<?php

namespace App\Provider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Repository\PokemonRepository;
use App\Entity\Pokemon;

class PokemonProvider implements ProviderInterface
{
    public function __construct(private PokemonRepository $pokemonRepository)
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation->getMethod() === 'GET') {
            if (isset($uriVariables['id'])) {
                return $this->pokemonRepository->find($uriVariables['id']);
            }
            
            return $this->pokemonRepository->findAll();
        }

        return null;
    }
}
