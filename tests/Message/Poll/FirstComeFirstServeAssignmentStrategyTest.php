<?php

namespace App\Tests\Message\Poll;

use App\Entity\Message;
use App\Entity\MessagePollChoice;
use App\Entity\MessagePollVote;
use App\Entity\MessagePollVoteRankedChoice;
use App\Message\Poll\FirstComeFirstServeAssignmentStrategy;
use App\Message\Poll\PollVoteHelper;
use App\Repository\MessagePollVoteRepositoryInterface;
use App\Utils\ArrayUtils;
use DateTime;
use PHPUnit\Framework\TestCase;

class FirstComeFirstServeAssignmentStrategyTest extends TestCase {

    private static int $voteId = 1;

    private function getMessageChoiceMock(int $id, string $label, int $maximum): MessagePollChoice {
        $mock = $this->createMock(MessagePollChoice::class);
        $mock->method('getId')->willReturn($id);
        $mock->method('getLabel')->willReturn($label);
        $mock->method('getMaximum')->willReturn($maximum);

        return $mock;
    }

    private function getMessage(): Message {
        $message = new Message();
        $message->setIsPollEnabled(true);
        $message->addPollChoice($this->getMessageChoiceMock(1, 'A', 3));
        $message->addPollChoice($this->getMessageChoiceMock(2, 'B', 2));
        $message->addPollChoice($this->getMessageChoiceMock(3, 'C', 1));

        return $message;
    }

    private function getMessagePollVoteMock(Message $message, array $voteChoices, DateTime $createdAt): MessagePollVote {
        $choices = ArrayUtils::createArrayWithKeys(
            $message->getPollChoices()->toArray(),
            fn(MessagePollChoice $choice) => $choice->getLabel()
        );

        $vote = $this->getMockBuilder(MessagePollVote::class)
            ->onlyMethods(['getId', 'getCreatedAt'])
            ->getMock();
        $vote->method('getId')->willReturn(self::$voteId++);
        $vote->method('getCreatedAt')->willReturn($createdAt);


        foreach($voteChoices as $rank => $choiceId) {
            $rankedChoice = $this->getMockBuilder(MessagePollVoteRankedChoice::class)
                ->onlyMethods(['getRank', 'getVote', 'getChoice'])
                ->getMock();
            $rankedChoice->method('getRank')->willReturn($rank);
            $rankedChoice->method('getVote')->willReturn($vote);
            $rankedChoice->method('getChoice')->willReturn($choices[$choiceId]);

            $vote->addChoice($rankedChoice);
        }

        return $vote;
    }

    public function testNotAllFirstRank() {
        $message = $this->getMessage();

        $message->addPollVote(
            $vote1 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'B', 3 => 'C'], new DateTime('2025-12-01 00:00:00')) // Result: A
        );
        $message->addPollVote(
            $vote2 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'B', 3 => 'C'], new DateTime('2025-12-08 00:00:00')) // Result: B
        );
        $message->addPollVote(
            $vote3 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'B', 3 => 'C'], new DateTime('2025-12-05 00:00:00')) // Result: B
        );
        $message->addPollVote(
            $vote4 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'C', 3 => 'B'], new DateTime('2025-12-02 00:00:00')) // Result: A
        );
        $message->addPollVote(
            $vote5 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'C', 3 => 'B' ], new DateTime('2025-12-03 00:00:00')) // Result: A
        );
        $message->addPollVote(
            $vote6 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'C', 3 => 'B'], new DateTime('2025-12-04 00:00:00')) // Result: C
        );
        $message->addPollVote(
            $vote7 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'C', 3 => 'B'], new DateTime('2025-12-09 00:00:00')) // Result: not assigned
        );

        $assigner = new FirstComeFirstServeAssignmentStrategy(new PollVoteHelper($this->createMock(MessagePollVoteRepositoryInterface::class)), $this->createMock(MessagePollVoteRepositoryInterface::class));
        $result = $assigner->assign($message);

        $this->assertCount(6, $result->assigned);
        $this->assertCount(1, $result->notAssigned);

        $this->assertNotNull($vote1->getAssignedChoice());
        $this->assertEquals('A', $vote1->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote2->getAssignedChoice());
        $this->assertEquals('B', $vote2->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote3->getAssignedChoice());
        $this->assertEquals('B', $vote3->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote4->getAssignedChoice());
        $this->assertEquals('A', $vote4->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote5->getAssignedChoice());
        $this->assertEquals('A', $vote5->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote6->getAssignedChoice());
        $this->assertEquals('C', $vote6->getAssignedChoice()->getLabel());

        $this->assertNull($vote7->getAssignedChoice());
    }

    public function testAllFirstRank() {
        $message = $this->getMessage();

        $message->addPollVote(
            $vote1 = $this->getMessagePollVoteMock($message, [ 1 => 'A', 2 => 'B', 3 => 'C'], new DateTime('2025-12-01 00:00:00')) // Result: A
        );
        $message->addPollVote(
            $vote2 = $this->getMessagePollVoteMock($message, [ 1 => 'B', 2 => 'A', 3 => 'C'], new DateTime('2025-12-01 00:00:00')) // Result: B
        );
        $message->addPollVote(
            $vote3 = $this->getMessagePollVoteMock($message, [ 1 => 'C', 2 => 'A', 3 => 'B' ], new DateTime('2025-12-01 00:00:00')) // Result: C
        );

        $assigner = new FirstComeFirstServeAssignmentStrategy(new PollVoteHelper($this->createMock(MessagePollVoteRepositoryInterface::class)), $this->createMock(MessagePollVoteRepositoryInterface::class));
        $result = $assigner->assign($message);

        $this->assertCount(3, $result->assigned);
        $this->assertCount(0, $result->notAssigned);

        $this->assertNotNull($vote1->getAssignedChoice());
        $this->assertEquals('A', $vote1->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote2->getAssignedChoice());
        $this->assertEquals('B', $vote2->getAssignedChoice()->getLabel());

        $this->assertNotNull($vote1->getAssignedChoice());
        $this->assertEquals('C', $vote3->getAssignedChoice()->getLabel());
    }
}