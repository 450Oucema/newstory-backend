<?php

namespace App\Controller;
use App\Entity\Piece;
use App\Entity\Produit;
use App\Form\PieceType;
use App\Form\ProduitType;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
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
     * @return JsonResponse
     * @Route("/api/test", name="apitest")
     */
    public function index(): JsonResponse
    {
        $data = [
            'username' => $this->getUser()->getUsername()
        ];

        return new JsonResponse($data);
    }

    /**
     * @return Response
     * @Route("/app/creations", name="creations")
     */
    public function creations(Request $request): Response
    {
        $slugify = new Slugify();
        $produit = new Produit();
        $piece = new Piece();
        $produit->setLiens([]);
        $produitForm = $this->createForm(ProduitType::class, $produit);
        $pieceForm = $this->createForm(PieceType::class, $piece);
        $produitForm->handleRequest($request);
        $pieceForm->handleRequest($request);

        if($produitForm->isSubmitted() && $produitForm->isValid()) {
            $this->em->persist($produit);
            $this->em->flush();

            return $this->redirectToRoute('creations');
        }

        if($pieceForm->isSubmitted() && $pieceForm->isValid()) {

            $piece->setSlug($slugify->slugify($piece->getNom()));

            $this->em->persist($piece);
            $this->em->flush();

            return $this->redirectToRoute('creations');
        }

        return $this->render('pages/creation.html.twig', [
            'produitForm' => $produitForm->createView(),
            'pieceForm' => $pieceForm->createView(),
        ]);
    }

    /**
     * @Route("/switchmode", name="switchmode")
     */
    public function switch(Request $request) {

    }
}