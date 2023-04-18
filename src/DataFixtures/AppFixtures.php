<?php

namespace App\DataFixtures;

use App\Entity\Post;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const NB_ARTICLES = 50;
    public function load(ObjectManager $manager): void
    {
        for($i = 0; $i < self::NB_ARTICLES; $i++) {

            $faker = Factory::create();

            $post = new Post();
            $post
                ->setTitle($faker->realText(35))
                ->setDateCreated($faker->dateTimeBetween('-2 years'))
                ->setVisible($faker->boolean(80))
                ->setContent($faker->realTextBetween(200,500));
            $manager->persist($post);
        }
        $manager->flush();
    }
}
