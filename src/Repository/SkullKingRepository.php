<?php

namespace App\Repository;


use App\Entity\SkullKing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class SkullKingRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SkullKing::class);
    }

    public function save(SkullKing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }


    public function remove(SkullKing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);


        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('gr')->addSelect(['gru'])
            ->leftJoin('gr.users', 'gru')
            ->getQuery()->getResult();
    }


}

