<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setEmail("toto@yahoo.com");
        $user->setPassword('$2y$10$RPoHXaniDyEi9lryXSaqP.5tX1phX9MWkSijROo.4xitH/WlQZYXa');
        $user->setUsername("Toto");

        $this->addReference('user', $user);

        $manager->persist($user);
        $manager->flush();
    }
}
