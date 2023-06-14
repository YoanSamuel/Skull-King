<?php

namespace App\Controller;

use App\Entity\SkullKing;
use App\Repository\GameRoomRepository;
use App\Repository\SkullKingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


class SkullKingController extends AbstractController
{

    private GameRoomRepository $gameRoomRepository;
    private SkullKingRepository $skullKingRepo;


    public function __construct(GameRoomRepository  $gameRoomRepository,
                                SkullKingRepository $skullKingRepo)
    {

        $this->gameRoomRepository = $gameRoomRepository;
        $this->skullKingRepo = $skullKingRepo;
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

        return $this->render("game/index.html.twig", [
            'id' => $id,
            'announceValues' => $announceValues,
            'cards' => $currentPlayer->getCards(),
            'gamePhase' => $gamePhase
        ]);
    }


    #[Route('/game/{id}/announce/{announce}', name: 'announce_before_play_round', methods: ["POST"])]
    public function announcePli($id, $announce, Request $request): Response
    {
        $skull = $this->skullKingRepo->find($id);
        $userId = new Uuid($request->cookies->get('userid'));
        $skull->announce($userId, $announce);


        $this->skullKingRepo->save($skull, true);

        return $this->redirectToRoute('current_game',
            [
                'id' => $id,
            ]
        );

    }


    #[Route('/game/{id}/play', name: 'play_card', methods: ["POST"])]
    public function playCard($id, Request $request)
    {

        $gameRoom = $this->gameRoomRepository->findOneBy(['id' => $id]);
        $users = $gameRoom->getUsers();
        $skull = new SkullKing($users);
        $players = $skull->getPlayers();


        // Effectuer les traitements nécessaires pour jouer la carte
        foreach ($players as $player) {

            $userId = $player->getUserId();
            $card = (int)$request->cookies->get('card');

            $skull->playCard($userId, $card);

        }
//
//        // Passer au joueur suivant
//        $this->nextPlayer();
//
//        // Vérifier si c'est la fin de la manche
//        if ($this->isRoundOver()) {
//            // Résoudre la manche
//            $this->resolveRound();
//
//            // Passer au tour suivant
//            $this->nextRound();
//        }

        // Rediriger vers une autre page ou retourner une réponse JSON, selon les besoins de votre application
        return $this->redirectToRoute('current_game');
    }


}