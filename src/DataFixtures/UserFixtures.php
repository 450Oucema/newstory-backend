<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail('oucema45@gmail.com');
        $user->setRoles(["ROLE_ADMIN"]);
        $user->setPassword($this->encoder->encodePassword($user,'oucemahe10'));
        $user->setPrivate(false);
        $user->setPrivateForFriends(false);
        $user->setUuid((Uuid::uuid4())->toString());
        $user->setCreatedAt(new \DateTime('now'));
        $this->setReference('oucema', $user);

        $manager->persist($user);
        $manager->flush();
    }
}