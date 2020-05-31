<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Form\PieceType;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Ramsey\Uuid\Uuid;

class ProduitFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i<10; $i++) {
            $achete = random_int(0,1);
            $name = $faker->name;
            $produit = new Produit();
            $produit->setNom($name);
            $produit->setLiens([$faker->url, $faker->url]);
            $produit->setAchete($achete);
            $produit->setUuid((Uuid::uuid4())->toString());
            $produit->setSlug((new Slugify())->slugify($name));
            $produit->setUser($this->getReference('oucema'));
            $achete ? $produit->setDateAchat(new \DateTime('now')) : $produit->setDateAchat(null);
            $produit->setPiece($this->getReference('piece'.random_int(0,4)));
            $manager->persist($produit);
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [
            PieceFixtures::class
        ];
    }
}
