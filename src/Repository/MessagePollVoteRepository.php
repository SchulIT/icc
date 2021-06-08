<?php

namespace App\Repository;

use App\Entity\MessagePollVote;

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