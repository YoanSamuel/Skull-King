<?php

namespace App\Controller;

use App\Entity\GameId;
use App\Entity\SkullKing\SkullKing;
use App\Repository\SkullKingDoctrineRepository;
use App\Repository\SkullKingFirebaseRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

class SkullKingController
{

    private SkullKingDoctrineRepository $repository;
    private EntityManager $em;
    private SkullKingFirebaseRepository $firebaseRepository;
    private UserService $userService;

    public function __construct(EntityManagerInterface   $em,
                                SkullKingDoctrineRepository $repository,
                                SkullKingFirebaseRepository $firebaseRepository,
                                UserService              $userService)
    {
        $this->repository = $repository;
        $this->em = $em;
        $this->firebaseRepository = $firebaseRepository;
        $this->userService = $userService;
    }


    /**
     * @Route("/games", name="create-game", methods={"POST"})
     * @throws \Throwable
     */
    public function create(): Response
    {
        $user = $this->userService->getUserOrThrow();
        $gameId = $this->em->wrapInTransaction(function () use ($user) {
            $skullking = new SkullKing();
            $skullking->join($user->gameUserId());

            $gameId = $this->repository->save($skullking);
            $this->firebaseRepository->project($gameId, $skullking);
            return $gameId;
        });

        return new JsonResponse(["id" => $gameId->value()], 201, ['Content-Type' => 'application/json']);
    }

    /**
     * @Route("/games/{id}/join", name="join-game", methods={"POST"})
     * @throws \Throwable
     */
    public function join(string $id): Response
    {
        $user = $this->userService->getUserOrThrow();
        $this->em->wrapInTransaction(function () use ($user, $id) {
            $gameId = new GameId($id);
            $skullking = $this->repository->find($gameId);

            $skullking->join($user->gameUserId());

            $this->repository->save($skullking, $gameId);
            $this->firebaseRepository->project($gameId, $skullking);
        });

        return new Response(null, 201);
    }

    /**
     * @Route("/games/{id}/start", name="start-game", methods={"POST"})
     * @throws \Throwable
     */
    public function start(string $id): Response
    {
        $this->em->wrapInTransaction(function () use ($id) {
            $gameId = new GameId($id);
            $skullking = $this->repository->find($gameId);

            $skullking->start();

            $this->repository->save($skullking, $gameId);
            $this->firebaseRepository->project($gameId, $skullking);
        });

        return new Response(null, 201);
    }

    /**
     * @Route("/games/{id}/bet", name="bet", methods={"POST"})
     * @throws \Throwable
     */
    public function bet(string $id, Request $request): Response
    {
        $user = $this->userService->getUserOrThrow();
        $body = json_decode($request->getContent(), true);
        $this->em->wrapInTransaction(function () use ($body, $user, $id) {
            $gameId = new GameId($id);
            $skullking = $this->repository->find($gameId);

            $skullking->bet($user->gameUserId(), $body['number'], $body['value']);

            $this->repository->save($skullking, $gameId);
            $this->firebaseRepository->project($gameId, $skullking);
        });

        return new Response(null, 201);
    }


}