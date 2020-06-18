<?php


namespace App\Controller\Api;

use App\Entity\Alert;
use App\Entity\FriendRequest;
use App\Repository\FriendRequestRepository;
use App\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ApiUserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var FriendRequestRepository
     */
    private $friendRequestRepository;

    public function __construct(UserRepository $repository, FriendRequestRepository $friendRequestRepository)
    {
        $this->repository = $repository;
        $this->friendRequestRepository = $friendRequestRepository;
    }

    /**
     * @return Response
     * @Route("/api/user/profile", name="api_user_profile", methods={"GET"})
     */
    public function index(): Response {

        return $this->json($this->getUser(), Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id','email','profilePictureUrl','private','privateForFriends', 'friends' => ['email']],
            ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object;
            }
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     * @Route("/api/user/profile/edit", name="api_user_profile_edit", methods={"POST"})
     */
    public function edit(Request $request, ValidatorInterface $validator): Response {

        $user = $this->getUser();

        $input = [
            'email' => $request->request->get('email'),
            'private' => $request->request->get('private'),
            'privateForFriends' => $request->request->get('privateForFriends'),
            'profilePictureUrl' => $request->request->get('profilePictureUrl'),
        ];

        $groups = new Assert\GroupSequence(['Default', 'custom']);
        $constraints = new Assert\Collection([
            'email' => [new Assert\Required(), new Assert\Email()],
            'private' => new Assert\Required(),
            'privateForFriends' => new Assert\Required(),
            'profilePictureUrl' => null,
        ]);

        $violations = $validator->validate($input, $constraints, $groups);

        if (0 === count($violations)) {

            if ($request->request->get('private') === "true") {
                $privateAccount = true;
            } else {
                $privateAccount = false;
            }

            if ($request->request->get('privateForFriends') === "true") {
                $privateForFriends = true;
            } else {
                $privateForFriends = false;
            }

            $user->setEmail($input['email']);
            $user->setProfilePictureUrl($input['profilePictureUrl']);
            $user->setPrivate($privateAccount);
            $user->setPrivateForFriends($privateForFriends);

            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->flush();

            return $this->json($request->request, Response::HTTP_OK, [], [
            ]);
        } else {
            $errors = [];
            foreach ($violations as $violation) {
                 array_push($errors, $violation->getMessage());
            }

            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @Route("/api/user/search", name="api_user_search", methods={"POST"})
     */
    public function search(Request $request) {

       $results = $this->repository->search($request->request->get('search'));

       return $this->json(['search' => $results], Response::HTTP_OK, [], [
           ObjectNormalizer::CIRCULAR_REFERENCE_HANDLER => function($object) {
           return $object;
           }
       ]);
    }

    /**
     * @Route("/api/user/alerts", name="api_user_alerts", methods={"GET"})
     */
    public function alerts()
    {
        $friendRequests = $this->friendRequestRepository->findAlerts($this->getUser());
        $alerts = $this->getDoctrine()->getRepository('App:Alert')->findBy(['user' => $this->getUser()]);

        $alertsRender = $alerts;
        if(!$friendRequests && !$alerts) {
            return $this->json(null, Response::HTTP_OK);
        }

        foreach ($alerts as $alert) {
            $alert->setSeen(true);
            $this->getDoctrine()->getManager()->persist($alert);
        }
        $this->getDoctrine()->getManager()->flush();

        return $this->json([
            'alerts' => $alertsRender,
            'friendRequests' => $friendRequests
        ], Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['uuid', 'content', 'created_at','seen', 'icon']
        ]);
    }

    /**
     * @Route("/api/user/one", name="api_user_one", methods={"POST"})
     */
    public function one(Request $request) {
        $isFriend = false;
        $isAsked = false;
        $user = $this->repository->findOneBy(['email' => $request->request->get('email')]);

        if(!$user) {
            return new Response('user not found', Response::HTTP_NOT_FOUND);
        }

        // Si l'utilisateur est le même que celui qui demande
        if($user->getUuid() === $this->getUser()->getUuid()) {
            return $this->json(['isMe' => true], Response::HTTP_OK);
        }

        if($user->isFriend($this->getUser())) {
            $isFriend = true;
        }

        if ($this->friendRequestRepository->findOneBy(['user_asking' => $this->getUser(), 'user_responding' => $user])) {
            $isAsked = true;
        }

        if($user->getPrivate() === true && $user->getPrivateForFriends() === true && $isFriend) {
            return $this->json(['user' => $user, 'isFriend' => true], Response::HTTP_OK, [], [
                ObjectNormalizer::ATTRIBUTES => ['id','email', 'profilePictureUrl','uuid', 'pieces' => ['nom','uuid', 'produits' => ['nom', 'prix']]]
            ]);
        }

        if($user->getPrivate() === false) {
            return $this->json(['user' => $user, 'isFriend' => $isFriend, 'isAsked' => $isAsked], Response::HTTP_OK, [], [
                ObjectNormalizer::ATTRIBUTES => ['id','email','uuid', 'profilePictureUrl', 'pieces' => ['nom','uuid', 'produits' => ['nom', 'prix']]]
            ]);
        }

        return new Response('private user', Response::HTTP_NOT_FOUND);
    }

    /**
     * @Route("/api/user/friendrequest", name="api_user_friendrequest", methods={"POST"})
     */
    public function friendRequest(Request $request) {
        $userAsked = $this->repository->findOneBy(['email' => $request->request->get('email')]);

        if(!$userAsked) {
            return $this->json('Utilisateur non trouvé.', Response::HTTP_NOT_FOUND);
        }

        if ($this->friendRequestRepository->findOneBy(['user_asking' => $this->getUser(), 'user_responding' => $userAsked])) {
            return $this->json('already asked', Response::HTTP_NOT_FOUND);
        }

        $fRequest = new FriendRequest();
        $fRequest->setUuid((Uuid::uuid4())->toString());
        $fRequest->setUserAsking($this->getUser());
        $fRequest->setUserResponding($userAsked);

        $this->getDoctrine()->getManager()->persist($fRequest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('success', Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/addfriend", name="api_user_addfriend", methods={"POST"})
     */
    public function addFriend(Request $request) {
        $user = $this->getUser();
        $frRequest = $this->friendRequestRepository->findOneBy(['uuid' => $request->request->get('uuid'), 'user_responding' => $user]);

        if(!$frRequest) {
            return $this->json('error', Response::HTTP_NOT_FOUND);
        }

        $userAsking = $frRequest->getUserAsking();

        $response = $request->request->get('accepted');
        $frRequest->setAccepted($response);
        $frRequest->setResponseDate(new \DateTime('now'));

        if ($response) {
            $user->addFriend($userAsking);
            $userAsking->addFriend($user);

            $alert = new Alert();
            $alert->setUuid((Uuid::uuid4())->toString());
            $alert->setUser($userAsking);
            $alert->setIcon(Alert::ICON_ADD_USER);
            $alert->setContent($user->getEmail().' a accepté votre demande d\'amis');
            $this->getDoctrine()->getManager()->persist($user);
            $this->getDoctrine()->getManager()->persist($alert);
            $this->getDoctrine()->getManager()->persist($userAsking);
        }

        $this->getDoctrine()->getManager()->persist($frRequest);
        $this->getDoctrine()->getManager()->flush();

        return $this->json([$response], Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/removefriend", name="api_user_removefriend", methods={"POST"})
     */
    public function removefriend(Request $request) {
        $currentUser = $this->getUser();
        $userRemoved = $this->repository->findOneBy(['uuid' => $request->request->get('uuid')]);

        if(!$userRemoved) {
            return $this->json('error', Response::HTTP_NOT_FOUND);
        }

        $currentUser->removeFriend($userRemoved);
        $userRemoved->removeFriend($currentUser);

        $this->getDoctrine()->getManager()->persist($userRemoved);
        $this->getDoctrine()->getManager()->persist($currentUser);
        $this->getDoctrine()->getManager()->flush();

        return $this->json('success', Response::HTTP_OK);
    }

    /**
     * @Route("/api/user/friends", name="api_user_friends", methods={"GET"})
     */
    public function friends() {
        $friends = $this->getUser()->getFriends();

        return $this->json($friends, Response::HTTP_OK, [], [
            ObjectNormalizer::ATTRIBUTES => ['id', 'email', 'uuid']
        ]);
    }
}