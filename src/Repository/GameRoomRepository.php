<?php

namespace App\Repository;

use App\Entity\GameRoom;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GameRoom>
 *
 * @method GameRoom|null find($id, $lockMode = null, $lockVersion = null)
 * @method GameRoom|null findOneBy(array $criteria, array $orderBy = null)
 * @method GameRoom[]    findAll()
 * @method GameRoom[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GameRoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GameRoom::class);
    }

    public function save(GameRoom $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GameRoom $entity, bool $flush = false): void
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
