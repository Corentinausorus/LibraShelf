<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        
        // 1 Admin
        $admin = new User();
        $admin->setEmail('admin@librashelf.local');
        $admin->setNom('Admin Principal');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin123'));
        $admin->setRole('ROLE_ADMIN');
        $manager->persist($admin);
        
        // 3 Bibliothécaires
        for ($i = 1; $i <= 3; $i++) {
            $librarian = new User();
            $librarian->setEmail("librarian{$i}@librashelf.local");
            $librarian->setNom($faker->name());
            $librarian->setPassword($this->passwordHasher->hashPassword($librarian, 'librarian123'));
            $librarian->setRole('ROLE_LIBRARIAN');
            $manager->persist($librarian);
        }
        
        // 50 Membres
        for ($i = 1; $i <= 50; $i++) {
            $member = new User();
            $member->setEmail($faker->unique()->safeEmail());
            $member->setNom($faker->name());
            $member->setPassword($this->passwordHasher->hashPassword($member, 'member123'));
            $member->setRole('ROLE_MEMBER');
            $manager->persist($member);
            
            if ($i <= 10) {
                $this->addReference('member_' . $i, $member);
            }
        }
        
        $manager->flush();
    }
}