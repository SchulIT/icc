<?php

namespace App\DataFixtures;

use App\Entity\TimetablePeriod;
use App\Entity\UserTypeEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TimetablePeriodFixtures extends Fixture implements DependentFixtureInterface {

    public function __construct(private Generator $generator)
    {
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

        $userTypes = $manager->getRepository(UserTypeEntity::class)
            ->findAll();

        foreach($userTypes as $type) {
            $period->addVisibility($type);
        }

        $manager->persist($period);
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            UserTypeFixtures::class
        ];
    }
}