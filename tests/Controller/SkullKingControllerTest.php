<?php

namespace App\Tests\Controller;

use App\Entity\SkullKing;
use App\Repository\SkullKingRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Uid\Uuid;

class SkullKingControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        static::bootKernel();

        $billId = Uuid::v4();
        $jeanId = Uuid::v4();
        $bill = $this->initializeUser('bill', $billId);
        $jean = $this->initializeUser('jean', $jeanId);

        $bill->request('POST', '/game/room');

        $response = $bill->getResponse();
        $redirectLocation = $response->headers->get('location');
        $this->assertEquals(302, $response->getStatusCode());

        $jean->request('POST', $redirectLocation);
        $response = $jean->getResponse();
        $this->assertEquals(302, $response->getStatusCode());

        $bill->request('POST', $redirectLocation . "/game");
        $response = $bill->getResponse();
        $redirectLocation = $response->headers->get('location');
        $this->assertEquals(302, $response->getStatusCode());

        $bill->request('POST', $redirectLocation . "/announce/0");
        $jean->request('POST', $redirectLocation . "/announce/0");
        $this->assertEquals(302, $bill->getResponse()->getStatusCode());
        $this->assertEquals(302, $jean->getResponse()->getStatusCode());
        $crawler = $bill->request('GET', $redirectLocation);

        /** @var SkullKingRepository $skullRepository */
        $skullRepository = static::getContainer()->get(SkullKingRepository::class);
        $id = explode("/", $redirectLocation);
        /** @var SkullKing $skull */
        $skull = $skullRepository->find($id[2]);
        $jeanPlayer = $skull->findPlayerByUserId($jeanId);
        $billPlayer = $skull->findPlayerByUserId($billId);
        $jeanCard = $jeanPlayer->getCards()[0];
        $billCard = $billPlayer->getCards()[0];
        $bill->request('POST', $redirectLocation . "/player/unused/playcard/" . $billCard);
        $jean->request('POST', $redirectLocation . "/player/unused/playcard/" . $jeanCard);

        /** @var SkullKing $skull */
        $skull = $skullRepository->find($id[2]);
        $this->assertEquals(2, $skull->getNbRound());

    }

    /**
     * @param KernelBrowser $client
     * @return KernelBrowser
     */
    public function initializeUser(string $username, Uuid $userId): KernelBrowser
    {
        /** @var KernelBrowser $client */
        $client = static::getContainer()->get('test.client');
        $usernameCookie = new Cookie('username', $username);
        $userIdCookie = new Cookie('userid', $userId->toRfc4122());
        $client->getCookieJar()->set($usernameCookie);
        $client->getCookieJar()->set($userIdCookie);

        return $client;
    }
}