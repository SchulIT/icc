<?php

namespace App\Message;

use App\Entity\MessagePollVote;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

class PollResultView {

    /**
     * @param Student[] $students
     * @param MessagePollVote[] $studentVotes
     * @param MessagePollVote[] $parentVotes
     * @param Teacher[] $teachers
     * @param MessagePollVote[] $teacherVotes
     * @param User[] $users
     * @param MessagePollVote[] $userVotes
     */
    public function __construct(private readonly array $students, private readonly array $studentVotes, private readonly array $parentVotes, private readonly array $teachers, private readonly array $teacherVotes, private readonly array $users, private readonly array $userVotes)
    {
    }

    /**
     * @return Student[]
     */
    public function getStudents(): array {
        return $this->students;
    }

    /**
     * @return Teacher[]
     */
    public function getTeachers(): array {
        return $this->teachers;
    }

    public function getStudentVotesCount(): int {
        return count($this->studentVotes);
    }

    public function getTeacherVotesCount(): int {
        return count($this->teacherVotes);
    }

    public function getStudentVote(Student $student): ?MessagePollVote {
        return $this->studentVotes[$student->getId()] ?? null;
    }

    public function getTeacherVote(Teacher $teacher): ?MessagePollVote {
        return $this->teacherVotes[$teacher->getId()] ?? null;
    }

    public function getParentVote(Student $student): ?MessagePollVote {
        return $this->parentVotes[$student->getId()] ?? null;
    }

    /**
     * @return User[]
     */
    public function getUsers(): array {
        return $this->users;
    }

    public function getUserVote(User $user): ?MessagePollVote {
        return $this->userVotes[$user->getId()] ?? null;
    }
}