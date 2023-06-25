<?php

namespace App\Controller;

use App\Repository\SkullKingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;


class SkullKingController extends AbstractController
{

    private SkullKingRepository $skullKingRepo;


    public function __construct(SkullKingRepository $skullKingRepo)
    {
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
        $fold = [];

        return $this->render("game/index.html.twig", [
            'id' => $id,
            'announceValues' => $announceValues,
            'cards' => $currentPlayer->getCards(),
            'gamePhase' => $gamePhase,
            'fold' => $fold
        ]);
    }


    #[Route('/game/{id}/announce/{announce}', name: 'announce_before_play_round', methods: ["POST"])]
    public function announce($id, $announce, Request $request): Response
    {
        $skull = $this->skullKingRepo->find($id);
        $userId = new Uuid($request->cookies->get('userid'));
        $skull->announce($userId, $announce);

        $this->skullKingRepo->save($skull, true);

        return $this->redirectToRoute('current_game', ['id' => $id,]);
    }


    #[Route('/game/{id}/play/{card}', name: 'play_card', methods: ["POST"])]
    public function playCard($id, $card, Request $request)
    {
        $skull = $this->skullKingRepo->find($id);
        $userId = new Uuid($request->cookies->get('userid'));
        $skull->playCard($userId, $card);

        $this->skullKingRepo->save($skull, true);
        return $this->redirectToRoute('current_game', ['id' => $id]);
    }


}