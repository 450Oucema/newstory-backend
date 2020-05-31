<?php


namespace App\Controller\Api;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Cocur\Slugify\Slugify;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ApiProduitController extends AbstractController
{
    /**
     * @var ProduitRepository
     */
    private $repository;

    public function __construct(ProduitRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/api/produits/", name="api_produits")
     */
    public function index() {
        $produits = $this->getDoctrine()->getRepository('App:Produit')->findBy(['user' => $this->getUser()]);

        return $this->json($produits, Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id','nom','prix','uuid','imageUrl','slug', 'achete']
        ]);
    }

    /**
     * @Route("/api/produits/create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator)
    {

        if ($request->request->get('achete') === "true") {
            $achete = true;
        } else {
            $achete = false;
        }

        $input = [
            'nom' => $request->request->get('nom'),
            'prix' => $request->request->get('prix'),
            'image' => $request->request->get('image'),
            'achete' => $achete,
            'liens' => json_decode($request->request->get('liens')),
            'piece' => $request->request->get('piece'),
            'commentaires' => $request->request->get('commentaires')
        ];

        $groups = new Assert\GroupSequence(['Default', 'custom']);
        $constraints = new Assert\Collection([
            'nom' => new Assert\Required(),
            'prix' => null,
            'piece' => new Assert\Required(),
            'liens' => null,
            'achete' => null,
            'image' => null,
            'commentaires' => null
        ]);

        $violations = $validator->validate($input, $constraints, $groups);

        if (0 === count($violations)) {
            $slg = new Slugify();
            $produit = new Produit();
            $produit->setUser($this->getUser());
            $produit->setNom($input['nom']);
            $produit->setPrix($input['prix']);
            $produit->setCommentaire($input['commentaires']);
            $produit->setSlug($slg->slugify($input['nom']));
            $produit->setAchete($achete);
            $produit->setUuid((Uuid::uuid4())->toString());
            $produit->setImageUrl($input['image']);
            $produit->setLiens($input['liens']);
            $produit->setDateAchat(null);
            $produit->setPiece($this->getDoctrine()->getRepository('App:Piece')->find($input['piece']));

            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();

            return $this->json('ok', Response::HTTP_OK);
        } else {
            $errors = array();
            foreach ($violations as $violation) {
                $errors = $violation->getMessage();
            }

            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @Route("api/produits/one", name="one_produit", methods={"POST"})
     * @return JsonResponse
     */
    public function one(Request $request) {
        $produit = $this->repository->findOneBy(['slug' => $request->request->get('slug'), 'user' => $this->getUser()]);

        if(!$produit) {
            return $this->json('error', Response::HTTP_NOT_FOUND);
        }

        return $this->json($produit, Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id','nom','prix','uuid','imageUrl','slug', 'achete','commentaire','liens'],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object;
            }
        ]);
    }

    /**
     * @param Request $request
     * @Route("api/produits/change", name="change_produit", methods={"POST"})
     * @return JsonResponse
     */
    public function change(Request $request)
    {
            $produit = $this->repository->findOneBy(['uuid' => $request->request->get('uuid'), 'user' => $this->getUser()]);

            if (!$produit) {
                return $this->json($request->request, Response::HTTP_NOT_FOUND);
            }

            if ($produit->getAchete() === false) {
                $produit->setAchete(true);
            } else {
                $produit->setAchete(false);
            }

            $this->getDoctrine()->getManager()->persist($produit);
            $this->getDoctrine()->getManager()->flush();

            return $this->json($produit, Response::HTTP_OK, [], [
                ObjectNormalizer::ATTRIBUTES => ['id','nom','prix','uuid','imageUrl','slug', 'achete','commentaire','liens'],
                ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                    return $object;
                }
            ]);
    }

    /**
     * @Route("/api/produits/delete/{uuid}", methods={"DELETE"})
     * @param Produit $produit
     * @return JsonResponse
     */
    public function delete(Produit $produit) {
        $this->getDoctrine()->getManager()->remove($produit);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('deleted', Response::HTTP_OK);
    }
}