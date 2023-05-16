<?php

namespace App\DataFixtures;

use App\Entity\Role;
use App\Entity\User;
use App\Manager\FileManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends AppFixtures
{
    private UserPasswordHasherInterface $userPasswordHasher;
    private FileManager $fileManager;

    /**
     * UserFixtures constructor.
     */
    public function __construct(UserPasswordHasherInterface $userPasswordHasher, FileManager $fileManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->fileManager = $fileManager;
        parent::__construct();
    }

    /**
     * Load data fixtures with the passed EntityManager.
     */
    public function load(ObjectManager $manager): void
    {
        $this->createUsers($manager);
    }

    public function createUsers(ObjectManager $manager)
    {
        $roles = Role::ROLES;

        $centres_interet = [
            'Festivals électronique',
            'Salons BD',
            'Conférences technologie',
            'Volontariat caritatif',
            'Concerts locaux',
            'Concerts classiques',
            'Concerts',
            'Danse contemporaine',
            'Ateliers écriture',
            'Foires d\'art',
            'Débats d\'actualité',
            'Salons littéraires',
            'Expositions photographie',
            'Projections cinématographiques',
            'Soirées thématiques',
            'Ateliers cuisine',
            'Démonstrations sportives',
            'Rencontres startup',
            'Conférences environnement',
            'Théâtre improvisation',
            'Stands gastronomiques',
            'Séminaires bien-être'
        ];

        $professions = [
            'Organisateur d\'événements',
            'Régisseur général',
            'Directeur artistique',
            'Ingénieur du son',
            'Scénographe',
            'Coordinateur de bénévoles',
            'Chargé de communication',
            'Animateur',
            'Éclairagiste',
            'Technicien vidéo',
            'Chanteur',
            'Musicien',
            'DJ',
            'Acteur',
            'Danseur',
            'Peintre',
            'Sculpteur',
            'Photographe',
            'Réalisateur',
            'Écrivain',
        ];

        $faker = $this->generator;

        // Principals users
        foreach ($roles as $key => $value) {
            $prefix = str_replace('ROLE_', '', $key);
            $userInterest = $faker->randomElements($centres_interet, $faker->numberBetween(1, 8));

            $role = new Role();
            $role->setRole($key);

            $manager->persist($role);

            $email = static::buildEmail($key);

            $user = new User();
            $user->setFullName($faker->name);
            $user->setCentreInteret(implode(', ', $userInterest));
            $user->setProfession($faker->randomElement($professions));
            $user->setEmail($email);
            $user->setBirthday($faker->dateTimeBetween('-45 years', '-18 years'));
            $user->setIsVerified(true);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, strtolower($prefix)));
            $user->setRole($role);

            $user->setFacebook('https://facebook.com/' . strtolower($prefix));
            $user->setTwitter('https://twitter.com/' . strtolower($prefix));
            $user->setLinkedin('https://linkedin.com/in/' . strtolower($prefix));

            $this->generateImage($user);

            $role->addUser($user);

            $manager->persist($user);

            $this->addReference($key, $role);
            $this->addReference($email, $user);
        }

        // Random users
        for ($i = 0; $i <= 16; $i++) {
            $userInterest = $faker->randomElements($centres_interet, $faker->numberBetween(1, 8));

            $role = $this->getReference($faker->randomElement([Role::ROLE_ORGANISATEUR, Role::ROLE_ANIMATEUR, Role::ROLE_USER]));

            $user = new User();
            $user->setFullName($faker->name);
            $user->setCentreInteret(implode(', ', $userInterest));
            $user->setProfession($faker->randomElement($professions));
            $user->setEmail($faker->email);
            $user->setBirthday($faker->dateTimeBetween('-45 years', '-18 years'));
            $user->setIsVerified(true);
            $user->setPassword($this->userPasswordHasher->hashPassword($user, 'user123'));
            $user->setRole($role);

            $this->generateImage($user);

            $user->setFacebook('https://facebook.com/' . $faker->firstName);
            $user->setTwitter('https://twitter.com/' . $faker->firstName);
            $user->setLinkedin('https://linkedin.com/in/' . $faker->firstName);

            $role->addUser($user);
            $manager->persist($user);
        }

        $manager->flush();
    }

    private function generateImage($user) {
        $path = $this->generator->file(__DIR__.'/../../data/sample-user-images', __DIR__.'/../../var/cache/dev');

        $fileName = explode(DIRECTORY_SEPARATOR, $path);
        $fileName = $fileName[array_key_last($fileName)];
        $uploadedFile = new UploadedFile($path, $fileName);

        $file = $this->fileManager->uploadFile($uploadedFile);
        $user->setAvatar($file);
    }

    public static function buildEmail(string $role)
    {
        $prefix = str_replace('ROLE_', '', $role);
        $email = strtolower($prefix) . '@paletteEvents.com';

        return $email;
    }
}
