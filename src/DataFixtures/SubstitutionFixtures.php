<?php

namespace App\DataFixtures;

use App\Entity\Room;
use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;
use SchulIT\CommonBundle\Helper\DateHelper;

class SubstitutionFixtures extends Fixture implements DependentFixtureInterface {

    private $roomGenerator;
    private $generator;
    private $dateHelper;

    public function __construct(RoomGenerator $roomGenerator, Generator $generator, DateHelper $dateHelper) {
        $this->roomGenerator = $roomGenerator;
        $this->generator = $generator;
        $this->dateHelper = $dateHelper;
    }

    public function load(ObjectManager $manager) {
        $subjects = [ 'M', 'D', 'E', 'IF', 'MU', 'F0', 'PH' ];
        $rooms = $manager->getRepository(Room::class)->findAll();
        $types = [ 'Raumvertretung', 'Entfall', 'Vertretung', null ];
        $studyGroups = $manager->getRepository(StudyGroup::class)->findAll();
        $teachers = $manager->getRepository(Teacher::class)->findAll();
        $dates = $this->dateHelper->getListOfNextDays(7);
        $id = 1;

        foreach($studyGroups as $studyGroup) {
            if($this->generator->boolean) {
                $start = $this->generator->numberBetween(1, 8);
                $end = $this->generator->numberBetween($start, 8);

                $substitution = (new Substitution())
                    ->setLessonStart($start)
                    ->setLessonEnd($end)
                    ->setDate($this->generator->randomElement($dates))
                    ->setSubject($this->generator->randomElement($subjects))
                    ->setType($this->generator->randomElement($types))
                    ->setExternalId(sprintf('substitution-%d', $id));

                $substitution->addTeacher($this->generator->randomElement($teachers));

                if($this->generator->boolean) {
                    $teacher = $this->generator->randomElement($teachers);

                    if(!$substitution->getTeachers()->contains($teacher)) {
                        $substitution->addTeacher($teacher);
                    }
                }

                if($this->generator->boolean) {
                    $substitution->setRoom($this->generator->randomElement($rooms));
                }

                if($this->generator->boolean) {
                    $substitution->setReplacementRoom($this->generator->randomElement($rooms));
                }

                if($this->generator->boolean) {
                    $substitution->setReplacementSubject($this->generator->randomElement($subjects));
                }

                if($this->generator->boolean(70)) {
                    $substitution->addReplacementTeacher($this->generator->randomElement($teachers));
                }

                if($this->generator->boolean) {
                    $substitution->setRemark($this->generator->text(100));
                }

                $substitution->addStudyGroup($studyGroup);

                if($this->generator->boolean) {
                    $additionalStudyGroup = $this->generator->randomElement($studyGroups);

                    if($additionalStudyGroup->getId() !== $studyGroup->getId()) {
                        $substitution->addStudyGroup($additionalStudyGroup);
                    }
                    $substitution->addReplacementStudyGroup($this->generator->randomElement($studyGroups));
                }

                $manager->persist($substitution);
            }

            $id++;
        }

        for($i = 0; $i < 100; $i++) {
            $start = $this->generator->numberBetween(1, 8);

            $substitution = (new Substitution())
                ->setStartsBefore(true)
                ->setLessonStart($start)
                ->setLessonEnd($start)
                ->setDate($this->generator->randomElement($dates))
                ->setType("Aufsicht")
                ->setExternalId(sprintf('substitution-%d', $id));

            $substitution->addTeacher($this->generator->randomElement($teachers));

            $manager->persist($substitution);
            $id++;
        }

        $manager->flush();
    }

    public function getDependencies() {
        return [
            StudyGroupFixtures::class,
            TeacherFixtures::class,
            RoomFixtures::class
        ];
    }
}