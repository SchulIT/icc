<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\Subject;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\ExpressionLanguage\Tests\Node\Obj;

class StudyGroupFixtures extends Fixture implements DependentFixtureInterface {

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $this->loadGradeStudyGroups($manager);
        $this->loadInformatikAG($manager);
        $this->loadEFCourses($manager);

        $manager->flush();
    }

    private function loadInformatikAG(ObjectManager $manager) {
        $studyGroup = (new StudyGroup())
            ->setName('Informatik AG')
            ->setExternalId('AG-IF')
            ->setType(StudyGroupType::Course());

        $grades = GradeFixtures::getSekIGradeNames();
        $agGrades = array_filter($grades, function(string $name) {
            return substr($name, 0, 1) === '5';
        });

        $students = [ ];

        foreach($agGrades as $id => $gradeName) {
            $grade = $manager->getRepository(Grade::class)
                ->findOneBy([
                    'externalId'=> $id
                ]);

            $studyGroup->addGrade($grade);

            $gradeStudents = $manager->getRepository(Student::class)
                ->findBy([
                    'grade' => $grade
                ]);

            for($i = 0; $i < 8; $i++) {
                $students[] = $gradeStudents[$i];
            }
        }

        $manager->persist($studyGroup);

        // Memberships
        foreach($students as $student) {
            $membership = (new StudyGroupMembership())
                ->setStudent($student)
                ->setStudyGroup($studyGroup)
                ->setType('PUT');

            $manager->persist($membership);
        }

        $manager->persist($studyGroup);
    }

    private function loadEFCourses(ObjectManager $manager) {
        $grade = $manager->getRepository(Grade::class)
            ->findOneBy(['externalId' => 'EF']);

        $students = $manager->getRepository(Student::class)
            ->findBy([
                'grade' => $grade
            ]);

        $subjects = [ 'M', 'D', 'E' ];

        foreach($subjects as $subject) {
            for ($courseNumber = 1; $courseNumber <= 3; $courseNumber++) {
                $name = sprintf('%s-GK%d', $subject, $courseNumber);
                $id = 'EF-' . $name;

                $studyGroup = (new StudyGroup())
                    ->setName($name)
                    ->setExternalId($id)
                    ->setType(StudyGroupType::Course());

                $studyGroup->addGrade($grade);

                $manager->persist($studyGroup);

                for ($studentIndex = $courseNumber - 1; $studentIndex < count($students); $studentIndex += 3) {
                    $membership = (new StudyGroupMembership())
                        ->setStudyGroup($studyGroup)
                        ->setStudent($students[$studentIndex])
                        ->setType('GKS');

                    $studyGroup->getMemberships()
                        ->add($membership);

                    $manager->persist($membership);
                }
            }
        }
    }

    private function loadGradeStudyGroups(ObjectManager $manager) {
        $grades = array_merge(
            GradeFixtures::getSekIGradeNames(),
            GradeFixtures::getSekIIGradeNames()
        );

        foreach($grades as $id => $name) {
            $studyGroup = (new StudyGroup())
                ->setName($name)
                ->setType(StudyGroupType::Grade())
                ->setExternalId($id);

            // Grade
            $grade = $manager->getRepository(Grade::class)
                ->findOneBy([
                    'externalId' => $id
                ]);

            $studyGroup->addGrade($grade);
            $manager->persist($studyGroup);

            // Memberships
            $students = $manager->getRepository(Student::class)
                ->findBy([
                    'grade' => $grade
                ]);

            foreach($students as $student) {
                $membership = (new StudyGroupMembership())
                    ->setStudyGroup($studyGroup)
                    ->setStudent($student)
                    ->setType('PUK');

                $manager->persist($membership);
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            GradeFixtures::class,
            SubjectFixtures::class,
            StudentFixtures::class
        ];
    }
}