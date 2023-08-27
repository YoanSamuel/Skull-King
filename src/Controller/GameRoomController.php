<?php

namespace App\Controller;

use App\Controller\dto\UserDTO;
use App\Entity\Deck;
use App\Entity\GameRoom;
use App\Entity\GameRoomUser;
use App\Entity\SkullKing;
use App\Repository\GameRoomRepository;
use App\Repository\SkullKingRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class GameRoomController extends AbstractController
{

    private GameRoomRepository $gameRoomRepository;
    private HubInterface $hub;
    private SkullKingRepository $skullRepository;

    public function __construct(GameRoomRepository  $gameRoomRepository,
                                SkullKingRepository $skullRepository,
                                HubInterface        $hub)
    {
        $this->gameRoomRepository = $gameRoomRepository;
        $this->skullRepository = $skullRepository;
        $this->hub = $hub;

    }

    #[Route('/game/room', name: 'app_game_room', methods: ["GET"])]
    public function index(Request $request): Response
    {

        $form = $this->createFormBuilder(null, ["method" => "POST"])
            ->add('Jouer', SubmitType::class)
            ->getForm();

        $allRooms = $this->gameRoomRepository->findAllWithUsers();
        foreach ($allRooms as $room) {
            $room->setContainsCurrentUser(new Uuid($request->cookies->get('userid')));
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
        $user->setUserName($request->cookies->get('username'));
        $user->setUserId(new Uuid($request->cookies->get('userid')));

        $gameRoom = new GameRoom();
        $gameRoom->setCreatedAt(new DateTimeImmutable());
        $gameRoom->addUser($user);

        $this->gameRoomRepository->save($gameRoom, true);
        $topicName = "game_room_topic_" . $gameRoom->getId();
//        $this->hub->publish(new Update($topicName, json_encode([
//            'status' => 'new_game',
//            'user' => new UserDTO($user),
//            'topicName' => $topicName
//        ])));

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
    public function enterInGame(Request $request, $id)
    {
        $gameRoom = $this->gameRoomRepository->findOneBy(['id' => $id]);
        $users = $gameRoom->getUsers();
        $deck = new Deck();
        $deck->shuffle();
        $skull = new SkullKing($users, $deck);
        $skull->setCreatedAt(new DateTimeImmutable());

        $gameRoom->setSkullKing($skull);

//        $this->skullRepository->save($skull, true);
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
