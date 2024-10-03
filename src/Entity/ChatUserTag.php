<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ChatUserTag {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Chat::class, inversedBy: 'userTags')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Chat $chat;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: ChatTag::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ChatTag $tag;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private User $user;

    public function getChat(): Chat {
        return $this->chat;
    }

    public function setChat(Chat $chat): ChatUserTag {
        $this->chat = $chat;
        return $this;
    }

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): ChatUserTag {
        $this->user = $user;
        return $this;
    }

    public function getTag(): ChatTag {
        return $this->tag;
    }

    public function setTag(ChatTag $tag): ChatUserTag {
        $this->tag = $tag;
        return $this;
    }
}