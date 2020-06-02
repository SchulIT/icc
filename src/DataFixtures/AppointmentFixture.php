<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\StudyGroup;
use App\Entity\Teacher;
use App\Entity\UserTypeEntity;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class AppointmentFixture extends Fixture implements DependentFixtureInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            AppointmentCategoryFixtures::class,
            StudyGroupFixtures::class
        ];
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $studyGroups = $manager->getRepository(StudyGroup::class)->findAll();
        $teachers = $manager->getRepository(Teacher::class)->findAll();
        $categories = $manager->getRepository(AppointmentCategory::class)->findAll();
        $visibilities = $manager->getRepository(UserTypeEntity::class)->findAll();

        for($i = 0; $i < 200; $i++) {
            $start = $this->generator->dateTimeBetween('-180 days', '+180 days');
            $end = $this->generator->dateTimeBetween($start, (clone $start)->modify('+5 days'));

            $appointment = (new Appointment())
                ->setExternalId('sg-' . $i)
                ->setCategory($this->generator->randomElement($categories))
                ->setStart($start)
                ->setEnd($end)
                ->setLocation($this->generator->city)
                ->setTitle($this->generator->words(3, true))
                ->setContent($this->generator->sentence)
                ->setAllDay($this->generator->boolean);

            $v = $this->generator->randomElements($visibilities, 2, false);

            foreach($v as $visibility) {
                $appointment->addVisibility($visibility);
            }

            $numStudyGroups = $this->generator->numberBetween(1, count($studyGroups));
            $studyGroups = $this->generator->randomElements($studyGroups, $numStudyGroups);

            foreach($studyGroups as $studyGroup) {
                $appointment->addStudyGroup($studyGroup);
            }

            $numOrganisators = $this->generator->numberBetween(0, 3);
            $organisators = $this->generator->randomElements($teachers, $numOrganisators, false);

            foreach($organisators as $organisator) {
                $appointment->addOrganizer($organisator);
            }

            $manager->persist($appointment);
        }

        $manager->flush();
    }
}