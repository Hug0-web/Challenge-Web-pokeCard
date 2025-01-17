<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['deck:read']]),
        new Post(normalizationContext: ['groups' => ['deck:write']]),
        new Delete()
    ],
    normalizationContext: ['groups' => ['deck:read']],
    denormalizationContext: ['groups' => ['deck:write']]
)]
class Deck
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(['deck:read'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(['deck:read', 'deck:write'])]
    private string $name = 'My Deck';

    #[ORM\ManyToMany(targetEntity: Pokemon::class)]
    #[ORM\JoinTable(name: 'deck_pokemon')]
    #[Groups(['deck:read', 'deck:write'])]
    private Collection $pokemons;

    #[ORM\Column(type: 'integer')]
    #[Groups(['deck:read'])]
    private int $pokemonCount = 0;

    public function __construct()
    {
        $this->pokemons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getPokemons(): Collection
    {
        return $this->pokemons;
    }

    public function addPokemon(Pokemon $pokemon): self
    {
        if (!$this->pokemons->contains($pokemon)) {
            $this->pokemons->add($pokemon);
            $this->pokemonCount++;
        }
        return $this;
    }

    public function removePokemon(Pokemon $pokemon): self
    {
        if ($this->pokemons->removeElement($pokemon)) {
            $this->pokemonCount--;
        }
        return $this;
    }

    public function getPokemonCount(): int
    {
        return $this->pokemonCount;
    }
}
