<?php

namespace App\Tests\Sorting;

use App\Entity\Grade;
use App\Entity\Student;
use App\Grouping\StudentGradeGroup;
use App\Sorting\GradeStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StudentsGradeStrategyTest extends TestCase {
    public function testStrategy() {
        $gradeGroupsStrategy = new StudentGradeGroupStrategy(new GradeStrategy());

        $groupEF = (new StudentGradeGroup((new Grade())->setName('EF')));
        $group9A = (new StudentGradeGroup((new Grade())->setName('9A')));
        $group9B = (new StudentGradeGroup((new Grade())->setName('9B')));
        $group10A = (new StudentGradeGroup((new Grade())->setName('10A')));

        $groups = [
            $groupEF,
            $group10A,
            $group9B,
            $group9A
        ];

        $sorter = new Sorter([$gradeGroupsStrategy]);

        $sorter->sort($groups, StudentGradeGroupStrategy::class);

        $this->assertEquals($group9A, $groups[0], '9A is first grade in list');
        $this->assertEquals($group9B, $groups[1], '9B is second grade in list');
        $this->assertEquals($group10A, $groups[2], '10A is second grade in list');
        $this->assertEquals($groupEF, $groups[3], 'EF is fourth grade in list');
    }

    public function testSortStudents() {
        $groupEF = (new StudentGradeGroup((new Grade())->setName('EF')));

        $studentOne = (new Student())
            ->setFirstname('Max')
            ->setLastname('Mustermann');
        $studentTwo = (new Student())
            ->setFirstname('Erika')
            ->setLastname('Mustermann');
        $studentThree = (new Student())
            ->setFirstname('Erika')
            ->setLastname('Musterfrau');

        $groupEF->addItem($studentOne);
        $groupEF->addItem($studentTwo);
        $groupEF->addItem($studentThree);

        $studentsStrategy = new StudentStrategy();
        $sorter = new Sorter([$studentsStrategy]);

        $sorter->sortGroupItems([$groupEF], StudentStrategy::class);

        $students = $groupEF->getStudents();

        $this->assertEquals($studentThree, $students[0]);
        $this->assertEquals($studentTwo, $students[1]);
        $this->assertEquals($studentOne, $students[2]);
    }
}