<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PostFixtures extends Fixture implements DependentFixtureInterface
{
    const NB_POSTS = 150;
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 0; $i < self::NB_POSTS; $i++) {
            $post = new Post();
            $post
                ->setTitle($faker->realText(35))
                ->setDateCreated($faker->dateTimeBetween('-2 years'))
                ->setVisible($faker->boolean(80))
                ->setContent($faker->paragraphs(6, true))
                ->setCategory(
                    $this->getReference(
                        CategoryFixtures::CATEGORY_REF_PREFIX . $faker->numberBetween(1, CategoryFixtures::NB_CATEGORIES)));
            $manager->persist($post);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            CategoryFixtures::class
        ];
    }
}
