<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Demande;
use App\Entity\Event;
use App\Entity\Role;
use App\Entity\Tag;
use App\Entity\User;
use App\Manager\FileManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class EventFixtures extends AppFixtures implements DependentFixtureInterface
{

    private FileManager $fileManager;

    /**
     * UserFixtures constructor.
     */
    public function __construct(FileManager $fileManager)
    {
        $this->fileManager = $fileManager;
        parent::__construct();
    }

    public function load(\Doctrine\Persistence\ObjectManager $manager): void
    {
        $faker = $this->generator;
        $roleOrganisateur = $this->getReference(Role::ROLE_ORGANISATEUR);
        $roleAnimateur = $this->getReference(Role::ROLE_ANIMATEUR);
        $roleUser = $this->getReference(Role::ROLE_USER);

        $organisteurs = $manager->getRepository(User::class)->findBy(['role' => $roleOrganisateur->getId()]);
        $animateurs = $manager->getRepository(User::class)->findBy(['role' => $roleAnimateur->getId()]);
        $users = $manager->getRepository(User::class)->findBy(['role' => $roleUser->getId()]);
        $categories = $manager->getRepository(Category::class)->findAll();
        $tags = $manager->getRepository(Tag::class)->findAll();

        $evenements = [
            'Festival ElectroMania',
            'ComicWorld Convention',
            'Sommet TechTalks',
            'Gala de Charité pour le Changement',
            'Local Talent Night Showcase',
            'Spectacle DanseFusion',
            'Ateliers Écrivains en Herbe',
            'Art Explosion Festival',
            'Forum DébatSphere',
            'Fête du Livre',
            'Exposition PhotoVisions',
            'Festival CinéScreen',
            'Soirées à Thème',
            'Cuisine Workshop Series',
            'Journée Démonstration Sportive',
            'Startup Networking Event',
            'Conférence Avenir Vert',
            'Improv Theater Fest',
            'Stands Gastronomiques Gourmets',
            'Weekend Retraite Bien-être',
        ];

        foreach ($organisteurs as $organisateur) {
            // create event
            for ($i = 0; $i <= $faker->numberBetween(2,6); $i++) {
                $event = new Event();
                $event->setTitle($faker->randomElement($evenements));
                $event->setOwner($organisateur);
                $event->setStatus($faker->randomElement([Event::STATUS_NEW, Event::STATUS_IN_PROGRESS, Event::STATUS_FINISHED]));
                $event->setShortDescription($faker->realText(100).'...');
                $event->setDescription($faker->realText(520));
                $event->setDateEvent($faker->dateTimeBetween('now', '+6 months'));
                $event->setLieu($faker->address);
                $event->setSponsors($faker->company);

                // add categories
                for ($j = 0; $j <= $faker->numberBetween(1,9); $j++) {
                    $event->addCategory($faker->randomElement($categories));
                }

                // add tags
                for ($j = 0; $j <= $faker->numberBetween(1,9); $j++) {
                    $event->addTag($faker->randomElement($tags));
                }

                // add participants
                for ($j = 0; $j <= $faker->numberBetween(1,5); $j++) {
                    $event->addAnimator($faker->randomElement($users));
                }

                // add media files
                for ($j = 0; $j <= $faker->numberBetween(1,3); $j++) {
                    $path = $faker->file(__DIR__.'/../../data/sample-images', __DIR__.'/../../var/cache/dev');

                    $fileName = explode(DIRECTORY_SEPARATOR, $path);
                    $fileName = $fileName[array_key_last($fileName)];
                    $uploadedFile = new UploadedFile($path, $fileName);

                    $file = $this->fileManager->uploadFile($uploadedFile);
                    $event->addMedia($file);
                }

                $manager->persist($event);
                $manager->flush();

                // add demandes to event
                for ($k = 0; $k < 10; $k++) {

                    $animateur = $faker->randomElement($animateurs);
                    $demande = $manager->getRepository(Demande::class)->findOneBy(['event' => $event, 'user' => $animateur]);

                    if (!$demande)  {
                        $demande = new Demande();
                        $demande->setText("Je souhaiterais faire partie de l'équipe d'animation de cet événement.");
                        $demande->setStatus(1);
                        $demande->setEvent($event);
                        $demande->setUser($animateur);
                        $demande->setCreatedAt($faker->dateTimeBetween('-'.$faker->numberBetween(1, 60).' days', $event->getDateEvent()));
                        $manager->persist($demande);

                        $event->addDemande($demande);
                        $manager->flush();
                    }
                }
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
            CategoriesAndTagsFixtures::class,
        ];
    }
}