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


    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function updateWithVersionning(?int $id)
    {
        $skull = $this->find($id);
        $skull->setVersion(is_null($id) ? 1 : $skull->getVersion() + 1);
        $this->save($skull, true);
    }

    public function remove(SkullKing $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return SkullKing[] Returns an array of SkullKing objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('g.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?SkullKing
//    {
//        return $this->createQueryBuilder('g')
//            ->andWhere('g.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('gr')->addSelect(['gru'])
            ->leftJoin('gr.users', 'gru')
            ->getQuery()->getResult();
    }

}

