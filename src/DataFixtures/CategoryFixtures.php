<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $travel = new Category();
        $travel->setName("Travel & Adventure");
        $this->addReference('travel', $travel);

        $sport = new Category();
        $sport->setName("Sport");
        $this->addReference('sport', $sport);

        $entertainment = new Category();
        $entertainment->setName("Entertainment");
        $this->addReference('entertainment', $entertainment);

        $humanRelations = new Category();
        $humanRelations->setName("Human Relations");
        $this->addReference('human-relations', $humanRelations);

        $others = new Category();
        $others->setName("Others");
        $this->addReference('others', $others);

        $manager->persist($travel);
        $manager->persist($sport);
        $manager->persist($entertainment);
        $manager->persist($humanRelations);
        $manager->persist($others);

        $manager->flush();
    }
}
