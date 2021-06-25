<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessagePollVote;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;

class PollResultViewHelper {

    private $studentRepository;
    private $teacherRepository;

    public function __construct(StudentRepositoryInterface $studentRepository, TeacherRepositoryInterface $teacherRepository) {
        $this->studentRepository = $studentRepository;
        $this->teacherRepository = $teacherRepository;
    }

    public function createView(Message $message): PollResultView {
        /** @var UserType[] $visibilities */
        $visibilities = $message->getPollUserTypes()->map(function(UserTypeEntity $visibility) {
            return $visibility->getUserType();
        });

        $students = [ ];
        $teachers = [ ];

        if(EnumArrayUtils::inArray(UserType::Teacher(), $visibilities)) {
            $teachers = $this->teacherRepository->findAll();
        }

        if(EnumArrayUtils::inArray(UserType::Student(), $visibilities) || EnumArrayUtils::inArray(UserType::Parent(), $visibilities)) {
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
     * @param Message $message
     * @return MessagePollVote[]
     */
    private function getStudentVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(function(MessagePollVote $vote) {
                return $vote->getUser()->getUserType()->equals(UserType::Student()) && $vote->getUser()->getStudents()->first() !== null;
            })->toArray();

        return ArrayUtils::createArrayWithKeys($votes, function(MessagePollVote $vote) {
            return $vote->getUser()->getStudents()->first()->getId();
        });
    }

    /**
     * Returns all teacher votes. Keys are the teacher ids.
     *
     * @param Message $message
     * @return MessagePollVote[]
     */
    private function getTeacherVotes(Message $message): array {
        $votes = $message->getPollVotes()
            ->filter(function(MessagePollVote $vote) {
                return $vote->getUser()->getUserType()->equals(UserType::Teacher()) && $vote->getUser()->getTeacher() !== null;
            })->toArray();

        return ArrayUtils::createArrayWithKeys($votes, function(MessagePollVote $vote) {
            return $vote->getUser()->getTeacher()->getId();
        });
    }
}