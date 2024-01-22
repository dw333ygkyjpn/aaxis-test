<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use App\Factory\ProductFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        ProductFactory::createMany(10);

        $defaultUser = new User();
        $defaultUser->setUsername('aaxis')
            ->setRoles(['ROLE_ADMIN']);
        $defaultUser->setPassword($this->hasher->hashPassword($defaultUser, 'aaxis'));

        $manager->persist($defaultUser);

        $manager->flush();
    }
}
