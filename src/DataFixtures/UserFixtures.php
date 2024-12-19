<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())
            ->setFirstname('Kadem')
            ->setLastname('Dev')
            ->setEmail('kadem@dev.fr')
            ->setRoles(['ROLE_ADMIN'])
            ->setAddress('3 Rue de Lyon')
            ->setCity('Lyon')
            ->setZip('69000')
            ->setPhone('0606060606')
            ->setGender('H')
            ->setEnabled(true)
            ->setTheme('light');

        $user->setPassword($this->passwordHasher->hashPassword($user, 'kadem'));
        $manager->persist($user);
        $manager->flush();
    }
}
