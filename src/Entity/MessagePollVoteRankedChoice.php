<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity]
class MessagePollVoteRankedChoice {

    use IdTrait;

    #[Gedmo\SortableGroup]
    #[ORM\ManyToOne(targetEntity: MessagePollVote::class, inversedBy: 'choices')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MessagePollVote $vote = null;

    #[ORM\ManyToOne(targetEntity: MessagePollChoice::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?MessagePollChoice $choice = null;

    #[Gedmo\SortablePosition]
    #[ORM\Column(type: 'integer')]
    private int $rank = 1;

    public function getVote(): ?MessagePollVote {
        return $this->vote;
    }

    public function setVote(?MessagePollVote $vote): MessagePollVoteRankedChoice {
        $this->vote = $vote;
        return $this;
    }

    public function getChoice(): ?MessagePollChoice {
        return $this->choice;
    }

    public function setChoice(?MessagePollChoice $choice): MessagePollVoteRankedChoice {
        $this->choice = $choice;
        return $this;
    }

    public function getRank(): int {
        return $this->rank;
    }

    public function setRank(int $rank): MessagePollVoteRankedChoice {
        $this->rank = $rank;
        return $this;
    }
}