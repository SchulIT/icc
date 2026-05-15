<?php

namespace App\Message\Repository;

use App\Framework\Repository\AbstractRepository;
use App\Message\Entity\MessagePollVote;
use App\Message\Repository\MessagePollVoteRepositoryInterface;

class MessagePollVoteRepository extends AbstractRepository implements MessagePollVoteRepositoryInterface {

    public function persist(MessagePollVote $vote): void {
        $this->em->persist($vote);
        $this->em->flush();
    }

    public function remove(MessagePollVote $vote): void {
        $this->em->remove($vote);
        $this->em->flush();
    }
}