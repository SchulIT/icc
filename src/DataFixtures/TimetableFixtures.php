<?php

namespace App\DataFixtures;

use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableWeek;
use App\Entity\Tuition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

class TimetableFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;
    private $roomProvider;

    public function __construct(Generator $generator, RoomGenerator $roomProvider) {
        $this->generator = $generator;
        $this->roomProvider = $roomProvider;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $weekA = $manager->getRepository(TimetableWeek::class)
            ->findOneBy([
                'key' => 'A'
            ]);

        $weekB = $manager->getRepository(TimetableWeek::class)
            ->findOneBy([
                'key' => 'B'
            ]);

        $period = $manager->getRepository(TimetablePeriod::class)
            ->findOneBy([
                'externalId' => 'period1'
            ]);

        $this->loadQ1LKTimetable($manager, $weekA, $weekB, $period);
        $this->loadQ2GKTimetable($manager, $weekA, $weekB, $period);

        $manager->flush();
    }

    private function loadQ1LKTimetable(ObjectManager $manager, TimetableWeek $weekA, TimetableWeek $weebB, TimetablePeriod $period) {
        /** @var Tuition[] $lks */
        $lks = $manager->getRepository(Tuition::class)
            ->findAll();

        foreach($lks as $lk) {
            if(substr($lk->getName(), -3) === 'LK1') {
                foreach([$weekA, $weebB] as $week) {
                    // LK1
                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(1)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 1, $week->getKey()))
                    );
                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(2)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 2, $week->getKey()))
                    );

                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(3)// Wednesday
                            ->setLesson(3)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 3, 3, $week->getKey()))
                    );

                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(3)// Wednesday
                            ->setLesson(4)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 3, 4, $week->getKey()))
                    );

                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(5)// Friday
                            ->setLesson(6)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 5, 6, $week->getKey()))
                    );
                }
            } else {
                foreach([$weekA, $weebB] as $week) {
                    // LK2
                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(3)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 3, $week->getKey()))
                    );
                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(4)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 4, $week->getKey()))
                    );

                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(2)// Tuesday
                            ->setLesson(3)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 2, 3, $week->getKey()))
                    );
                    $manager->persist(
                        (new TimetableLesson())
                            ->setDay(2)// Tuesday
                            ->setLesson(4)
                            ->setTuition($lk)
                            ->setRoom($this->roomProvider->getRoom())
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 2, 4, $week->getKey()))
                    );
                }

                $manager->persist(
                    (new TimetableLesson())
                        ->setDay(4)// Tuesday
                        ->setLesson(3)
                        ->setTuition($lk)
                        ->setRoom($this->roomProvider->getRoom())
                        ->setWeek($weekA)
                        ->setPeriod($period)
                        ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 4, 3, $weekA->getKey()))
                );
                $manager->persist(
                    (new TimetableLesson())
                        ->setDay(4)// Tuesday
                        ->setLesson(4)
                        ->setTuition($lk)
                        ->setRoom($this->roomProvider->getRoom())
                        ->setWeek($weekA)
                        ->setPeriod($period)
                        ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 4, 4, $weekA->getKey()))
                );
            }
        }
    }

    private function loadQ2GKTimetable(ObjectManager $manager, TimetableWeek $weekA, TimetableWeek $weebB, TimetablePeriod $period) {

    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            TimetablePeriodFixtures::class,
            TimetableWeekFixtures::class,
            TuitionFixtures::class
        ];
    }
}