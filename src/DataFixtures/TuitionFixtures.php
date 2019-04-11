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

        $manager->flush();
    }
}