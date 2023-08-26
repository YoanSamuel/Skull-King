<?php

namespace App\Repository;


use App\Entity\SkullKing;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SkullKingRepository extends ServiceEntityRepository
{
    private SerializerInterface $serializer;

    public function __construct(ManagerRegistry $registry, SerializerInterface $serializer)
    {
        parent::__construct($registry, SkullKing::class);
        $this->serializer = $serializer;
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

    public function serialize(SkullKing $skullKing): string
    {
        return $this->serializer->serialize($skullKing, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['announceValues']]);
    }

    public function deserialize(string $json): SkullKing
    {
        return $this->serializer->deserialize($json, SkullKing::class, 'json');
    }

    public function findAllWithUsers(): array
    {
        return $this->createQueryBuilder('gr')->addSelect(['gru'])
            ->leftJoin('gr.users', 'gru')
            ->getQuery()->getResult();
    }


}

