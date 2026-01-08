<?php

namespace App\Message\Poll;

use App\Entity\Message;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class PollVoteAssigner {
    public function __construct(
        #[AutowireIterator(AssignmentStrategyInterface::AUTOCONFIGURE_TAG)] private iterable $strategies
    ) { }

    /**
     * @return AssignmentStrategyInterface[]
     */
    public function getStrategies(): array {
        return iterator_to_array($this->strategies);
    }

    public function assign(Message $message, AssignmentStrategyInterface $strategy): AssignmentResult|null {
        return $strategy->assign($message);
    }
}