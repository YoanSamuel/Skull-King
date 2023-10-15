<?php

namespace App\Controller;

use App\Controller\dto\UserDTO;
use App\Entity\Deck;
use App\Entity\GameRoom;
use App\Entity\GameRoomUser;
use App\Entity\SkullKing;
use App\Repository\GameRoomRepository;
use App\Security\UserService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;


class GameRoomController extends AbstractController
{

    private GameRoomRepository $gameRoomRepository;
    private HubInterface $hub;
    private UserService $userService;

    public function __construct(GameRoomRepository $gameRoomRepository,
                                HubInterface       $hub,
                                UserService        $userService)
    {
        $this->gameRoomRepository = $gameRoomRepository;
        $this->hub = $hub;
        $this->userService = $userService;
    }

    #[Route('/game/room', name: 'app_game_room', methods: ["GET"])]
    public function index(): Response
    {

        $form = $this->createFormBuilder(null, ["method" => "POST"])
            ->add('Jouer', SubmitType::class)
            ->getForm();


        $userId = $this->userService->getUser()->getUuid();
        $allRooms = $this->gameRoomRepository->findAllAvailable($userId);
        foreach ($allRooms as $room) {
            $room->setContainsCurrentUser($userId);
        }

        return $this->render('game_room/index.html.twig', [
            'controller_name' => 'GameRoomController',
            'gameRooms' => $allRooms,
            'isEmpty' => empty($allRooms),
            'form' => $form->createView(),
        ]);
    }

    #[Route('/game/room', name: 'new_game_room', methods: ["POST"])]
    public function new(Request $request): Response
    {

        $user = new GameRoomUser();

        $authenticated = $this->userService->getUser();
        $user->setUserName($authenticated->getName());
        $user->setUserId($authenticated->getUuid());

        $gameRoom = new GameRoom();
        $gameRoom->setCreatedAt(new DateTimeImmutable());
        $gameRoom->addUser($user);

        $this->gameRoomRepository->save($gameRoom, true);

        return $this->redirectToRoute("waiting_game_room", ['id' => $gameRoom->getId()]);

    }

    #[Route('/game/room/{id}', name: 'join_game_room', methods: ["POST"])]
    public function enterInGameRoom($id): Response
    {

        $user = new GameRoomUser();

        $authenticated = $this->userService->getUser();
        $user->setUserName($authenticated->getName());
        $user->setUserId($authenticated->getUuid());

        $currentGame = $this->gameRoomRepository->findOneBy(['id' => $id]);

        $currentGame->addUser($user);
        $this->gameRoomRepository->save($currentGame, true);
        $topicName = "game_room_topic_$id";
        $this->hub->publish(new Update($topicName, json_encode([
            'status' => 'player_joined',
            'user' => new UserDTO($user)
        ])));


        return $this->redirectToRoute("waiting_game_room", ['id' => $currentGame->getId()]);
    }

    #[Route('/game/room/{id}', name: 'waiting_game_room', methods: ["GET"])]
    public function enterInCurrentGameRoom($id): Response
    {
        $currentGame = $this->gameRoomRepository->findOneBy(['id' => $id]);
        $topicName = "game_room_topic_$id";
        return $this->render("game_room/currentGame.html.twig", [
            'roomid' => $currentGame->getId(),
            'users' => array_map(function (GameRoomUser $user) {
                return new UserDTO($user);
            }, $currentGame->getUsers()->toArray()),
            'skullkingid' => $currentGame->getSkullKing()?->getId(),
            'topicName' => $topicName

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
        $deck = new Deck();
        $deck->shuffle();
        $skull = new SkullKing($users, $deck);
        $skull->setCreatedAt(new DateTimeImmutable());

        $gameRoom->setSkullKing($skull);

        $this->gameRoomRepository->save($gameRoom, true);
        $response = $this->redirectToRoute('current_game', ['id' => $skull->getId()]);
        $topicName = "game_room_topic_$id";
        $this->hub->publish(new Update(
            $topicName, json_encode([
            'status' => 'game_started',
            'game_url' => $response->getTargetUrl()
        ])));


        return $response;
    }

}
