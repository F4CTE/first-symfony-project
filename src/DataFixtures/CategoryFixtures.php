<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CategoryFixtures extends Fixture
{
    public const NB_CATEGORIES = 20;
    public const CATEGORY_REF_PREFIX = 'CATEGORY_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 1; $i <= self::NB_CATEGORIES; $i++) {
            $category = new Category();
            $category
                ->setName($faker->realText(15))
                ->setDescription($faker->realTextBetween(200, 500));
            $this->addReference('CATEGORY_' . $i, $category);
            $manager->persist($category);
        }

        $manager->flush();
    }
}
