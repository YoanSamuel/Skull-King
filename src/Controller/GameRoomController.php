<?php

namespace App\Controller;

use App\Entity\GameRoom;
use App\Repository\GameRoomRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameRoomController extends AbstractController
{

    private GameRoomRepository $gameRoomRepository;

    public function __construct(GameRoomRepository $gameRoomRepository)
    {

        $this->gameRoomRepository = $gameRoomRepository;
    }

    #[Route('/game/room', name: 'app_game_room', methods: ["GET"])]
    public function index(Request $request): Response
    {

        $form = $this->createFormBuilder(null, ["method" => "POST"])
                 ->add('new', SubmitType::class)
                 ->getForm();

        $allRooms = $this->gameRoomRepository->findAll();


        return $this->render('game_room/index.html.twig', [
            'controller_name' => 'GameRoomController',
            'gameRooms'       => $allRooms,
            'isEmpty'         => empty($allRooms),
            'form'             => $form->createView()
        ]);
    }

    #[Route('/game/room', name: 'new_game_room', methods: ["POST"])]
    public function new(): RedirectResponse
    {
            $gameRoom = new GameRoom();
            $gameRoom->setCreatedAt(new DateTimeImmutable());

            $this->gameRoomRepository->save($gameRoom, true);
            // redirect sur la meme page
            return $this->redirectToRoute("app_game_room");

    }





}
