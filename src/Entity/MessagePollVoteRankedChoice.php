<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity()
 */
class MessagePollVoteRankedChoice {

    use IdTrait;

    /**
     * @Gedmo\SortableGroup()
     * @ORM\ManyToOne(targetEntity="MessagePollVote", inversedBy="choices")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var MessagePollVote|null
     */
    private $vote;

    /**
     * @ORM\ManyToOne(targetEntity="MessagePollChoice")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var MessagePollChoice|null
     */
    private $choice;

    /**
     * @Gedmo\SortablePosition()
     * @ORM\Column(type="integer")
     * @var int
     */
    private $rank = 1;

    /**
     * @return MessagePollVote|null
     */
    public function getVote(): ?MessagePollVote {
        return $this->vote;
    }

    /**
     * @param MessagePollVote|null $vote
     * @return MessagePollVoteRankedChoice
     */
    public function setVote(?MessagePollVote $vote): MessagePollVoteRankedChoice {
        $this->vote = $vote;
        return $this;
    }

    /**
     * @return MessagePollChoice|null
     */
    public function getChoice(): ?MessagePollChoice {
        return $this->choice;
    }

    /**
     * @param MessagePollChoice|null $choice
     * @return MessagePollVoteRankedChoice
     */
    public function setChoice(?MessagePollChoice $choice): MessagePollVoteRankedChoice {
        $this->choice = $choice;
        return $this;
    }

    /**
     * @return int
     */
    public function getRank(): int {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return MessagePollVoteRankedChoice
     */
    public function setRank(int $rank): MessagePollVoteRankedChoice {
        $this->rank = $rank;
        return $this;
    }
}