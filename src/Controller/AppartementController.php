<?php

namespace App\Controller;
use App\Entity\Piece;
use App\Entity\Produit;
use App\Form\PieceType;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AppartementController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em  = $em;
    }

    /**
     * @return Response
     * @Route("/app/appartement", name="appartement")
     */
    public function index(): Response
    {
        $pieces = $this->getDoctrine()->getRepository('App:Piece')->findAll();

        return $this->render('pages/piece/index.html.twig', [
            'pieces' => $pieces
        ]);
    }

    /**
     * @return Response
     * @Route("/app/appartement/piece/{slug}", name="piece")
     */
    public function piece(Piece $piece): Response
    {
        return $this->render('pages/piece/show.html.twig', [
            'piece' => $piece
        ]);
    }

}