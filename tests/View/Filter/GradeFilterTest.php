<?php

namespace App\Tests\View\Filter;

use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Sorting\StringStrategy;
use App\View\Filter\GradeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class GradeFilterTest extends TestCase {

    private function createGrade(int $id, string $name): Grade {
        $grade = $this->createMock(Grade::class);
        $grade
            ->method('getId')
            ->willReturn($id);

        $grade
            ->method('getName')
            ->willReturn($name);

        return $grade;
    }

    private function createRepository(): GradeRepositoryInterface {
        $repository = $this->createMock(GradeRepositoryInterface::class);

        $repository
            ->method('findAll')
            ->willReturn([
                $this->createGrade(1, '8A'),
                $this->createGrade(2, 'EF'),
                $this->createGrade(3, '10A'),
                $this->createGrade(4, '5B')
            ]);

        return $repository;
    }

    private function createSorter(): Sorter {
        return new Sorter([ new GradeNameStrategy(new StringStrategy())]);
    }

    private function createTeacher(): User {
        $user = $this->createMock(User::class);

        $user
            ->method('getUserType')
            ->willReturn(UserType::Teacher());

        return  $user;
    }

    private function createStudentEF(): User {
        $user = $this->createMock(User::class);
        $student = $this->createMock(Student::class);

        $student
            ->method('getGrade')
            ->willReturn($this->createGrade(2, 'EF'));

        $user
            ->method('getUserType')
            ->willReturn(UserType::Student());

        $user
            ->method('getStudents')
            ->willReturn(new ArrayCollection([$student]));

        return $user;
    }

    public function testDefaultFilterTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository());
        $view = $filter->handle(null, $this->createTeacher());

        $this->assertNull($view->getCurrentGrade());
    }

    public function testValidQueryTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository());
        $view = $filter->handle(3, $this->createTeacher());

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals(3, $view->getCurrentGrade()->getId());
    }

    public function testInvalidQueryTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository());
        $view = $filter->handle(10, $this->createTeacher());

        $this->assertNull($view->getCurrentGrade());
    }

    public function testDefaultFilterStudent() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository());
        $view = $filter->handle(null, $this->createStudentEF());

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals(2, $view->getCurrentGrade()->getId());
    }

    public function testInvalidFilterStudent() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository());
        $view = $filter->handle(10, $this->createStudentEF());

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals(2, $view->getCurrentGrade()->getId());
    }
}