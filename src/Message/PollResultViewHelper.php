<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Utils\ArrayUtils;

class PollResultViewHelper {

    public function __construct(private StudentRepositoryInterface $studentRepository, private TeacherRepositoryInterface $teacherRepository)
    {
    }

    public function createView(Message $message): PollResultView {
        /** @var UserType[] $visibilities */
        $visibilities = $message->getPollUserTypes()->map(fn(UserTypeEntity $visibility) => $visibility->getUserType());

        $students = [ ];
        $teachers = [ ];

        if(ArrayUtils::inArray(UserType::Teacher, $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
        }

        if(ArrayUtils::inArray(UserType::Student, $visibilities) || ArrayUtils::inArray(UserType::Parent, $visibilities)) {
            $students = $this->studentRepository->findAllByStudyGroups($message->getPollStudyGroups()->toArray());
        }

        return new PollResultView(
            $students,
            $this->getStudentVotes($message),
            $teachers,
            $this->getTeacherVotes($message)
        );
    }

    /**
     * Returns all student votes. Keys are the student ids.
     *
     * @return MessagePollVote[]
     */
    private function getStudentVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(fn(MessagePollVote $vote) => $vote->getUser()->isStudent() && $vote->getUser()->getStudents()->first() !== null)->toArray();

        return ArrayUtils::createArrayWithKeys($votes, fn(MessagePollVote $vote) => $vote->getUser()->getStudents()->first()->getId());
    }

    /**
     * Returns all teacher votes. Keys are the teacher ids.
     *
     * @return MessagePollVote[]
     */
    private function getTeacherVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(fn(MessagePollVote $vote) => $vote->getUser()->isTeacher() && $vote->getUser()->getTeacher() !== null)->toArray();

        return ArrayUtils::createArrayWithKeys($votes, fn(MessagePollVote $vote) => $vote->getUser()->getTeacher()->getId());
    }
}