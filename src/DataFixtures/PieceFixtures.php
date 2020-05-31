<?php

namespace App\DataFixtures;

use App\Entity\Piece;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Uuid;

class PieceFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $slugify  = new Slugify();

        for($i = 0; $i < 5; $i++) {
            $name = $faker->name;
           $piece =  new Piece();
           $piece->setNom($name);
           $piece->setUuid(Uuid::uuid());
           $piece->setSlug($slugify->slugify($name));
           $piece->setImageUrl(null);
           $piece->setUser($this->getReference('oucema'));
           $this->addReference('piece'.$i, $piece);
           $manager->persist($piece);
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            UserFixtures::class
        ];
    }
}
