<?php

namespace App\Controller;

use App\Controller\dto\SkullDTO;
use App\Entity\SkullKing;
use App\Repository\SkullKingRepository;
use App\Security\UserService;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;


class SkullKingController extends AbstractController
{

    private SkullKingRepository $skullKingRepo;
    private HubInterface $hub;
    private UserService $userService;


    public function __construct(SkullKingRepository $skullKingRepo,
                                HubInterface        $hub,
                                UserService         $userService)
    {
        $this->skullKingRepo = $skullKingRepo;
        $this->hub = $hub;
        $this->userService = $userService;
    }


    #[Route('/game/{id}', name: 'current_game', methods: ["GET"])]
    public function currentGame($id, Request $request): Response
    {

        $skull = $this->skullKingRepo->find($id);

        if (is_null($skull)) {
            $this->redirectToRoute('app_game_room');
        }

        $userId = $this->userService->getUser()->getUuid();

        $announceValues = [];
        for ($i = 0; $i <= $skull->getNbRound(); $i++) {
            $announceValues[] = $i;
        }

        $topicName = "game_topic_$id";

        return $this->render('game/index.html.twig', [
            'id' => $id,
            'announceValues' => $announceValues,
            'skull' => new SkullDTO($skull, $userId),
            'topicName' => $topicName,
            'userId' => $userId,
        ]);
    }

    #[Route('/api/game/{id}', name: 'get_skullking', methods: ["GET"])]
    public function getSkullKing($id, Request $request): JsonResponse
    {

        $skull = $this->skullKingRepo->find($id);
        $userId = $this->userService->getUser()->getUuid();

        return new JsonResponse(json_encode(new SkullDTO($skull, $userId)), 200, ['Content-type' => 'application/json'], true);
    }

    /**
     * @throws OptimisticLockException
     */
    #[Route('/game/{id}/announce/{announce}', name: 'announce_before_play_round', methods: ["POST"])]
    public function announce($id, $announce, Request $request): Response
    {

        $skull = $this->skullKingRepo->find($id);
        try {

            $userId = $this->userService->getUser()->getUuid();
            $skull->announce($userId, $announce);

            $this->skullKingRepo->save($skull, true);
            $topicName = "game_topic_$id";
            $this->hub->publish(new Update(
                $topicName, json_encode([
                'status' => 'player_announced',
                'userId' => $userId,
                'announce' => $announce,
                'gameId' => $skull->getId()

            ])));

            return $this->redirectToRoute('current_game', ['id' => $id]);

        } catch (OptimisticLockException $e) {

            return $this->redirectToRoute('current_game', ['id' => $id, 'error' => $e->getMessage()]);
        }


    }


    #[Route('/game/{id}/player/{playerId}/playcard/{cardId}', name: 'play_card', methods: ["POST"])]
    public function playCard($id, $cardId, $playerId, Request $request): Response
    {
        /** @var SkullKing $skull */
        $skull = $this->skullKingRepo->find($id);
        try {

            $userId = $this->userService->getUser()->getUuid();
            $player = $skull->playCard($userId, $cardId);
            $this->skullKingRepo->save($skull, true);
            $topicName = "game_topic_$id";
            $this->hub->publish(new Update(
                $topicName, json_encode([
                'status' => 'player_play_card',
                'userId' => $userId,
                'cardId' => $cardId,
                'playerId' => $player->getId(),
                'gameId' => $skull->getId()

            ])));

            return new JsonResponse(null, 200);

        } catch (Exception $e) {

            return new JsonResponse(['code' => $e->getCode(),
                'message' => $e->getMessage()], 400);
        }
    }

}