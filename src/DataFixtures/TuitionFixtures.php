<?php

namespace App\DataFixtures;

use App\Entity\StudyGroup;
use App\Entity\Subject;
use App\Entity\Teacher;
use App\Entity\Tuition;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TuitionFixtures extends Fixture implements DependentFixtureInterface {

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
                $teacher = $teachers[random_int(0, count($teachers) - 1)];

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

    private function loadInformatikAG(ObjectManager $manager) {
        $studyGroup = $manager->getRepository(StudyGroup::class)
            ->findOneBy(['externalId' => 'AG-IF']);

        $teacher = $manager->getRepository(Teacher::class)
            ->findOneBy([
                'acronym' => 'GREE'
            ]);

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

                $teacher = $teachers[random_int(0, count($teachers) - 1)];
                $tuition->setTeacher($teacher);

                $manager->persist($tuition);
            }
        }

        $this->loadInformatikAG($manager);
        $this->loadEFTuitions($manager);

        $manager->flush();
    }
}