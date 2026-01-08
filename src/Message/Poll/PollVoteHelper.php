<?php

namespace App\Message\Poll;

use App\Entity\Message;
use App\Entity\MessagePollChoice;
use App\Entity\MessagePollVote;
use App\Entity\MessagePollVoteRankedChoice;
use App\Entity\Student;
use App\Entity\User;
use App\Repository\MessagePollVoteRepositoryInterface;
use App\Utils\ArrayUtils;
use InvalidArgumentException;

class PollVoteHelper {

    public function __construct(private MessagePollVoteRepositoryInterface $repository)
    {
    }

    public function getPollVote(Message $message, User $user, Student|null $student = null): ?MessagePollVote {
        if($user->isParent() && $student === null) {
            throw new InvalidArgumentException('You must provide a student');
        }

        /** @var MessagePollVote $vote */
        foreach($message->getPollVotes() as $vote) {
            if($user->isParent() && $vote->getStudent()?->getId() === $student->getId()) {
                return $vote;
            } else if(!$user->isParent() && $vote->getUser() === $user) {
                return $vote;
            }
        }

        return null;
    }

    /**
     * @param MessagePollVote|null $vote
     * @return MessagePollChoice[]
     */
    public function getRankedChoices(?MessagePollVote $vote): array {
        if($vote === null) {
            return [ ];
        }

        $result = [ ];

        /** @var MessagePollVoteRankedChoice $choice */
        foreach($vote->getChoices() as $choice) {
            $result[$choice->getRank()] = $choice->getChoice();
        }

        return array_values($result);
    }

    /**
     * @param MessagePollChoice[] $choices Choices in the correct order
     */
    public function persistVote(Message $message, User $user, array $choices, Student|null $student = null): void {
        $vote = $this->getPollVote($message, $user, $student);

        if($vote !== null) {
            $this->repository->remove($vote);
        }

        $vote = (new MessagePollVote())
            ->setMessage($message)
            ->setUser($user)
            ->setStudent($student);

        $count = 0;
        foreach($choices as $rank => $choice) {
            if($count >= $message->getPollNumChoices()) {
                break;
            }

            $vote->addChoice(
                (new MessagePollVoteRankedChoice())
                ->setChoice($choice)
                ->setRank($rank)
                ->setVote($vote)
            );
            $count++;
        }

        $this->repository->persist($vote);
    }
}