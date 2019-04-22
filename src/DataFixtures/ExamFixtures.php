<?php

namespace App\DataFixtures;

use App\Entity\Exam;
use App\Entity\ExamInvigilator;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Teacher;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Generator;

class ExamFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;
    private $tuitionRepository;

    public function __construct(Generator $generator, TuitionRepositoryInterface $tuitionRepository) {
        $this->generator = $generator;
        $this->tuitionRepository = $tuitionRepository;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $this->loadQ1Exams($manager);

        $manager->flush();
    }

    private function loadQ1Exams(ObjectManager $manager) {
        $rooms = [ ];

        foreach(['A', 'B', 'C'] as $building) {
            for($floor = 0; $floor <= 3; $floor++) {
                for($room = 0; $room <= 10; $room++) {
                    $rooms[] = sprintf(
                        '%s%s',
                        $building,
                        str_pad((string)($floor * 100 + $room), 3, '0', STR_PAD_LEFT)
                    );
                }
            }
        }

        $grade = $manager->getRepository(Grade::class)
            ->findOneBy([
                'externalId' => 'Q1'
            ]);

        $tuitions = $this->tuitionRepository->findAllByGrades([$grade]);

        $teachers = $manager->getRepository(Teacher::class)
            ->findAll();

        foreach($tuitions as $tuition) {
            for($i = 1; $i <= 4; $i++) {
                $id = sprintf(
                    'exam-%s-%d',
                    $tuition->getName(),
                    $i
                );

                $start = $this->generator->numberBetween(1, 3);
                $duration = $this->generator->numberBetween(2, 4);
                $room = $this->generator->randomElement($rooms);

                $exam = (new Exam())
                    ->setExternalId($id)
                    ->setDate(
                        $this->generator->dateTimeBetween('-180 days', '+180 days')
                    )
                    ->setLessonStart($start)
                    ->setLessonEnd($start + $duration - 1)
                    ->setDescription($this->generator->text(20))
                    ->setRooms(array_fill(0, $duration, $room));

                $manager->persist($exam);

                $exam->addTuition($tuition);

                $students = $tuition
                    ->getStudyGroup()
                    ->getMemberships()
                    ->filter(
                        function(StudyGroupMembership $membership) {
                            return in_array($membership->getType(), [ 'GKS', 'LK1', 'LK2', 'ABI3', 'ABI4']);
                        }
                    )
                    ->map(
                        function(StudyGroupMembership $membership) {
                            return $membership->getStudent();
                        }
                    )
                    ->toArray();

                foreach($students as $student) {
                    $exam->addStudent($student);
                }

                if($this->generator->boolean) {
                    $invigilatorTeachers = $this->generator->randomElements($teachers, $duration);

                    for($lesson = $exam->getLessonStart(); $lesson <= $exam->getLessonEnd(); $lesson++) {
                        $invigilator = (new ExamInvigilator())
                            ->setTeacher($invigilatorTeachers[$lesson - $exam->getLessonStart()])
                            ->setLesson($lesson)
                            ->setExam($exam);

                        $manager->persist($invigilator);
                    }
                }

                $manager->persist($exam);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            TuitionFixtures::class
        ];
    }
}