<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\StudyGroup;
use App\Entity\TimetableLesson;
use App\Entity\TimetablePeriod;
use App\Entity\TimetableWeek;
use App\Entity\Tuition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TimetableFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
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

        $rooms = $manager->getRepository(Room::class)
            ->findAll();

        $this->loadQ1LKTimetable($manager, $weekA, $weekB, $period, $rooms);
        $this->loadQ2GKTimetable($manager, $weekA, $weekB, $period, $rooms);

        $manager->flush();
    }

    private function addGrades(TimetableLesson $lesson, Tuition $tuition) {
        /** @var Grade $grade */
        foreach($tuition->getStudyGroup()->getGrades() as $grade) {
            $lesson->addGrade($grade);
        }

        return $lesson;
    }

    private function loadQ1LKTimetable(ObjectManager $manager, TimetableWeek $weekA, TimetableWeek $weekB, TimetablePeriod $period, array $rooms) {
        /** @var Tuition[] $lks */
        $lks = $manager->getRepository(Tuition::class)
            ->findAll();

        foreach($lks as $lk) {
            if(substr($lk->getName(), -3) === 'LK1') {
                foreach([$weekA, $weekB] as $week) {
                    // LK1
                    $manager->persist(
                        $this->addGrades(
                            (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(1)
                            ->setIsDoubleLesson(true)
                            ->setTuition($lk)
                            ->setRoom($this->generator->randomElement($rooms))
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 1, $week->getKey())),
                            $lk
                        )
                    );

                    $manager->persist(
                        $this->addGrades(
                            (new TimetableLesson())
                                ->setDay(3)// Wednesday
                                ->setLesson(3)
                                ->setIsDoubleLesson(true)
                                ->setTuition($lk)
                                ->setRoom($this->generator->randomElement($rooms))
                                ->setWeek($week)
                                ->setPeriod($period)
                                ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 3, 3, $week->getKey())),
                            $lk
                        )
                    );

                    $manager->persist(
                        $this->addGrades(
                            (new TimetableLesson())
                                ->setDay(5)// Friday
                                ->setLesson(6)
                                ->setTuition($lk)
                                ->setRoom($this->generator->randomElement($rooms))
                                ->setWeek($week)
                                ->setPeriod($period)
                                ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 5, 6, $week->getKey())),
                            $lk
                        )
                    );
                }
            } else if(substr($lk->getName(), -3) === 'LK2')  {
                foreach([$weekA, $weekB] as $week) {
                    // LK2
                    $manager->persist(
                        $this->addGrades(
                        (new TimetableLesson())
                            ->setDay(1)// Monday
                            ->setLesson(3)
                            ->setIsDoubleLesson(true)
                            ->setTuition($lk)
                            ->setRoom($this->generator->randomElement($rooms))
                            ->setWeek($week)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 1, 3, $week->getKey())),
                            $lk
                        )
                    );

                    $manager->persist(
                        $this->addGrades(
                            (new TimetableLesson())
                                ->setDay(2)// Tuesday
                                ->setLesson(3)
                                ->setIsDoubleLesson(true)
                                ->setTuition($lk)
                                ->setRoom($this->generator->randomElement($rooms))
                                ->setWeek($week)
                                ->setPeriod($period)
                                ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 2, 3, $week->getKey())),
                            $lk
                        )
                    );
                }

                $manager->persist(
                    $this->addGrades(
                        (new TimetableLesson())
                            ->setDay(4)// Tuesday
                            ->setLesson(3)
                            ->setIsDoubleLesson(true)
                            ->setTuition($lk)
                            ->setRoom($this->generator->randomElement($rooms))
                            ->setWeek($weekA)
                            ->setPeriod($period)
                            ->setExternalId(sprintf('%s-%d-%d-%s', $lk->getExternalId(), 4, 3, $weekA->getKey())),
                        $lk
                    )
                );
            }
        }
    }

    private function loadQ2GKTimetable(ObjectManager $manager, TimetableWeek $weekA, TimetableWeek $weebB, TimetablePeriod $period, array $rooms) {

    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            TimetablePeriodFixtures::class,
            TimetableWeekFixtures::class,
            TuitionFixtures::class,
            RoomFixtures::class
        ];
    }
}