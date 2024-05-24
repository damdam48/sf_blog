<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use App\Entity\Article;
use App\Entity\Categorie;
use App\Repository\ArticleRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private Generator $faker;

    public function __construct(
        private UserPasswordHasherInterface $hasher,

    ) {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = (new User)
            ->setFirstName('admin')
            ->setLastName('admin')
            ->setEmail('admin@mail.com')
            ->setPassword(
                $this->hasher->hashPassword(new User, '123')
            )
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $users[] = $user;

        for ($i = 0; $i < 10; $i++) {
            $user = (new User)
                ->setFirstName("User $i")
                ->setLastName('Test')
                ->setEmail("user-$i@test.com")
                ->setPassword(
                    $this->hasher->hashPassword(new User, '123')
                );

            $manager->persist($user);
            $users[] = $user;
        }

        for ($i = 0; $i < 10; $i++) {
            $category = (new Categorie)
                ->setName($this->faker->unique()->word())
                ->setEnable(true);

            $manager->persist($category);
            $categories[] = $category;
        }

        for ($i = 0; $i < 50; $i++) {
            $article = (new Article)
                ->setTitle($this->faker->unique()->words(3, true))
                ->setUser($this->faker->randomElement($users))
                ->setContent(file_get_contents('https://loripsum.net/api/10/medium/headers/ul/dl/link'))
                ->setEnable($this->faker->boolean)
                ->setImageFile($this->uploadArticleImage())
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($this->faker->dateTimeThisYear()));

            for ($j = 0; $j < $this->faker->numberBetween(1, 4); $j++) {
                $article->addCategory($this->faker->unique()->randomElement($categories));
            }

            $manager->persist($article);
        }

        $manager->flush();
    }

    private function uploadArticleImage(): UploadedFile
    {
        // On récupère tous les chemins du dossiers images
        $files = glob(realpath(__DIR__ . '/images/') . '/*.*');

        // On sélectionne de manière aléatoire 1 chemin
        $pathFile = $files[array_rand($files)];

        // On crée un objet File
        $imageFile = new File($pathFile);

        // dd($imageFile->getPathname());
        // On crée un objet UploadedFile (simule un upload d'image via formulaire)
        $uploadedFile = new UploadedFile($imageFile, $imageFile->getFilename());

        return $uploadedFile;
    }
}
