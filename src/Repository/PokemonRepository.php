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
        $em = $this->getEntityManager();
        $em->persist($pokemon);
        if ($flush) {
            $em->flush();
        }
    }

    public function remove(Pokemon $pokemon, bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->remove($pokemon);
        if ($flush) {
            $em->flush();
        }
    }

    public function removeAll(bool $flush = true): void
    {
        $em = $this->getEntityManager();
        $em->createQuery('DELETE FROM App\Entity\Pokemon p')->execute();
        if ($flush) {
            $em->flush();
        }
    }
}
