<?php

namespace App\Repository;

use App\Entity\Game;
use App\Entity\GameId;
use App\Entity\SkullKing\SkullKing;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class SkullKingDoctrineRepository
{
    private GameRepository $gameRepository;
    private SerializerInterface $serializer;

    public function __construct(GameRepository $gameRepository, SerializerInterface $serializer)
    {
        $this->gameRepository = $gameRepository;
        $this->serializer = $serializer;
    }

    /**
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    public function save(SkullKing $skullking, ?GameId $id = null): GameId
    {
        $game = is_null($id) ? new Game() : $this->findGame($id);
        $game->setVersion(is_null($id) ? 1 : $game->getVersion() + 1);
        $game->setPayload($this->serialize($skullking));

        $this->gameRepository->entityManager()->persist($game);
        $this->gameRepository->entityManager()->flush();

        return new GameId($game->getId());
    }

    private function findGame(GameId $gameId): Game
    {
        return $this->gameRepository->find($gameId->value());
    }

    public function find(GameId $gameId): SkullKing
    {
        $game = $this->findGame($gameId);
        return $this->deserialize($game->getPayload());
    }

    public function serialize(SkullKing $skullking): string
    {
        return $this->serializer->serialize($skullking, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['diceLauncher']]);
    }

    public function deserialize(string $json): SkullKing
    {
        return $this->serializer->deserialize($json, SkullKing::class, 'json');
    }
}