<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use App\Entity\Student;
use App\Entity\Teacher;
use App\Entity\User;

class PollResultView {

    /** @var Student[]|array  */
    private $students;
    /** @var Teacher[]|array  */
    private $teachers;

    /** @var MessagePollVote[] */
    private $studentVotes;
    /** @var MessagePollVote[]  */
    private $teacherVotes;

    /**
     * @param MessagePollVote[] $studentVotes
     * @param Teacher[] $teachers
     * @param MessagePollVote[] $teacherVotes
     */
    public function __construct(array $students, array $studentVotes, array $teachers, array $teacherVotes) {
        $this->students = $students;
        $this->teachers = $teachers;

        $this->studentVotes = $studentVotes;
        $this->teacherVotes = $teacherVotes;
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

}