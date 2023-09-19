<?php

namespace App\Controller;

use App\Controller\dto\PlayerDTO;
use App\Controller\dto\SkullDTO;
use App\Entity\Player;
use App\Entity\SkullKing;
use App\Repository\SkullKingRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


class SkullKingController extends AbstractController
{

    private SkullKingRepository $skullKingRepo;
    private HubInterface $hub;


    public function __construct(SkullKingRepository $skullKingRepo,
                                HubInterface        $hub)
    {
        $this->skullKingRepo = $skullKingRepo;
        $this->hub = $hub;

    }


    #[Route('/game/{id}', name: 'current_game', methods: ["GET"])]
    public function currentGame($id, Request $request): Response
    {

        $skull = $this->skullKingRepo->find($id);

        if (is_null($skull)) {
            $this->redirectToRoute('app_game_room');
        }

        $userId = new Uuid($request->cookies->get('userid'));

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
            'version' => $skull->getVersion(),
        ]);
    }

    #[Route('/api/game/{id}', name: 'get_skullking', methods: ["GET"])]
    public function getSkullKing($id, Request $request): JsonResponse
    {

        $skull = $this->skullKingRepo->find($id);
        $userId = new Uuid($request->cookies->get('userid'));

        return new JsonResponse(json_encode(new SkullDTO($skull, $userId)), 200, ['Content-type' => 'application/json'], true);
    }

    /**
     * @throws OptimisticLockException
     * @throws \Doctrine\ORM\ORMException
     */
    #[Route('/game/{id}/announce/{announce}', name: 'announce_before_play_round', methods: ["POST"])]
    public function announce($id, $announce, Request $request): Response
    {

        $skull = $this->skullKingRepo->find($id);
        try {

            $userId = new Uuid($request->cookies->get('userid'));
            $skull->announce($userId, $announce);

            $this->skullKingRepo->save($skull, true);
            $topicName = "game_topic_$id";
            $this->hub->publish(new Update(
                $topicName, json_encode([
                'status' => 'player_announced',
                'userId' => $userId,
                'announce' => $announce,
                'gamePhase' => $skull->getState(),

            ])));

            return $this->redirectToRoute('current_game', ['id' => $id]);

        } catch (OptimisticLockException $e) {

            return $this->redirectToRoute('current_game', ['id' => $id, 'error' => true]);
        }


    }


    /**
     * @throws OptimisticLockException
     */
    #[Route('/game/{id}/player/{playerId}/playcard/{cardId}', name: 'play_card', methods: ["POST"])]
    public function playCard($id, $cardId, $playerId, Request $request): Response
    {
        /** @var SkullKing $skull */
        $skull = $this->skullKingRepo->find($id);
        try {
            $userId = new Uuid($request->cookies->get('userid'));
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

        } catch (\Exception $e) {

            return new JsonResponse(['code' => $e->getCode(),
                'message' => $e->getMessage()], 400);
        }


    }


    /**
     * @param Collection $players
     * @return PlayerDTO[]
     */
    public function convertPlayersDTO(Collection $players): array
    {
        return $players->map(function (Player $player) {
            return new PlayerDTO($player);
        })->toArray();
    }


}