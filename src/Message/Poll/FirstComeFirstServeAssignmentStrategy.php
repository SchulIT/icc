<?php

namespace App\Message\Poll;

use App\Entity\Message;
use App\Entity\MessagePollChoice;
use App\Entity\MessagePollVote;
use App\Repository\MessagePollVoteRepositoryInterface;
use Override;

readonly class FirstComeFirstServeAssignmentStrategy implements AssignmentStrategyInterface {

    public function __construct(
        private PollVoteHelper $pollVoteHelper,
        private MessagePollVoteRepositoryInterface $messagePollVoteRepository
    ) {

    }

    #[Override]
    public function assign(Message $message): AssignmentResult|null {
        if($message->isPollEnabled() === false) {
            return null;
        }

        /** @var MessagePollChoice[] $choices */
        $choices = $message->getPollChoices()->toArray();
        /** @var MessagePollVote[] $votes */
        $votes = $message->getPollVotes()->toArray();

        usort($votes, fn(MessagePollVote $voteA, MessagePollVote $voteB) => $voteA->getCreatedAt() <=> $voteB->getCreatedAt());

        $assignmentCounts = [ ];
        foreach($choices as $choice) {
            $assignmentCounts[$choice->getId()] = 0;
        }

        $assigned = [ ];
        $notAssigned = [ ];
        $log = null;

        foreach($votes as $vote) {
            $ranked = $this->pollVoteHelper->getRankedChoices($vote);
            ksort($ranked);

            foreach($ranked as $choice) {
                if($assignmentCounts[$choice->getId()] < $choice->getMaximum()) {
                    $assigned[] = $vote;
                    $vote->setAssignedChoice($choice);
                    $this->messagePollVoteRepository->persist($vote);
                    break;
                }
            }

            if($vote->getAssignedChoice() === null) {
                $notAssigned[] = $vote;
            }
        }

        return new AssignmentResult($assigned, $notAssigned, $log);
    }

    #[Override]
    public function getTranslationKey(): string {
        return 'message.poll.assignment_strategy.first_come_first_serve.label';
    }

    #[Override]
    public function getHelpTranslationKey(): string {
        return 'message.poll.assignment_strategy.first_come_first_serve.help';
    }
}