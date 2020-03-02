<?php

namespace App\DataFixtures;

use App\Entity\AppointmentVisibility;
use App\Entity\UserType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppointmentVisibilityFixture extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $types = [
            UserType::Teacher(),
            UserType::Student(),
            UserType::Parent()
        ];

        foreach($types as $type) {
            $visibility = (new AppointmentVisibility())
                ->setUserType($type);

            $manager->persist($visibility);
        }

        $manager->flush();
    }
}