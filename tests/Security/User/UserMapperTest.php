<?php

namespace App\Tests\Security\User;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;
use App\Entity\UserType;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Security\User\UserMapper;
use LightSaml\ClaimTypes;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use SchulIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;

class UserMapperTest extends TestCase {

    private function getTypesMap() {
        return [
            'parent' => 'parent',
            'teacher' => 'teacher',
            'intern' => 'intern',
            'student' => 'student',
            'secretary' => 'staff'
        ];
    }

    private function getTeacherRepositoryMock(): TeacherRepositoryInterface {
        $repository = $this->createMock(TeacherRepositoryInterface::class);

        $map = [
            [ 'test@schulit.de', (new Teacher())->setEmail('test@schulit.de') ],
            [ 'foo@schulit.de', (new Teacher())->setEmail('foo@schulit.de') ]
        ];

        $repository
            ->method('findOneByEmailAddress')
            ->willReturnMap($map);

        return $repository;
    }

    private function getStudentRepositoryMock(): StudentRepositoryInterface {
        $repository = $this->createMock(StudentRepositoryInterface::class);

        $map = [
            [ 'student1@schulit.de', (new Student())->setEmail('student1@schulit.de') ],
            [ 'student2@schulit.de', (new Student())->setEmail('student2@schulit.de') ]
        ];

        $repository
            ->method('findOneByEmailAddress')
            ->willReturnMap($map);

        $repository
            ->method('findAllByEmailAddresses')
            ->willReturnMap([
                [['student1@schulit.de', 'student2@schulit.de', 'student3@schulit.de'], [ (new Student())->setEmail('student1@schulit.de'), (new Student())->setEmail('student2@schulit.de')]],
                [ ['notexisting@schulit.de'], [ ]]
            ]);

        return $repository;
    }

    private function createUserMapper(): UserMapper {
        return new UserMapper(
            $this->getTypesMap(),
            $this->getTeacherRepositoryMock(),
            $this->getStudentRepositoryMock(),
            new NullLogger()
        );
    }

    public function testMapTeacherWithExistingTeacher() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'test@schulit.de',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'teacher'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('test@schulit.de', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isTeacher());
        $this->assertNotNull($user->getTeacher());
        $this->assertEquals('test@schulit.de', $user->getTeacher()->getEmail());
    }

    public function testMapTeacherWithNonExistingTeacher() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'notexisting@schulit.de',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'teacher'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('notexisting@schulit.de', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isTeacher());
        $this->assertNull($user->getTeacher());
    }

    public function testMapStudentWithExistingStudent() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'student1@schulit.de',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'student'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('student1@schulit.de', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isStudent());
        $this->assertEquals(1, $user->getStudents()->count());
        $this->assertEquals('student1@schulit.de', $user->getStudents()->first()->getEmail());
    }

    public function testMapStudentWithNonExistingStudent() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'notexisting@schulit.de',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'student'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('notexisting@schulit.de', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isStudent());
        $this->assertEquals(0, $user->getStudents()->count());
    }

    public function testMapParentWithExistingStudents() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'parent',
            SamlClaimTypes::EXTERNAL_ID => 'student1@schulit.de,student2@schulit.de,student3@schulit.de'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isParent());
        $this->assertEquals(2, $user->getStudents()->count());
        $this->assertEquals(['student1@schulit.de', 'student2@schulit.de'], $user->getStudents()->map(function(Student $student) { return $student->getEmail(); })->toArray());
    }

    public function mapParentWithNonExistingStudents() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            SamlClaimTypes::ID => '1f1248d4-8742-4b89-a0c4-1f345ce5664a',
            ClaimTypes::COMMON_NAME => 'username',
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'parent',
            SamlClaimTypes::EXTERNAL_ID => 'student3@schulit.de'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('username', $user->getUsername());
        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->isParent());
        $this->assertEquals(0, $user->getStudents()->count());
    }

}