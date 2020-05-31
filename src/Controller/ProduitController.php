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

class ProduitController extends AbstractController
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
     * @Route("/app/produits", name="produits")
     */
    public function index(): Response
    {
        $produits = $this->getDoctrine()->getRepository('App:Produit')->findBy(['achete' => false]);

        return $this->render('pages/produit/index.html.twig', [
            'produits' => $produits
        ]);
    }

    /**
     * @return Response
     * @Route("/app/produits/show/{id}", name="produit_show")
     */
    public function show(Produit $produit, Request $request): Response
    {
        $produitForm = $this->createForm(ProduitType::class, $produit);
        $produitForm->handleRequest($request);

        return $this->render('pages/produit/show.html.twig', [
            'produitForm' => $produitForm->createView()
        ]);
    }

}