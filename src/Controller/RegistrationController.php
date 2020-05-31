<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     * @param Request $request
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, ValidatorInterface $validator): Response
    {
        $input = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password')
        ];

        $groups = new Assert\GroupSequence(['Default', 'custom']);
        $constraints = new Assert\Collection([
            'email' => [new Assert\Email()],
            'password' => [new Assert\Length(['min' => 8, 'max' => 16])]
        ]);

        $violations = $validator->validate($input, $constraints, $groups);

        if (0 === count($violations)) {
            $user = new User();
            $user->setEmail($input['email']);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $input['password']
                )
            );
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->json('ok', Response::HTTP_OK);
        } else {
            return $this->json('error', Response::HTTP_BAD_REQUEST);
        }
    }
}
