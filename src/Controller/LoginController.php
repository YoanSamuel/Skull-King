<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class LoginController extends AbstractController
  {

      #[Route('/', name: 'showlogin', methods: ["GET", "POST"])]
      public function newUser(Request $request): Response
      {
          if($request->cookies->has('userid')) {

              return $this->redirectToRoute('app_game_room');

          }

          $form = $this->createFormBuilder(null, ["method" => "POST"])

              ->add('name', TextType::class)
              ->add('new', SubmitType::class)
              ->getForm();


          $form->handleRequest($request);
          if ($form->isSubmitted() && $form->isValid()) {
              // $form->getData() holds the submitted values
              // but, the original `$task` variable has also been updated
              $login = $form->getData();

              // ... perform some action, such as saving the task to the database
              $response = $this->redirectToRoute('app_game_room');
              $response->headers->setCookie(Cookie::create('username', $login['name']));
              $response->headers->setCookie(Cookie::create('userid', Uuid::v4()));

              return $response;
          }

          return $this->render('login/index.html.twig', [
              'form'             => $form->createView(),
              'error' => null,
              'login' => null
          ]);
      }


  }
