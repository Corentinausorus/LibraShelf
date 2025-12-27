<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public const LIBRARIAN_REFERENCE = 'librarian_';
    public const MEMBER_REFERENCE = 'member_';

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
        $admin->setRole(Role::ADMIN);
        $manager->persist($admin);
        $this->addReference('admin', $admin);
        
        // 3 Bibliothécaires
        for ($i = 1; $i <= 3; $i++) {
            $librarian = new User();
            $librarian->setEmail("librarian{$i}@librashelf.local");
            $librarian->setNom($faker->name());
            $librarian->setPassword($this->passwordHasher->hashPassword($librarian, 'librarian123'));
            $librarian->setRole(Role::LIBRARIAN);
            $manager->persist($librarian);
            $this->addReference(self::LIBRARIAN_REFERENCE . $i, $librarian);
        }
        
        // 50 Membres
        for ($i = 1; $i <= 50; $i++) {
            $member = new User();
            $member->setEmail($faker->unique()->safeEmail());
            $member->setNom($faker->name());
            $member->setPassword($this->passwordHasher->hashPassword($member, 'member123'));
            $member->setRole(Role::MEMBER);
            $manager->persist($member);
            $this->addReference(self::MEMBER_REFERENCE . $i, $member);
        }
        
        $manager->flush();
    }
}