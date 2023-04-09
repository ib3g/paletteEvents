<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Demande;
use App\Entity\Event;
use App\Entity\Facture;
use App\Entity\Media;
use App\Entity\Prix;
use App\Entity\Role;
use App\Entity\Tag;
use App\Entity\Ticket;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\VarDumper\Cloner\Data;

class AppFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface $passwordEncoder */


    public EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function load(ObjectManager $manager): void
    {

        // array with 10 events
        $events=[
            'event1' => [
                'title' => 'THE ANNUAL MARKETING CONFERENCE',
                'short_description' => 'Sed nam ut dolor qui repellendus iusto odit. Possimus inventore eveniet',
                'description' => 'Sed nam ut dolor qui repellendus iusto odit. Possimus inventore eveniet accusamus error amet eius aut accusantium et. Non odit consequatur repudiandae sequi ea odio molestiae. Enim possimus sunt inventore in est ut optio sequi unde.',
                'dateEvent' => new \DateTime('2023-06-01'),
                'status' => 1,
                'Lieu' => 'Downtown Conference Center, New York',
                'organisateur' => 'organisateur1',
                'animateur' => 'animator_1',
                'participant' => 'participant1',
                'categorie' => 'Sport',
                'tag' => 'Sport',
                ],
        ];
        // array with 10 different users related to entity User
        $users = [
            'organisateur_1' => [
                'fullName' => 'Brenden Legros',
                'email' => 'brenden@gmail.com',
                'password' => 'organisateur1',
                'role' => 'ROLE_ORGANISATEUR',
                'avatar' => 'organisateur1',
                'birthday' => new \DateTime('1990-01-01'),
                'centreInteret' => 'music',
                'profession' => 'organisateur',
                'isVerified' => 1,
                 ] ,
            'animateur_1' => [
                'fullName' => 'Hubert Hirthe',
                'email' => 'hubert@gmail.com',
                'password' => 'animateur1',
                'profession' => 'chanteur',
                'birthday' => new \DateTime('1994-04-01'),
                'centreInteret' => 'music',
                'role' => 'ROLE_ANIMATEUR',
                'avatar' => 'animator_1',
                'isVerified' => 1,
            ],
            'participant_1' => [
                'fullName' => 'Cole Emmerich',
                'email' => 'cole@gmail.com',
                'password' => 'participant1',
                'birthday' => new \DateTime('1980-06-11'),
                'role' => 'ROLE_PARTICIPANT',
                'profession' => 'participant',
                'centreInteret' => 'music',
                'isVerified' => 1,
            ],
            'admin_1' => [
                'fullName' => 'Jack Christiansen',
                'email' => 'admin@admin.com',
                'birthday' => new \DateTime('1999-01-21'),
                'password' => 'admin1',
                'profession' => 'admin',
                'role' => 'ROLE_ADMIN',
                'isVerified' => 1,
            ],
            'user_1' => [
                'fullName' => 'Alejandrin Littel',
                'email' => 'alejandrin@admin.com',
                'password' => 'user1',
                'birthday' => new \DateTime('1988-03-01'),
                'profession' => 'user',
                'role' => 'ROLE_USER',
                'isVerified' => 1,
            ]
        ];
        // array with 10 different categories related to events
        $categories = [
            'Sport', 'Culture', 'Musique', 'Cinéma', 'Théâtre', 'Exposition', 'Conférence', 'Spectacle', 'Festival', 'Autre',
        ];
        // array with 10 different tags related to events
        $tags = [
            'Sport', 'Culture', 'Musique', 'Cinéma', 'Théâtre', 'Exposition', 'Conférence', 'Spectacle', 'Festival', 'Gala', 'Workshop', 'Meetup', 'Hackathon', 'Show',
        ];
        $roles = [
            'ROLE_USER', 'ROLE_ADMIN',"ROLE_ORGANISATEUR","ROLE_ANIMATEUR","ROLE_PARTICIPANT"
        ];
        // array with 10 different medias related to entity Media
        $medias = [
            'media1' => [
                'real_name' => 'scene',
                'path' => 'about',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media2' => [
                'real_name' => 'audience',
                'path' => 'audience',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media3' => [
                'real_name' => 'banner1',
                'path' => 'banner1',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media4' => [
                'real_name' => 'banner2',
                'path' => 'banner2',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media5' => [
                'real_name' => 'banner4',
                'path' => 'banner4',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media6' => [
                'real_name' => 'blog1',
                'path' => 'blog1',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media7' => [
                'real_name' => 'c3',
                'path' => 'c3',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media8' => [
                'real_name' => 'conf_2',
                'path' => 'conf_2',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media9' => [
                'real_name' => 'conference',
                'path' => 'conference',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media10' => [
                'real_name' => 'hotel',
                'path' => 'hotel',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media11' => [
                'real_name' => 'localisation',
                'path' => 'localisation',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            'media12' => [
                'real_name' => 'localisation_1',
                'path' => 'localisation_1',
                'extension' => 'jpg',
                'mime_type' => 'jpg',
            ],
            // add 5 more elements with different real_name, path, extension, mime_type and size
            'animator_1' => [
                'real_name' => '1',
                'path' => '1',
                'extension' => 'png',
                'mime_type' => 'png',
            ],
            'animator_2' => [
                'real_name' => '2',
                'path' => '2',
                'extension' => 'png',
                'mime_type' => 'png',
            ],
            'animator_3' => [
                'real_name' => '3',
                'path' => '3',
                'extension' => 'png',
                'mime_type' => 'png',
            ],
            'animator_4' => [
                'real_name' => '4',
                'path' => '4',
                'extension' => 'png',
                'mime_type' => 'png',
            ],
            'organisateur1' => [
                'real_name' => '5',
                'path' => '5',
                'extension' => 'png',
                'mime_type' => 'png',
            ],
        ];
        $prices = [
           // array with 10 prices with keys
            'prix1' => [
                'type' => 'Premium',
                'somme' => 300,
                'place_max' => 200,
                'place_restantes' => 195,
            ],'prix2' => [
                'type' => 'Pro',
                'somme' => 250,
                'place_max' => 100,
                'place_restantes' => 95,
            ],'prix3' => [
                'type' => 'Standard',
                'somme' => 200,
                'place_max' => 50,
                'place_restantes' => 45,
            ],
            // add 3 other elements with somme differnt and same type
            'prix4' => [
                'type' => 'Premium',
                'somme' => 150,
                'place_max' => 50,
                'place_restantes' => 45,
            ],'prix5' => [
                'type' => 'Pro',
                'somme' => 100,
                'place_max' => 100,
                'place_restantes' => 180,
            ],'prix6' => [
                'type' => 'Standard',
                'somme' => 50,
                'place_max' => 150,
                'place_restantes' => 100,
            ],

        ];
        // array with 10 different tickets related to events
        $tickets = [
            'ticket1' => [
                'rang' => 1,
                'position' => 1,
                'code' => 'T'.rand(100, 999),
                'prix' => 'prix1',
                'facture' => 'Facture_1',
            ],'ticket2' => [
                'rang' => 2,
                'position' => 2,
                'code' => 'T'.rand(100, 999),
                'prix' => 'prix2',
                'facture' => 'Facture_2',
            ],'ticket3' => [
                'rang' => 3,
                'position' => 3,
                'code' => 'T'.rand(100, 999),
                'prix' => 'prix3',
                'facture' => 'Facture_3',
            ],
            ];
        // add 10 categories
        for ($i = 0; $i < count($categories); $i++) {
            // check if the category already exists in the database
            $category = $manager->getRepository(Category::class)->findOneBy(['name' => $categories[$i]]);
            // if the category already exists, skip it
            if (!$category) {
                $category = new Category();
                $category->setName($categories[$i]);
                $manager->persist($category);
                $this->addReference($categories[$i], $category);
            }

        }

        // add 10 tags
        for ($i = 0; $i < count($tags); $i++) {
            // check if the tag already exists in the database
            $tag = $manager->getRepository(Tag::class)->findOneBy(['name' => $tags[$i]]);
            // if the tag already exists, skip it
            if (!$tag) {
                $tag = new Tag();
                $tag->setName($tags[$i]);
                $manager->persist($tag);
                $this->addReference("tag_".$tags[$i], $tag);
            }
        }

        // add roles
        for ($i = 0; $i < count($roles); $i++) {
            // check if the role already exists in the database
            $role = $manager->getRepository(Role::class)->findOneBy(['role' => $roles[$i]]);
            // if the role already exists, skip it
            if (!$role) {
                $role = new Role();
                $role->setRole($roles[$i]);
                $manager->persist($role);
                $this->addReference($roles[$i], $role);
            }
        }

        // add 10 media files for events related to entity Media
        foreach ($medias as $key=>$m) {
            // check if the media already exists in the database
            $media = $manager->getRepository(Media::class)->findOneBy(['path' => $m['path']]);
            // if the media already exists, skip it
            if (!$media) {
                $media = new Media();
                $media->setRealName($m['real_name']);
                if(str_contains($key, 'media'))
                   $path = dirname(__DIR__, 2) . '/data/sample-images/';
                else
                    $path = dirname(__DIR__, 2) . '/data/sample-images/personnes/';

                $media->setPath($path.$m['path'].".".$m['extension']);
                $media->setExtension($m['extension']);
                $media->setMimeType($m['mime_type']);
                $random_num = mt_rand(0, 999999999999999999);
                $random_num = $random_num / 100;
                $random_num = number_format($random_num, 2, '.', '');
                $media->setSize($random_num);
                $manager->persist($media);
                $this->addReference($key, $media);
            }
        }
        // add 10 factures
        for ($i = 0; $i < 10; $i++) {
                $facture = new Facture();
                $facture->setCode("F".rand(1000,9999));
                $facture->setStatus(1);
                $facture->setCreatedAt(new \DateTime());
                $manager->persist($facture);
                $this->addReference("Facture_".$i, $facture);
        }

        // add  prix


        // add 10 users
        foreach ($users as $key=>$u) {
            // check if the user already exists in the database
            $user = $manager->getRepository(User::class)->findOneBy(['email' => $u['email']]);
            // if the user already exists, skip it
            if (!$user) {
                $prefix = str_replace('ROLE_', '', $u['role']);
                $user = new User();
                $user->setFullName($u['fullName']);
                $user->setBirthday($u['birthday']);
                $user->setEmail($u['email']);
              if(isset($u['centreInteret']))  $user->setCentreInteret($u['centreInteret']);
              if(isset($u['avatar']))  $user->setAvatar($this->getReference($u['avatar']));
                $user->setPassword(strtolower($prefix));
                if(isset($u['profession']))  $user->setProfession($u['profession']);
                $user->setRole($this->getReference($u['role']));
                $user->setIsVerified($u['isVerified']);
                $manager->persist($user);
                $this->addReference($key, $user);
            }
        }


        foreach ($prices as  $key=>$price) {
            $prix = new Prix();
            $prix->setType($price["type"]);
            $prix->setSomme($price["somme"]);
            $prix->setPlaceMax($price["place_max"]);
            $prix->setPlaceRestantes($price["place_restantes"]);
            $manager->persist($prix);
            $this->addReference($key, $prix);
        }

        // add 10 events
        foreach ($events as $key=>$e) {
            // check if the event already exists in the database
            $event = $manager->getRepository(Event::class)->findOneBy(['title' => $e['title']]);
            // if the event already exists, skip it
            if (!$event) {
                $event = new Event();
                $event->setTitle($e['title']);
                $event->setDescription($e['description']);
                $event->setShortDescription($e['short_description']);
                $event->setDateEvent($e['dateEvent']);
                $event->setStatus(1);
                $event->setLieu($e['Lieu']);
                $event->setCreatedAt(new \DateTime());
                $event->setOwner($this->getReference("organisateur_1"));
                $event->addAnimator($this->getReference("animateur_1"));
                $event->addCategory($this->getReference("Sport"));
                $event->addCategory($this->getReference("Festival"));
                $event->addTag($this->getReference("tag_Sport"));
                $event->addTag($this->getReference("tag_Festival"));
                $event->addPrix($this->getReference("prix1"));
                $event->addPrix($this->getReference("prix2"));
                $event->addPrix($this->getReference("prix3"));
                $event->addMedia($this->getReference("media1"));
                $event->addMedia($this->getReference("media2"));
                $event->addMedia($this->getReference("media3"));
                $event->addMedia($this->getReference("media4"));
                $event->addMedia($this->getReference("media5"));
                $event->addMedia($this->getReference("media6"));
                $event->addMedia($this->getReference("media7"));
                $event->addMedia($this->getReference("media8"));
                $event->addMedia($this->getReference("media9"));
                $event->addMedia($this->getReference("media10"));
                $event->addMedia($this->getReference("media11"));
                $event->addMedia($this->getReference("media12"));
                $manager->persist($event);
                $this->addReference($key, $event);
            }
        }

        // add  tickets
        foreach ($tickets as $key=>$t) {
            $ticket = new Ticket();
            $ticket->setRang($t["rang"]);
            $ticket->setPosition($t["position"]);
            $ticket->setCode($t["code"]);
            $ticket->setPrix($this->getReference($t["prix"]));
            $ticket->setUser($this->getReference("participant_1"));
            $ticket->setFacture($this->getReference($t['facture']));
            $manager->persist($ticket);
            $this->addReference($key, $ticket);
        }

        // add 10 demandes
        for ($i = 0; $i < 10; $i++) {
            $demande = new Demande();
            $demande->setText("Je suis très intéressé par l'organisation de votre événement.");
            $demande->setStatus(1);
            $demande->setEvent($this->getReference("event1"));
            $demande->setUser($this->getReference("animateur_1"));
            $demande->setCreatedAt(new \DateTime());
            $manager->persist($demande);
            $this->addReference("Demande_".$i, $demande);
        }
        $manager->flush();

    }
}
