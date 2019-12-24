<?php

namespace App\DataFixtures;

use App\Entity\Post;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    private const POST_COUNT = 20;
    
    private $faker;
    private $slugify;
    
    /**
     * AppFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
        $this->slugify = new Slugify();
    }
    
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->loadPost($manager);
    }
    
    /**
     * @param ObjectManager $manager
     *
     * @throws \Exception
     */
    private function loadPost(ObjectManager $manager)
    {
        for ($i = 1; $i < self::POST_COUNT; $i++) {
            $post = new Post();
            $post->setTitle($this->faker->text(100));
            $post->setSlug($this->slugify->slugify($post->getTitle()));
            $post->setBody($this->faker->text(1000));
            $manager->persist($post);
        }
        
        $manager->flush();
    }
}
