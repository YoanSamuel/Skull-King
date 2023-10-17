<?php

namespace App\Tests\Controller;

use App\Entity\SkullKing;
use App\Entity\User;
use App\Repository\SkullKingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Uid\Uuid;

class SkullKingControllerTest extends WebTestCase
{

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        //In case leftover entries exist
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        // Drop and recreate tables for all entities
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }

    public function testFlow(): void
    {
        $billId = Uuid::v4();
        $jeanId = Uuid::v4();
        $bill = $this->initializeUser('bill', $billId);
        $jean = $this->initializeUser('jean', $jeanId);

        $bill->request('POST', '/game/room');

        $response = $bill->getResponse();
        $redirectLocation = $response->headers->get('location');
        print $response;
        $this->assertEquals(302, $response->getStatusCode());

        $jean->request('POST', $redirectLocation);
        $response = $jean->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        $bill->request('POST', $redirectLocation . "/game");
        $response = $bill->getResponse();
        $redirectLocation = $response->headers->get('location');

        $this->assertEquals(302, $response->getStatusCode());

        $bill->request('POST', $redirectLocation . "/announce/0");
        $jean->request('POST', $redirectLocation . "/announce/1");
        $this->assertEquals(302, $bill->getResponse()->getStatusCode());
        $this->assertEquals(302, $jean->getResponse()->getStatusCode());
        $bill->request('GET', $redirectLocation);

        /** @var SkullKingRepository $skullRepository */
        $skullRepository = static::getContainer()->get(SkullKingRepository::class);
        $id = explode("/", $redirectLocation);
        /** @var SkullKing $skull */
        $skull = $skullRepository->find($id[2]);
        $jeanPlayer = $skull->findPlayerByUserId($jeanId);
        $billPlayer = $skull->findPlayerByUserId($billId);
        $jeanCard = $jeanPlayer->getCards()->first()->getId();
        $billCard = $billPlayer->getCards()->first()->getId();

        $bill->request('POST', $redirectLocation . "/player/unused/playcard/" . $billCard);
        $jean->request('POST', $redirectLocation . "/player/unused/playcard/" . $jeanCard);

        $jean->request('GET', '/api/game/' . $id[2]);

        $jsonSkull = json_decode($jean->getResponse()->getContent(), true);
        $this->assertEquals(2, $jsonSkull["roundNumber"]);
    }

    /**
     * @param string $username
     * @param Uuid $userId
     * @return KernelBrowser
     */
    public function initializeUser(string $username, Uuid $userId): KernelBrowser
    {
        /** @var KernelBrowser $client */
        $client = static::getContainer()->get('test.client');
        $client->insulate();

        /** @var UserRepository $userRepository */
        $userRepository = static::getContainer()->get(UserRepository::class);

        $user = new User();
        $user->setEmail($username . "@gmail.com");
        $user->setName($username);
        $user->setUuid($userId);
        $user->setPassword("toto");

        $userRepository->save($user);

        $client->loginUser($user);

        return $client;
    }
}