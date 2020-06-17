<?php

namespace App\Controller;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $users = $this->getDoctrine()->getRepository('App:User')->findAll();
        return $this->render('pages/home.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @return JsonResponse
     * @Route("/api/test", name="apitest")
     */
    public function test(): JsonResponse
    {
        $data = [
            'username' => $this->getUser()->getUsername(),
            'notifications' => $this->getDoctrine()->getRepository('App:Alert')->count(['user' => $this->getUser(), 'seen' => false])
        ];

        return new JsonResponse($data);
    }
}