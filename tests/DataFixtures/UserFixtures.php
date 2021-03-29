<?php

namespace App\Tests\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $userTurbo = new User();
        $userDefault = new User();
        $userTurbo
            ->setName('turbo')
            ->setPassword('test')
            ->setRoles(['ROLE_ADMIN']);
        $userDefault
            ->setName('user')
            ->setPassword('test');
        $manager->persist($userTurbo);
        $manager->persist($userDefault);
        $manager->flush();
    }
}
