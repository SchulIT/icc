<?php

namespace App\DataFixtures;

use App\Entity\StudyGroup;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class TuitionFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            SubjectFixtures::class,
            StudyGroupFixtures::class,
            TeacherFixtures::class
        ];
    }

    private function loadEFTuitions(ObjectManager $manager) {
        $subjects = [ 'M', 'D', 'E' ];

        $teachers = $manager->getRepository(Teacher::class)
            ->findAll();

        foreach($subjects as $subjectName) {
            $subject = $manager->getRepository(Subject::class)
                ->findOneBy([
                    'abbreviation' => $subjectName
                ]);

            for($courseNumber = 1; $courseNumber <= 3; $courseNumber++) {
                $teacher = $this->generator->randomElement($teachers);

                $studyGroupId = sprintf('EF-%s-GK%d', $subjectName, $courseNumber);
                $studyGroup = $manager->getRepository(StudyGroup::class)
                    ->findOneBy([
                        'externalId' => $studyGroupId
                    ]);

                $tuition = (new Tuition())
                    ->setExternalId($studyGroup->getExternalId())
                    ->setName($studyGroup->getName())
                    ->setStudyGroup($studyGroup)
                    ->setSubject($subject)
                    ->setTeacher($teacher);

                $manager->persist($tuition);
            }
        }
    }

    private function loadQ1Tuitions(ObjectManager $manager) {
        $lkSubjects = [ 'M', 'D', 'E' ];
        $gkSubjects = [ 'IF', 'MU', 'L', 'F' ];
        $subjects = array_merge($lkSubjects, $gkSubjects);

        $teachers = $manager->getRepository(Teacher::class)
            ->findAll();

        foreach($lkSubjects as $subjectName) {
            for($i = 1; $i <= 2; $i++) {
                $name = sprintf(
                    '%s-LK%d',
                    $subjectName,
                    $i
                );

                $id = 'Q1-' . $name;

                $studyGroup = $manager->getRepository(StudyGroup::class)
                    ->findOneBy([
                        'externalId' => $id
                    ]);

                $subject = $manager->getRepository(Subject::class)
                    ->findOneBy([
                        'externalId' => $subjectName
                    ]);

                $teacher = $this->generator->randomElement($teachers);

                $tuition = (new Tuition())
                    ->setName($name)
                    ->setExternalId($id)
                    ->setStudyGroup($studyGroup)
                    ->setTeacher($teacher)
                    ->setSubject($subject);

                $manager->persist($tuition);
            }
        }

        foreach($subjects as $subjectName) {
            for($i = 1; $i <= 2; $i++) {
                $name = sprintf(
                    '%s-GK%d',
                    $subjectName,
                    $i
                );

                $id = 'Q1-' . $name;

                $studyGroup = $manager->getRepository(StudyGroup::class)
                    ->findOneBy([
                        'externalId' => $id
                    ]);

                $subject = $manager->getRepository(Subject::class)
                    ->findOneBy([
                        'externalId' => $subjectName
                    ]);

                $teacher = $this->generator->randomElement($teachers);

                $tuition = (new Tuition())
                    ->setName($name)
                    ->setExternalId($id)
                    ->setStudyGroup($studyGroup)
                    ->setTeacher($teacher)
                    ->setSubject($subject);

                $manager->persist($tuition);
            }
        }
    }

    private function loadInformatikAG(ObjectManager $manager) {
        $studyGroup = $manager->getRepository(StudyGroup::class)
            ->findOneBy(['externalId' => 'AG-IF']);

        $teachers = $manager->getRepository(Teacher::class)
            ->findAll();
        $teacher = $this->generator->randomElement($teachers);

        $subject = $manager->getRepository(Subject::class)
            ->findOneBy([
                'externalId' => 'AG-IF'
            ]);

        $tuition = (new Tuition())
            ->setExternalId('5-IF-AG')
            ->setTeacher($teacher)
            ->setName('Informatik AG')
            ->setSubject($subject)
            ->setStudyGroup($studyGroup);

        $manager->persist($tuition);
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $teachers = $manager->getRepository(Teacher::class)
            ->findAll();

        $subjects = $manager->getRepository(Subject::class)
            ->findAll();

        /** @var Subject[] $mainSubjects */
        $mainSubjects = array_filter($subjects, function(Subject $subject) {
            return in_array($subject->getAbbreviation(), [ 'M', 'D', 'E']);
        });

        $grades = GradeFixtures::getSekIGradeNames();

        foreach($grades as $id => $gradeName) {
            $studyGroup = $manager->getRepository(StudyGroup::class)
                ->findOneBy([
                    'externalId' => $id
                ]);

            foreach($mainSubjects as $subject) {
                $tuitionId = sprintf('%s-%s', $id, $subject->getAbbreviation());
                $tuition = (new Tuition())
                    ->setStudyGroup($studyGroup)
                    ->setSubject($subject)
                    ->setExternalId($tuitionId)
                    ->setName($subject->getName());

                $teacher = $this->generator->randomElement($teachers);
                $tuition->setTeacher($teacher);

                $manager->persist($tuition);
            }
        }

        $this->loadInformatikAG($manager);
        $this->loadEFTuitions($manager);
        $this->loadQ1Tuitions($manager);

        $manager->flush();
    }
}