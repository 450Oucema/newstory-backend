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
        return $this->render('pages/home.html.twig');
    }

    /**
     * @return JsonResponse
     * @Route("/api/test", name="apitest")
     */
    public function test(): JsonResponse
    {
        $data = [
            'username' => $this->getUser()->getUsername()
        ];

        return new JsonResponse($data);
    }
}