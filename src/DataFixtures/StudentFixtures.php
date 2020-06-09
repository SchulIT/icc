<?php

namespace App\DataFixtures;

use App\Entity\Gender;
use App\Entity\Grade;
use App\Entity\Student;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class StudentFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $studentIdStart = $this->loadSekIStudents($manager);
        $this->loadSekIIStudents($manager, $studentIdStart);
        $manager->flush();
    }

    private function loadSekIIStudents(ObjectManager $manager, int $studentIdStart) {
        $grades = GradeFixtures::getSekIIGradeNames();
        $size = 60;
        $studentId = $studentIdStart;

        foreach($grades as $gradeName) {
            $grade = $manager->getRepository(Grade::class)
                ->findOneBy([
                    'externalId' => $gradeName
                ]);

            $actualSize = $this->generator->numberBetween($size - 5, $size + 5);
            for($i = 0; $i < $actualSize; $i++) {
                $entity = $this->getStudentFromObject(true);

                $entity
                    ->setExternalId($studentId)
                    ->setGrade($grade);

                $studentId++;
                $manager->persist($entity);
            }
        }
    }

    private function loadSekIStudents(ObjectManager $manager): int {
        $grades = GradeFixtures::getSekIGradeNames();
        $studentId = 0;
        $size = 20;

        foreach($grades as $id => $name) {
            $grade = $manager->getRepository(Grade::class)
                ->findOneBy([
                    'externalId' => $id
                ]);

            $actualSize = $this->generator->numberBetween($size - 3, $size + 3);
            for($i = 0; $i < $actualSize; $i++) {
                $entity = $this->getStudentFromObject(false);

                $entity
                    ->setExternalId($studentId)
                    ->setGrade($grade);

                $studentId++;
                $manager->persist($entity);
            }
        }

        return $studentId;
    }

    private function getStudentFromObject($isSekII = false) {
        $gender = $this->generator->boolean ?
            Gender::Male() :
            Gender::Female();

        $isFullAged = $isSekII && $this->generator->boolean;
        $firstname = $this->generator->firstName;
        $lastname = $this->generator->lastName;

        return (new Student())
            ->setGender($gender)
            ->setStatus('aktiv')
            ->setIsFullAged($isFullAged)
            ->setLastname($lastname)
            ->setFirstname($firstname)
            ->setEmail($this->generateEmail($firstname, $lastname));
    }

    private function generateEmail(string $firstname, string $lastname) {
        return mb_strtolower(sprintf('%s.%s@students.school.it', $firstname, $lastname));
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            GradeFixtures::class
        ];
    }
}