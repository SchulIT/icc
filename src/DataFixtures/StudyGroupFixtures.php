<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class StudyGroupFixtures extends Fixture implements DependentFixtureInterface {

    public function __construct(private Generator $generator)
    {
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $this->loadGradeStudyGroups($manager);
        $this->loadInformatikAG($manager);
        $this->loadEFStudyGroups($manager);
        $this->loadQ1StudyGroups($manager);

        $manager->flush();
    }

    private function loadInformatikAG(ObjectManager $manager) {
        $studyGroup = (new StudyGroup())
            ->setName('Informatik AG')
            ->setExternalId('AG-IF')
            ->setType(StudyGroupType::Course);

        $grades = GradeFixtures::getSekIGradeNames();
        $agGrades = array_filter($grades, fn(string $name) => str_starts_with($name, '5'));

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

    private function loadEFStudyGroups(ObjectManager $manager) {
        $grade = $manager->getRepository(Grade::class)
            ->findOneBy(['externalId' => 'EF']);

        $students = $manager->getRepository(Student::class)
            ->findBy([
                'grade' => $grade
            ]);

        $subjects = [ 'M', 'D', 'E', 'IF', 'MU' ];
        $writtenSubjects = [ 'M', 'D', 'E'];

        foreach($subjects as $subject) {
            for ($courseNumber = 1; $courseNumber <= 3; $courseNumber++) {
                $name = sprintf('%s-GK%d', $subject, $courseNumber);
                $id = 'EF-' . $name;

                $studyGroup = (new StudyGroup())
                    ->setName($name)
                    ->setExternalId($id)
                    ->setType(StudyGroupType::Course);

                $studyGroup->addGrade($grade);

                $manager->persist($studyGroup);

                for ($studentIndex = $courseNumber - 1; $studentIndex < count($students); $studentIndex += 3) {
                    $type = in_array($subject, $writtenSubjects) ?
                        'GKS' :
                        ($this->generator->boolean ? 'GKS' : 'GKM');

                    $membership = (new StudyGroupMembership())
                        ->setStudyGroup($studyGroup)
                        ->setStudent($students[$studentIndex])
                        ->setType($type);

                    $studyGroup->getMemberships()
                        ->add($membership);

                    $manager->persist($membership);
                }
            }
        }
    }

    private function loadQ1StudyGroups(ObjectManager $manager) {
        $grade = $manager->getRepository(Grade::class)
            ->findOneBy(['externalId' => 'Q1']);

        $students = $manager->getRepository(Student::class)
            ->findBy([
                'grade' => $grade
            ]);

        $lkSubjects = [ 'M', 'D', 'E' ];
        $gkSubjects = [ 'IF', 'MU', 'L', 'F' ];
        $subjects = array_merge($lkSubjects, $gkSubjects);

        $studyGroups = [ ];

        foreach($subjects as $subject) {
            for($i = 1; $i <= 2; $i++) { // 2 Kurse pro Fach
                $name = sprintf(
                    '%s-%s%d',
                    $subject,
                    in_array($subject, $lkSubjects) ? 'LK' : 'GK',
                    $i
                );
                $id = 'Q1-' . $name;

                $studyGroup = (new StudyGroup())
                    ->setExternalId($id)
                    ->setName($name)
                    ->setType(StudyGroupType::Course);
                $studyGroup->addGrade($grade);

                $studyGroups[$id] = $studyGroup;

                $manager->persist($studyGroup);
            }
        }

        foreach($lkSubjects as $subject) { // Auch GKs erzeugen
            for($i = 1; $i <= 2; $i++) {
                $name = sprintf(
                    '%s-GK%d',
                    $subject,
                    $i
                );
                $id = 'Q1-' . $name;

                $studyGroup = (new StudyGroup())
                    ->setExternalId($id)
                    ->setName($name)
                    ->setType(StudyGroupType::Course);
                $studyGroup->addGrade($grade);

                $studyGroups[$id] = $studyGroup;

                $manager->persist($studyGroup);
            }
        }

        foreach($students as $student) {
            $appliedSubjects = [ ];
            $lks = $this->generator->randomElements($lkSubjects, 2);

            // LK1+LK2
            for($i = 0; $i < 2; $i++) {
                $id = sprintf(
                    'Q1-%s-LK%d',
                    $lks[$i],
                    $this->generator->numberBetween(1, 2)
                );
                $membership = (new StudyGroupMembership())
                    ->setStudyGroup($studyGroups[$id])
                    ->setStudent($student)
                    ->setType(sprintf('LK%d', $i + 1));

                $manager->persist($membership);
            }

            $appliedSubjects = array_merge($appliedSubjects, $lks);
            $availableSubjects = array_diff($subjects, $appliedSubjects);
            $gks = $this->generator->randomElements($availableSubjects, 2);

            for($i = 0; $i < 2; $i++) {
                $id = sprintf(
                    'Q1-%s-GK%d',
                    $gks[$i],
                    $this->generator->numberBetween(1, 2)
                );

                $membership = (new StudyGroupMembership())
                    ->setStudyGroup($studyGroups[$id])
                    ->setStudent($student)
                    ->setType(sprintf('ABI%d', $i + 3));

                $manager->persist($membership);
            }

            $appliedSubjects = array_merge($appliedSubjects, $gks);
            $availableSubjects = array_diff($subjects, $appliedSubjects);
            $leftOvers = $this->generator->randomElements($availableSubjects, 2);

            foreach($leftOvers as $subject) {
                $id = sprintf(
                    'Q1-%s-GK%d',
                    $subject,
                    $this->generator->numberBetween(1, 2)
                );

                $membership = (new StudyGroupMembership())
                    ->setStudyGroup($studyGroups[$id])
                    ->setStudent($student)
                    ->setType(
                        $this->generator->boolean ? 'GKS' : 'GKM'
                    );

                $manager->persist($membership);
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
                ->setType(StudyGroupType::Grade)
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