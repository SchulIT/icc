<?php

namespace App\Message\Poll;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Message\Poll\PollResultView;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Utils\ArrayUtils;

class PollResultViewHelper {

    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly TeacherRepositoryInterface $teacherRepository, private readonly UserRepositoryInterface $userRepository)
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

        /** @var UserType[] $remainingUserTypes */
        $remainingUserTypes = ArrayUtils::remove($visibilities,
            [
                UserType::Student,
                UserType::Teacher,
                UserType::Parent
            ]);

        $users = $this->userRepository->findAllByUserTypes($remainingUserTypes);

        return new PollResultView(
            $students,
            $this->getStudentVotes($message),
            $this->getParentVotes($message),
            $teachers,
            $this->getTeacherVotes($message),
            $users,
            $this->getUserVotes($message)
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
     * @param Message $message
     * @return MessagePollVote[]
     */
    private function getParentVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(fn(MessagePollVote $vote) => $vote->getUser()->isParent() && $vote->getStudent() !== null)
            ->toArray();

        return ArrayUtils::createArrayWithKeys($votes, fn(MessagePollVote $vote) => $vote->getStudent()->getId());
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

    /**
     * Returns all normal user (non-student/-parent/-teacher) votes. Keys are the user ids.
     *
     * @param Message $message
     * @return MessagePollVote[]
     */
    private function getUserVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(fn(MessagePollVote $vote) => !ArrayUtils::inArray($vote->getUser()->getUserType(), [
                UserType::Student,
                UserType::Parent,
                UserType::Teacher
            ]))->toArray();

        return ArrayUtils::createArrayWithKeys($votes, fn(MessagePollVote $vote) => $vote->getUser()->getId());
    }
}