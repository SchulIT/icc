<?php

namespace App\Message;

use App\Entity\MessageConfirmation;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

class MessageConfirmationView {

    /**
     * MessageConfirmationView constructor.
     * @param Student[] $students
     * @param MessageConfirmation[] $studentConfirmations
     * @param MessageConfirmation[] $parentConfirmations
     * @param Teacher[] $teachers
     * @param MessageConfirmation[] $teacherConfirmations
     * @param User[] $users
     * @param MessageConfirmation[] $userConfirmations
     */
    public function __construct(private array $students, private array $studentConfirmations, private array $parentConfirmations, private array $teachers, private array $teacherConfirmations, private array $users, private array $userConfirmations)
    {
    }

    /**
     * @return Student[]
     */
    public function getStudents() {
        return $this->students;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers() {
        return $this->teachers;
    }

    /**
     * @return User[]
     */
    public function getUsers() {
        return $this->users;
    }

    public function getStudentConfirmation(Student $student): ?MessageConfirmation {
        return $this->studentConfirmations[$student->getId()] ?? null;
    }

    public function getParentConfirmation(Student $student): ?MessageConfirmation {
        return $this->parentConfirmations[$student->getId()] ?? null;
    }

    public function getTeacherConfirmation(Teacher $teacher): ?MessageConfirmation {
        return $this->teacherConfirmations[$teacher->getId()] ?? null;
    }

    public function getUserConfirmation(User $user): ?MessageConfirmation {
        return $this->userConfirmations[$user->getId()] ?? null;
    }

}