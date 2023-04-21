<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

  class LoginController extends AbstractController
  {
      #[Route('/login', name: 'app_login')]
      public function index(AuthenticationUtils $authenticationUtils, UserRepository $userRepo): Response
      {

//          $user = new User();
//          $user->setEmail('yoansamuelfernandes@gmail.com');
//          $user->setPassword('totololo');
//          $userRepo->save($user);

                  // get the login error if there is one
          $error = $authenticationUtils->getLastAuthenticationError();

          // last username entered by the user
          //
          $lastUsername = $authenticationUtils->getLastUsername();

         $this->enterInApp();
//
              return $this->render('login/index.html.twig', [
                               'last_username' => $lastUsername,
                               'error'         => $error,
              ]);
      }

      public function enterInApp() : RedirectResponse
      {
          return $this->redirectToRoute('app_game_room');
      }
  }
