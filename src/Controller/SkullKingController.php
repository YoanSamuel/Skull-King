<?php

namespace App\Controller;

use App\Controller\dto\CardDTO;
use App\Controller\dto\PlayerDTO;
use App\Entity\Card;
use App\Entity\Player;
use App\Repository\SkullKingRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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

    public function __construct(SkullKingRepository    $skullKingRepo,
                                HubInterface           $hub,
                                EntityManagerInterface $em)
    {
        $this->skullKingRepo = $skullKingRepo;
        $this->hub = $hub;
        $this->em = $em;

    }


    #[Route('/game/{id}', name: 'current_game', methods: ["GET"])]
    public function currentGame($id, Request $request, $error = false): Response
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
            'cards' => array_map(function (Card $card) {
                return new CardDTO($card);
            }, $currentPlayer->getCards()->toArray()),
            'gamePhase' => $gamePhase,
            'fold' => array_map(function (Card $card) {
                return new CardDTO($card);
            }, $fold),
            'players' => array_map(function (Player $player) {
                return new PlayerDTO($player);
            }, $skull->getPlayers()->toArray()),
            'topicName' => $topicName,
            'currentUserId' => $userId,
            'version' => $skull->getVersion(),
            'error' => $error

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
            $this->em->lock($skull, LockMode::OPTIMISTIC, 789);

            $userId = new Uuid($request->cookies->get('userid'));
            $skull->announce($userId, $announce);

            $this->skullKingRepo->updateWithVersionning($skull);
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

            $error = true;
            return $this->redirectToRoute('current_game', ['id' => $id, 'error' => $error]);
        }


    }


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    #[Route('/game/{id}/play/{card}', name: 'play_card', methods: ["POST"])]
    public function playCard($id, $card, Request $request)
    {
        $skull = $this->skullKingRepo->find($id);
        $userId = new Uuid($request->cookies->get('userid'));
        $skull->playCard($userId, $card);

        $this->skullKingRepo->updateWithVersionning($skull);
        return $this->redirectToRoute('current_game', ['id' => $id]);
    }


}