<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $n = 10;
        $faker = Factory::create('fr_FR');
        for ($i = 0; $i < $n; $i++) {
            $user = new User();
            $user->setNom($faker->name());
            $user->setEmail($faker->email());
            $user->setPassword(password_hash('password', PASSWORD_BCRYPT));
            $user->setRole([Role::ROLE_USER]);
            $manager->persist($user);
        }
        $manager->flush();
    }
}
