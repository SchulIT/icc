<?php

namespace App\DataFixtures;

use App\Entity\Grade;
use App\Entity\GradeTeacher;
use App\Entity\GradeTeacherType;
use App\Entity\Teacher;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Generator;

class GradeFixtures extends Fixture implements DependentFixtureInterface {

    private $generator;

    public function __construct(Generator $generator) {
        $this->generator = $generator;
    }

    /**
     * @return array<string, string> keys: external ID, value: name
     */
    public static function getSekIGradeNames() {
        $grades = [ ];

        $suffix = ['A', 'B', 'C'];
        $suffixCount = count($suffix);

        // Sek I
        for($i = 5; $i <= 10; $i++) {
            for($j = 0; $j < $suffixCount; $j++) {
                $name = sprintf('%d%s', $i, $suffix[$j]);
                $id = str_pad($name, 3, '0', STR_PAD_LEFT);

                $grades[$id] = $name;
            }
        }

        return $grades;
    }

    public static function getSekIIGradeNames() {
        return [ 'EF' => 'EF', 'Q1' => 'Q1', 'Q2' => 'Q2' ];
    }

    private static function addTeacherToGrade(Grade $grade, Teacher $teacher, GradeTeacherType $type) {
        $gradeTeacher = (new GradeTeacher())
            ->setGrade($grade)
            ->setTeacher($teacher)
            ->setType($type);

        $grade->addTeacher($gradeTeacher);
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager) {
        $allTeachers = $manager->getRepository(Teacher::class)->findAll();

        foreach(static::getSekIGradeNames() as $externalId => $name) {
            $grade = (new Grade())
                ->setExternalId($externalId)
                ->setName($name);

            $teachers = $this->generator->randomElements($allTeachers, 2, false);
            static::addTeacherToGrade($grade, $teachers[0], GradeTeacherType::Primary());
            static::addTeacherToGrade($grade, $teachers[1], GradeTeacherType::Substitute());

            $manager->persist($grade);
        }

        foreach(static::getSekIIGradeNames() as $name) {
            $grade = (new Grade())
                ->setExternalId($name)
                ->setName($name);

            $teachers = $this->generator->randomElements($allTeachers, 2, false);
            static::addTeacherToGrade($grade, $teachers[0], GradeTeacherType::Primary());
            static::addTeacherToGrade($grade, $teachers[1], GradeTeacherType::Substitute());

            $manager->persist($grade);
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies() {
        return [
            TeacherFixtures::class
        ];
    }
}