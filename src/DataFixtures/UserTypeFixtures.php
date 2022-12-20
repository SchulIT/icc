<?php

namespace App\DataFixtures;

use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserTypeFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        foreach(UserType::cases() as $type) {
            $manager->persist((new UserTypeEntity())->setUserType($type));
        }
        $manager->flush();
    }
}