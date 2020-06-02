<?php

namespace App\DataFixtures;

use App\Entity\TimetablePeriod;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TimetablePeriodFixtures extends Fixture {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $period = (new TimetablePeriod())
            ->setExternalId('period1')
            ->setName('Schuljahr 2019/20')
            ->setStart(
                $this->generator->dateTimeBetween('-90 days', 'now')
            )
            ->setEnd(
                $this->generator->dateTimeBetween('now', '+90 days')
            );

        $manager->persist($period);
        $manager->flush();
    }
}