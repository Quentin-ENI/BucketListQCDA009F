<?php

namespace App\DataFixtures;

use App\Entity\Wish;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class WishFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        $categoryIndexes = [
            'travel',
            'sport',
            'entertainment',
            'human-relations',
            'others'
        ];

        for ($index = 0; $index < 50; $index++) {
            $wish = new Wish();
            $wish->setTitle($faker->realText(20));
            $wish->setDescription($faker->realText(200));
//            $wish->setAuthor($faker->name());
            $wish->setIsPublished($faker->boolean(50));
            $wish->setDateCreated($faker->dateTimeBetween('-40 days', '-30 days'));
            $wish->setDateUpdated($faker->dateTimeBetween('-20 days', '-10 days'));
            $wish->setCategory($this->getReference($categoryIndexes[array_rand($categoryIndexes)]));
            $manager->persist($wish);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class];
    }
}
