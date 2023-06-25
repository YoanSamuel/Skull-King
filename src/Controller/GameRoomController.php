<?php

namespace App\Controller;

use App\Entity\GameRoom;
use App\Entity\GameRoomUser;
use App\Entity\SkullKing;
use App\Repository\GameRoomRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

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

        $allRooms = $this->gameRoomRepository->findAllWithUsers();
        foreach ($allRooms as $room) {
            $room->setContainsCurrentUser(new Uuid($request->cookies->get('userid')));
        }


        return $this->render('game_room/index.html.twig', [
            'controller_name' => 'GameRoomController',
            'gameRooms' => $allRooms,
            'isEmpty' => empty($allRooms),
            'form' => $form->createView()
        ]);
    }

    #[Route('/game/room', name: 'new_game_room', methods: ["POST"])]
    public function new(Request $request): Response
    {

        $user = new GameRoomUser();
        $user->setUserName($request->cookies->get('username'));
        $user->setUserId(new Uuid($request->cookies->get('userid')));

        $gameRoom = new GameRoom();
        $gameRoom->setCreatedAt(new DateTimeImmutable());
        $gameRoom->addUser($user);

        $this->gameRoomRepository->save($gameRoom, true);
//            $this->enterInGame($gameRoom->getId());
        return $this->redirectToRoute("waiting_game_room", ['id' => $gameRoom->getId()]);

    }

    #[Route('/game/room/{id}', name: 'join_game_room', methods: ["POST"])]
    public function enterInGameRoom(Request $request, $id): Response
    {

        $user = new GameRoomUser();
        $user->setUserName($request->cookies->get('username'));
        $user->setUserId(new Uuid($request->cookies->get('userid')));


        $currentGame = $this->gameRoomRepository->findOneBy(['id' => $id]);

        $currentGame->addUser($user);
        $this->gameRoomRepository->save($currentGame, true);


        return $this->redirectToRoute("waiting_game_room", ['id' => $currentGame->getId()]);
    }

    #[Route('/game/room/{id}', name: 'waiting_game_room', methods: ["GET"])]
    public function enterInCurrentGameRoom($id): Response
    {

        $currentGame = $this->gameRoomRepository->findOneBy(['id' => $id]);

        return $this->render("game_room/currentGame.html.twig", [
            'roomid' => $currentGame->getId(),
            'users' => $currentGame->getUsers(),
            'skullkingid' => $currentGame->getSkullKing()?->getId(),
        ]);
    }

    /**
     * @throws \Exception
     */
    #[Route('/game/room/{id}/game', name: 'enter_in_game', methods: ["POST"])]
    public function enterInGame($id)
    {
        $gameRoom = $this->gameRoomRepository->findOneBy(['id' => $id]);
        $users = $gameRoom->getUsers();
        $skull = new SkullKing($users);
        $skull->setCreatedAt(new DateTimeImmutable());


        $gameRoom->setSkullKing($skull);
        $this->gameRoomRepository->save($gameRoom, true);

        return $this->redirectToRoute('current_game',
            ['id' => $skull->getId()]);
    }

}
