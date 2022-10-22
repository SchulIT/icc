<?php

namespace App\Message;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

abstract class AbstractMessageFileView {
    /**
     * @param Student[] $students
     * @param Teacher[] $teachers
     * @param array<int, User[]> $studentUsersLookup
     * @param array<int, User[]> $parentUsersLookup
     * @param array<int, User[]> $teacherUsersLookup
     * @param User[] $users
     */
    public function __construct(private array $students, private array $studentUsersLookup, private array $parentUsersLookup, private array $teachers, private array $teacherUsersLookup, private array $users)
    {
    }

    public function getStudents() {
        return $this->students;
    }

    public function getTeachers() {
        return $this->teachers;
    }

    public function getUsers() {
        return array_values($this->users);
    }

    public function getStudentUsers(Student $student) {
        return $this->studentUsersLookup[$student->getId()] ?? [ ];
    }

    public function getParentUsers(Student $student) {
        return $this->parentUsersLookup[$student->getId()] ??  [ ];
    }

    public function getTeacherUsers(Teacher $teacher) {
        return $this->teacherUsersLookup[$teacher->getId()] ?? [ ];
    }

    public function getAllUsers(): array {
        $users = $this->users;

        foreach($this->studentUsersLookup as $id => $studentUsers) {
            $users = array_merge($users, $studentUsers);
        }

        foreach($this->parentUsersLookup as $id => $parentUsers) {
            $users = array_merge($users, $parentUsers);
        }

        foreach($this->teacherUsersLookup as $id => $teacherUsers) {
            $users = array_merge($users, $teacherUsers);
        }

        return $users;
    }
}