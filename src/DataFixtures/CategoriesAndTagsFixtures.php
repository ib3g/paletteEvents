<?php

/*
 * This file is part of the Chellal  project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * Developed by Monarkit
 *
 */

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Tag;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CategoriesAndTagsFixtures extends AppFixtures implements DependentFixtureInterface
{

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        parent::__construct();
    }

    /**
     * Load data fixtures with the passed EntityManager.
     */
    public function load(ObjectManager $manager): void
    {
        // array with 10 different categories related to events
        $categories = [
            'Sport',
            'Culture',
            'Musique',
            'Cinéma',
            'Théâtre',
            'Exposition',
            'Conférence',
            'Spectacle',
            'Festival',
            'Autre',
        ];
        // array with 10 different tags related to events
        $tags = [
            'Sport',
            'Culture',
            'Musique',
            'Cinéma',
            'Théâtre',
            'Exposition',
            'Conférence',
            'Spectacle',
            'Festival',
            'Gala',
            'Workshop',
            'Meetup',
            'Hackathon',
            'Show',
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
                $manager->flush();
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
                $manager->flush();
            }
        }
    }

    public function getDependencies(): array
    {
        return [
            UserFixtures::class,
        ];
    }
}