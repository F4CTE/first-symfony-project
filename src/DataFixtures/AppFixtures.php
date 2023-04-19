<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Post;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $genereator = Factory::create();
        $populator =new Populator($genereator,$manager);
        $populator->addEntity(Category::class,20);
        $populator->addEntity(Post::class,150);

        $populator->execute();
    }
}
