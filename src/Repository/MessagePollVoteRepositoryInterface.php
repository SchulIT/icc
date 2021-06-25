<?php

namespace App\Repository;

use App\Entity\MessagePollVote;

interface MessagePollVoteRepositoryInterface {
    public function persist(MessagePollVote $vote): void;

    public function remove(MessagePollVote $vote): void;
}