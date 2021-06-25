<?php

namespace App\Message;

use App\Entity\Message;
use App\Entity\MessagePollChoice;
use App\Entity\MessagePollVote;
use App\Entity\MessagePollVoteRankedChoice;
use App\Entity\User;
use App\Repository\MessagePollVoteRepositoryInterface;
use App\Utils\ArrayUtils;

class PollVoteHelper {

    private $repository;

    public function __construct(MessagePollVoteRepositoryInterface $repository) {
        $this->repository = $repository;
    }

    public function getPollVote(Message $message, User $user): ?MessagePollVote {
        /** @var MessagePollVote $vote */
        foreach($message->getPollVotes() as $vote) {
            if($vote->getUser() === $user) {
                return $vote;
            }
        }

        return null;
    }

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
     * @param Message $message
     * @param User $user
     * @param MessagePollChoice[] $choices Choices in the correct order
     */
    public function persistVote(Message $message, User $user, array $choices) {
        $vote = $this->getPollVote($message, $user);

        if($vote !== null) {
            $this->repository->remove($vote);
        }

        $vote = (new MessagePollVote())
            ->setMessage($message)
            ->setUser($user);

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