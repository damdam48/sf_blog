<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher
    ) {
    }
    public function load(ObjectManager $manager): void
    {

        // $product = new Product();
        // $manager->persist($product);

        $user = (new User)
            ->setFirstName('admin')
            ->setLastName('admin')
            ->setEmail("admin@mail.fr")
            ->setPassword(
                $this->hasher->hashPassword(new User, '123')
            )
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);

        for ($i = 0; $i < 10; $i++) {
            $user = (new User)
                ->setFirstName("User $i")
                ->setLastName('test')
                ->setEmail("user-$i@mail.fr")
                ->setPassword(
                    $this->hasher->hashPassword(new User, '123')
                );
            $manager->persist($user);
        }

        $manager->flush();
    }
}
