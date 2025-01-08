<?php

namespace App\Repository;

use App\Entity\Pokemon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PokemonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pokemon::class);
    }

    public function save(Pokemon $pokemon, bool $flush = true): void
    {
        $this->_em->persist($pokemon);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function remove(Pokemon $pokemon, bool $flush = true): void
    {
        $this->_em->remove($pokemon);
        if ($flush) {
            $this->_em->flush();
        }
    }
}
