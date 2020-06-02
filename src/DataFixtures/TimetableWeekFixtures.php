<?php

namespace App\DataFixtures;

use App\Entity\TimetableWeek;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TimetableWeekFixtures extends Fixture {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $manager->persist(
            (new TimetableWeek())
            ->setKey('A')
            ->setDisplayName('A-Woche')
            ->setWeekMod(1)
        );

        $manager->persist(
            (new TimetableWeek())
            ->setKey('B')
            ->setDisplayName('B-Woche')
            ->setWeekMod(0)
        );

        $manager->flush();
    }
}