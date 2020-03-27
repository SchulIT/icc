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
use SchoolIT\CommonBundle\Saml\ClaimTypes as SamlClaimTypes;

class UserMapperTest extends TestCase {

    private function getTypesMap() {
        return [
            'parent' => 'Parent',
            'teacher' => 'Teacher',
            'intern' => 'Intern',
            'student' => 'Student',
            'secretary' => 'Staff'
        ];
    }

    private function getTeacherRepositoryMock(): TeacherRepositoryInterface {
        $repository = $this->createMock(TeacherRepositoryInterface::class);

        $map = [
            [ 'TEST', (new Teacher())->setExternalId('TEST') ],
            [ 'FOO', (new Teacher())->setExternalId('FOO') ]
        ];

        $repository
            ->method('findOneByExternalId')
            ->willReturnMap($map);

        return $repository;
    }

    private function getStudentRepositoryMock(): StudentRepositoryInterface {
        $repository = $this->createMock(StudentRepositoryInterface::class);

        $map = [
            [ '1234', (new Student())->setExternalId('1234') ],
            [ '9876', (new Student())->setExternalId('9876') ]
        ];

        $repository
            ->method('findOneByExternalId')
            ->willReturnMap($map);

        $repository
            ->method('findAllByExternalId')
            ->willReturnMap([
                [['1234', '4567', '9876'], [ (new Student())->setExternalId('1234'), (new Student())->setExternalId('9876')]],
                [ ['4567'], [ ]]
            ]);

        return $repository;
    }

    private function createUserMapper(): UserMapper {
        return new UserMapper(
            $this->getTypesMap(),
            $this->getTeacherRepositoryMock(),
            $this->getStudentRepositoryMock(),
            null
        );
    }

    public function testMapTeacherWithExistingTeacher() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'teacher',
            SamlClaimTypes::INTERNAL_ID => 'TEST'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Teacher()));
        $this->assertNotNull($user->getTeacher());
        $this->assertEquals('TEST', $user->getTeacher()->getExternalId());
    }

    public function testMapTeacherWithNonExistingTeacher() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'teacher',
            SamlClaimTypes::INTERNAL_ID => 'NOTEXISTING'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Teacher()));
        $this->assertNull($user->getTeacher());
    }

    public function testMapStudentWithExistingStudent() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'student',
            SamlClaimTypes::INTERNAL_ID => '1234'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Student()));
        $this->assertEquals(1, $user->getStudents()->count());
        $this->assertEquals('1234', $user->getStudents()->first()->getExternalId());
    }

    public function testMapStudentWithNonExistingStudent() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'student',
            SamlClaimTypes::INTERNAL_ID => '4567'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Student()));
        $this->assertEquals(0, $user->getStudents()->count());
    }

    public function testMapParentWithExistingStudents() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'parent',
            SamlClaimTypes::INTERNAL_ID => '1234,4567,9876'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Parent()));
        $this->assertEquals(2, $user->getStudents()->count());
        $this->assertEquals(['1234', '9876'], $user->getStudents()->map(function(Student $student) { return $student->getExternalId(); })->toArray());
    }

    public function mapParentWithNonExistingStudents() {
        $mapper = $this->createUserMapper();

        // Test with array
        $user = new User();
        $data = [
            ClaimTypes::GIVEN_NAME => 'Vorname',
            ClaimTypes::SURNAME => 'Nachname',
            ClaimTypes::EMAIL_ADDRESS => 'vorname.nachname@example.org',
            UserMapper::ROLES_ASSERTION_NAME => [
                'ROLE_USER'
            ],
            SamlClaimTypes::TYPE => 'parent',
            SamlClaimTypes::INTERNAL_ID => '4567'
        ];

        $mapper->mapUser($user, $data);

        $this->assertEquals('Vorname', $user->getFirstname());
        $this->assertEquals('Nachname', $user->getLastname());
        $this->assertEquals('vorname.nachname@example.org', $user->getEmail());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getUserType()->equals(UserType::Parent()));
        $this->assertEquals(0, $user->getStudents()->count());
    }

}