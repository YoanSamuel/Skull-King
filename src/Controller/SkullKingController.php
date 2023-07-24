<?php

namespace App\Controller;

use App\Controller\dto\CardDTO;
use App\Controller\dto\PlayerDTO;
use App\Controller\dto\SkullDTO;
use App\Entity\Card;
use App\Entity\Player;
use App\Entity\SkullKing;
use App\Repository\CardRepository;
use App\Repository\SkullKingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    private EntityManagerInterface $em;
    private CardRepository $cardRepo;
    private LoggerInterface $logger;

    public function __construct(SkullKingRepository    $skullKingRepo,
                                HubInterface           $hub,
                                EntityManagerInterface $em,
                                CardRepository         $cardRepo,
                                LoggerInterface        $logger)
    {
        $this->skullKingRepo = $skullKingRepo;
        $this->hub = $hub;
        $this->em = $em;
        $this->cardRepo = $cardRepo;
        $this->logger = $logger;
    }


    #[Route('/game/{id}', name: 'current_game', methods: ["GET"])]
    public function currentGame($id, Request $request): Response
    {

        $skull = $this->skullKingRepo->find($id);

        $userId = new Uuid($request->cookies->get('userid'));
        $currentPlayer = $skull->findPlayer($userId);
        $gamePhase = $skull->getState();

        // Champ Annonce
        $announceValues = [];
        for ($i = 0; $i <= $skull->getNbRound(); $i++) {
            $announceValues[] = $i;
        }
        $fold = [];
        $topicName = "game_topic_$id";

        return $this->render("game/index.html.twig", [
            'id' => $id,
            'announceValues' => $announceValues,
            'cards' => $currentPlayer->getCards()->map(function (Card $card) {
                return new CardDTO($card);
            })->filter(function (CardDTO $card) {
                return is_null($card->skullKingId);
            })->toArray(),
            'gamePhase' => $gamePhase,
            'fold' => array_map(function (Card $card) {
                return new CardDTO($card);
            }, $skull->getFold()->toArray()),
            'players' => array_map(function (Player $player) {
                return new PlayerDTO($player);
            }, $skull->getPlayers()->toArray()),
            'skull' => array_map(function (SkullKing $skullKing) {
                return new SkullDTO($skullKing);
            }, [$skull]),
            'topicName' => $topicName,
            'playerId' => $userId,
            'version' => $skull->getVersion(),

        ]);

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
     * @throws ORMException
     */
    #[Route('/game/{id}/player/{playerId}/playcard/{cardId}', name: 'play_card', methods: ["POST"])]
    public function playCard($id, $cardId, $playerId, Request $request): Response
    {
        $skull = $this->skullKingRepo->find($id);
        $card = $this->cardRepo->find($cardId);
        try {
            $userId = new Uuid($request->cookies->get('userid'));
            $skull->addToFold($userId, $card);
            $fold = $skull->getFold();
            $this->skullKingRepo->save($skull, true);
            $topicName = "game_topic_$id";
            $this->hub->publish(new Update(
                $topicName, json_encode([
                'status' => 'player_play_card',
                'userId' => $userId,
                'fold' => $fold->toArray(),
                'players' => $skull->getPlayers()->toArray(),
                'gamePhase' => $skull->getState(),

            ])));

            return $this->redirectToRoute('current_game', ['id' => $id]);

        } catch (OptimisticLockException $e) {

            return $this->redirectToRoute('current_game', ['id' => $id]);
        }


    }
}