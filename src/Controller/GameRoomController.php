<?php

namespace App\Controller;

use App\Entity\GameRoom;
use App\Entity\SkullKing\Card;
use App\Repository\GameRoomRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameRoomController extends AbstractController
{

    private GameRoomRepository $gameRoomRepository;
    private UserRepository $userRepository;

    public function __construct(GameRoomRepository $gameRoomRepository,
                                UserRepository $userRepository)
    {

        $this->gameRoomRepository = $gameRoomRepository;
        $this->userRepository = $userRepository;
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
    public function new(): Response
    {
            $gameRoom = new GameRoom();
            $gameRoom->setCreatedAt(new DateTimeImmutable());

            $this->gameRoomRepository->save($gameRoom, true);
            $this->enterInGame($gameRoom->getId());
          return $this->redirectToRoute("current_game_room", [
                                        'id' => $gameRoom->getId()
          ]);

    }

    #[Route('/game/room/{id}', name: 'current_game_room', methods: ["GET"])]
    public function enterInGame($id): Response
    {

        $currentGame = $this->gameRoomRepository->findOneBy(['id' => $id]);
        $email ='yoansamuelfernandes@gmail.com';
        $player = $this->userRepository->findOneBy(['email' => $email]);
        $cards = new Card();
        $randomCard = $cards->getRandomCard();
        $colorRandomCard = explode('_', $randomCard);

        return $this->render('game_room/currentGame.html.twig',
                            ['currentGame' => $currentGame,
                             'randomCard' => $randomCard,
                             'userEmail' => $player->getEmail(),
                             'color' => $colorRandomCard[1]]);
    }





}
