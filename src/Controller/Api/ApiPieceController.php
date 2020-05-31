<?php


namespace App\Controller\Api;


use App\Entity\Piece;
use App\Entity\Produit;
use App\Repository\PieceRepository;
use App\Repository\ProduitRepository;
use Cocur\Slugify\Slugify;
use phpDocumentor\Reflection\Types\This;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File;

class ApiPieceController extends AbstractController
{
    /**
     * @var ProduitRepository
     */
    private $repository;

    public function __construct(PieceRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route("/api/pieces/", name="api_pieces")
     */
    public function index() {
        $pieces = $this->getDoctrine()->getRepository('App:Piece')->findBy(['user' => $this->getUser()]);

        return $this->json($pieces, Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id','nom','slug','uuid','imageUrl','produits' => ['nom', 'id', 'achete', 'prix', 'slug', 'uuid']],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
            return $object;
        }
        ]);
    }

    /**
     * @Route("/api/pieces/create", methods={"POST"})
     */
    public function create(Request $request, ValidatorInterface $validator)
    {

        $input = [
            'nom' => $request->request->get('nom'),
            'imageUrl' => $request->request->get('imageUrl')
        ];

        $groups = new Assert\GroupSequence(['Default', 'custom']);
        $constraints = new Assert\Collection([
            'nom' => new Assert\Required(),
            'imageUrl' => null
        ]);

        $violations = $validator->validate($input, $constraints, $groups);

        if (0 === count($violations)) {
            $slg = new Slugify();
            $piece = new Piece();
            $piece->setNom($input['nom']);
            $piece->setImageUrl($input['imageUrl']);
            $piece->setUuid((Uuid::uuid4())->toString());
            $piece->setUser($this->getUser());
            $piece->setSlug($slg->slugify($input['nom']));

            $this->getDoctrine()->getManager()->persist($piece);
            $this->getDoctrine()->getManager()->flush();

            return $this->json($piece, Response::HTTP_OK, [], [
                ObjectNormalizer::ATTRIBUTES => ['id','nom','slug','uuid','image_url']
            ]);

        } else {
            $errors = array();
            foreach ($violations as $violation) {
                $errors = $violation->getMessage().' '.$violation;
            }

            return $this->json([$errors, $input],Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @param Request $request
     * @Route("api/pieces/one", name="one_piece", methods={"POST"})
     * @return JsonResponse
     */
    public function one(Request $request) {
        $piece = $this->repository->findOneBy(['slug' => $request->request->get('slug'), 'user' => $this->getUser()]);

        if(!$piece) {
            return $this->json('error', Response::HTTP_NOT_FOUND);
        }

        return $this->json($piece, Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id','nom','slug','uuid','imageUrl','produits' => ['id','nom','prix','uuid','imageUrl','slug', 'achete']],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object;
            }
        ]);
    }

    /**
     * @Route("/api/pieces/delete/{uuid}", methods={"DELETE"})
     * @param Piece $piece
     * @return JsonResponse
     */
    public function delete(Piece $piece) {
        $this->getDoctrine()->getManager()->remove($piece);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('deleted', Response::HTTP_OK);
    }
}