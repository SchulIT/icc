<?php

namespace App\Message;

use App\Entity\MessageConfirmation;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

class MessageConfirmationView {

    /** @var Student[]|array  */
    private $students;
    /** @var Teacher[]|array  */
    private $teachers;
    /** @var User[]|array  */
    private $users;

    /** @var MessageConfirmation[] */
    private $studentConfirmations;
    /** @var MessageConfirmation[] */
    private $parentConfirmations;
    /** @var MessageConfirmation[]  */
    private $teacherConfirmations;
    /** @var MessageConfirmation[]  */
    private $userConfirmations;

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
    public function __construct(array $students, array $studentConfirmations, array $parentConfirmations, array $teachers, array $teacherConfirmations, array $users, array $userConfirmations) {
        $this->students = $students;
        $this->teachers = $teachers;
        $this->users = $users;

        $this->studentConfirmations = $studentConfirmations;
        $this->parentConfirmations = $parentConfirmations;
        $this->teacherConfirmations = $teacherConfirmations;
        $this->userConfirmations = $userConfirmations;
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