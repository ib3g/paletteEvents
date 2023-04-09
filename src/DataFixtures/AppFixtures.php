<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    /** @var \Faker\Generator */
    protected $generator;

    public function __construct()
    {
        $this->generator = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        // TODO: Implement load() method.
    }
}
