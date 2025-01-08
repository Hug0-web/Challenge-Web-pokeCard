<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;  

#[ORM\Entity(repositoryClass: PokemonRepository::class)]
#[ApiResource(
    operations: [
        new Get(normalizationContext: ['groups' => ['pokemon:read']]),
        new GetCollection(normalizationContext: ['groups' => ['pokemon:read']])
    ],
    order: ['name' => 'ASC'],
    paginationEnabled: true,
    paginationItemsPerPage: 250
)]
class Pokemon
{
    #[ORM\Id]
    #[ORM\Column(length: 255)]
    private ?string $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $supertype = null;

    #[ORM\Column(type: Types::JSON)]
    private array $subtypes = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $hp = null;

    #[ORM\Column(type: Types::JSON)]
    private array $types = [];

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $evolvesFrom = null;

    #[ORM\Column(type: Types::JSON)]
    private array $abilities = [];

    #[ORM\Column(type: Types::JSON)]
    private array $attacks = [];

    #[ORM\Column(type: Types::JSON)]
    private array $weaknesses = [];

    #[ORM\Column(type: Types::JSON)]
    private array $retreatCost = [];

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private ?int $convertedRetreatCost = null;

    #[ORM\Column(type: Types::JSON)]
    private array $set = [];

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(length: 255)]
    private ?string $artist = null;

    #[ORM\Column(length: 255)]
    private ?string $rarity = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $flavorText = null;

    #[ORM\Column(type: Types::JSON)]
    private array $nationalPokedexNumbers = [];

    #[ORM\Column(type: Types::JSON)]
    private array $legalities = [];

    #[ORM\Column(type: Types::JSON)]
    private array $images = [];

    #[ORM\Column(type: Types::JSON)]
    private array $tcgplayer = [];

    #[ORM\Column(type: Types::JSON)]
    private array $cardmarket = [];

    
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSupertype(): ?string
    {
        return $this->supertype;
    }

    public function setSupertype(string $supertype): self
    {
        $this->supertype = $supertype;
        return $this;
    }

    public function getSubtypes(): array
    {
        return $this->subtypes;
    }

    public function setSubtypes(array $subtypes): self
    {
        $this->subtypes = $subtypes;
        return $this;
    }

    public function getHp(): ?string
    {
        return $this->hp;
    }

    public function setHp(?string $hp): self
    {
        $this->hp = $hp;
        return $this;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function setTypes(array $types): self
    {
        $this->types = $types;
        return $this;
    }

    public function getEvolvesFrom(): ?string
    {
        return $this->evolvesFrom;
    }

    public function setEvolvesFrom(?string $evolvesFrom): self
    {
        $this->evolvesFrom = $evolvesFrom;
        return $this;
    }

    public function getAbilities(): array
    {
        return $this->abilities;
    }

    public function setAbilities(array $abilities): self
    {
        $this->abilities = $abilities;
        return $this;
    }

    public function getAttacks(): array
    {
        return $this->attacks;
    }

    public function setAttacks(array $attacks): self
    {
        $this->attacks = $attacks;
        return $this;
    }

    public function getWeaknesses(): array
    {
        return $this->weaknesses;
    }

    public function setWeaknesses(array $weaknesses): self
    {
        $this->weaknesses = $weaknesses;
        return $this;
    }

    public function getRetreatCost(): array
    {
        return $this->retreatCost;
    }

    public function setRetreatCost(array $retreatCost): self
    {
        $this->retreatCost = $retreatCost;
        return $this;
    }

    public function getConvertedRetreatCost(): ?int
    {
        return $this->convertedRetreatCost;
    }

    public function setConvertedRetreatCost(?int $convertedRetreatCost): self
    {
        $this->convertedRetreatCost = $convertedRetreatCost;
        return $this;
    }

    public function getSet(): array
    {
        return $this->set;
    }

    public function setSet(array $set): self
    {
        $this->set = $set;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): self
    {
        $this->artist = $artist;
        return $this;
    }

    public function getRarity(): ?string
    {
        return $this->rarity;
    }

    public function setRarity(string $rarity): self
    {
        $this->rarity = $rarity;
        return $this;
    }

    public function getFlavorText(): ?string
    {
        return $this->flavorText;
    }

    public function setFlavorText(?string $flavorText): self
    {
        $this->flavorText = $flavorText;
        return $this;
    }

    public function getNationalPokedexNumbers(): array
    {
        return $this->nationalPokedexNumbers;
    }

    public function setNationalPokedexNumbers(array $nationalPokedexNumbers): self
    {
        $this->nationalPokedexNumbers = $nationalPokedexNumbers;
        return $this;
    }

    public function getLegalities(): array
    {
        return $this->legalities;
    }

    public function setLegalities(array $legalities): self
    {
        $this->legalities = $legalities;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    public function setImages(array $images): self
    {
        $this->images = $images;
        return $this;
    }

    public function getTcgplayer(): array
    {
        return $this->tcgplayer;
    }

    public function setTcgplayer(array $tcgplayer): self
    {
        $this->tcgplayer = $tcgplayer;
        return $this;
    }

    public function getCardmarket(): array
    {
        return $this->cardmarket;
    }

    public function setCardmarket(array $cardmarket): self
    {
        $this->cardmarket = $cardmarket;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'supertype' => $this->supertype,
            'subtypes' => $this->subtypes,
            'hp' => $this->hp,
            'types' => $this->types,
            'evolvesFrom' => $this->evolvesFrom,
            'abilities' => $this->abilities,
            'attacks' => $this->attacks,
            'weaknesses' => $this->weaknesses,
            'retreatCost' => $this->retreatCost,
            'convertedRetreatCost' => $this->convertedRetreatCost,
            'set' => $this->set,
            'number' => $this->number,
            'artist' => $this->artist,
            'rarity' => $this->rarity,
            'flavorText' => $this->flavorText,
            'nationalPokedexNumbers' => $this->nationalPokedexNumbers,
            'legalities' => $this->legalities,
            'images' => $this->images,
            'tcgplayer' => $this->tcgplayer,
            'cardmarket' => $this->cardmarket,
        ];
    }
}
