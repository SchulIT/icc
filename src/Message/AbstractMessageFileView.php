<?php

namespace App\Message;

use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

abstract class AbstractMessageFileView {
    /** @var Student[] */
    private $students;

    /** @var Teacher[] */
    private $teachers;

    /** @var array<int, User[]>  */
    private $studentUsersLookup;
    /** @var array<int, User[]> */
    private $parentUsersLookup;
    /** @var array<int, User[]>  */
    private $teacherUsersLookup;
    /** @var array<int, User>  */
    private $users;

    public function __construct(array $students, array $studentUsersLookup, array $parentUsersLookup, array $teachers, array $teacherUsersLookup, array $users) {
        $this->students = $students;
        $this->studentUsersLookup = $studentUsersLookup;
        $this->parentUsersLookup = $parentUsersLookup;
        $this->teachers = $teachers;
        $this->teacherUsersLookup = $teacherUsersLookup;
        $this->users = $users;
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