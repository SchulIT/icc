<?php

namespace App\Tests\View\Filter;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\GradeNameStrategy;
use App\Sorting\Sorter;
use App\Sorting\StringStrategy;
use App\View\Filter\GradeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GradeFilterTest extends TestCase {

    /** @var Section */
    private $section;

    public function setUp(): void {
        $mock = $this->createMock(Section::class);
        $mock->method('getId')->willReturn(1);

        $this->section = $mock;
    }

    private function createGrade(UuidInterface $uuid, string $name): Grade {
        $grade = $this->createMock(Grade::class);
        $grade
            ->method('getUuid')
            ->willReturn($uuid);

        $grade
            ->method('getName')
            ->willReturn($name);

        return $grade;
    }

    private function createTuitionRepository(): TuitionRepositoryInterface {
        $repository = $this->createMock(TuitionRepositoryInterface::class);

        $repository
            ->method('findAllByTeacher')
            ->willReturn([]);

        return $repository;
    }

    private function createRepository(): GradeRepositoryInterface {
        $repository = $this->createMock(GradeRepositoryInterface::class);

        $repository
            ->method('findAll')
            ->willReturn([
                $this->createGrade(Uuid::fromString('6e0af4fd-9b1a-4218-a7f8-107284ef2e38'), '8A'),
                $this->createGrade(Uuid::fromString('cbd7de3d-fd46-457e-93d3-dc94acd82ae4'), 'EF'),
                $this->createGrade(Uuid::fromString('124a67e8-addd-42c5-b0a0-4639df9eb342'), '10A'),
                $this->createGrade(Uuid::fromString('8ba5aead-7b64-4412-a760-f9fa08b91656'), '5B')
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
            ->willReturn(UserType::Teacher);

        return  $user;
    }

    private function createStudentEF(): User {
        $user = $this->createMock(User::class);
        $student = $this->createMock(Student::class);

        $student
            ->method('getGrade')
            ->with($this->section)
            ->willReturn($this->createGrade(Uuid::fromString('cbd7de3d-fd46-457e-93d3-dc94acd82ae4'), 'EF'));

        $user
            ->method('isStudent')
            ->willReturn(true);

        $user
            ->method('isStudentOrParent')
            ->willReturn(true);

        $user
            ->method('getStudents')
            ->willReturn(new ArrayCollection([$student]));

        return $user;
    }

    public function testDefaultFilterTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository(), $this->createTuitionRepository());
        $view = $filter->handle(null, null, $this->createTeacher());

        $this->assertNull($view->getCurrentGrade());
    }

    public function testValidQueryTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository(), $this->createTuitionRepository());
        $view = $filter->handle('cbd7de3d-fd46-457e-93d3-dc94acd82ae4', null, $this->createTeacher());

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals('cbd7de3d-fd46-457e-93d3-dc94acd82ae4', $view->getCurrentGrade()->getUuid()->toString());
    }

    public function testInvalidQueryTeacher() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository(), $this->createTuitionRepository());
        $view = $filter->handle('a4cef2e4-4a9a-42bb-ac99-55c5c5332ada', null, $this->createTeacher());

        $this->assertNull($view->getCurrentGrade());
    }

    public function testDefaultFilterStudent() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository(), $this->createTuitionRepository());
        $view = $filter->handle(null, $this->section, $this->createStudentEF(), true);

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals('cbd7de3d-fd46-457e-93d3-dc94acd82ae4', $view->getCurrentGrade()->getUuid()->toString());
    }

    public function testInvalidFilterStudent() {
        $filter = new GradeFilter($this->createSorter(), $this->createRepository(), $this->createTuitionRepository());
        $view = $filter->handle('cbd7de3d-fd46-457e-93d3-dc94acd82ae4', $this->section, $this->createStudentEF());

        $this->assertNotNull($view->getCurrentGrade());
        $this->assertEquals('cbd7de3d-fd46-457e-93d3-dc94acd82ae4', $view->getCurrentGrade()->getUuid()->toString());
    }
}