<?php

namespace App\Message\Repository;

use App\Message\Entity\MessagePollVote;

interface MessagePollVoteRepositoryInterface {
    public function persist(MessagePollVote $vote): void;

    public function remove(MessagePollVote $vote): void;
}