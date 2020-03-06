<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user->setFirstName('Santi');
        $user->setEmail('ssabate@iesebre.com');
        $user->setPassword('12345');
        $manager->persist($user);
        $manager->flush();
    }
}
