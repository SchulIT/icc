<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class MessageVisibility {

    /**
     * @ORM\Id()
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="visibilities", cascade={"persist"})
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Message
     */
    private $message;

    /**
     * @ORM\Id()
     * @ORM\Column(type="UserType::class")
     * @var UserType
     */
    private $userType;

    /**
     * @return Message
     */
    public function getMessage(): Message {
        return $this->message;
    }

    /**
     * @param Message $message
     * @return MessageVisibility
     */
    public function setMessage(Message $message): MessageVisibility {
        $this->message = $message;
        return $this;
    }

    /**
     * @return UserType
     */
    public function getUserType(): UserType {
        return $this->userType;
    }

    /**
     * @param UserType $userType
     * @return MessageVisibility
     */
    public function setUserType(UserType $userType): MessageVisibility {
        $this->userType = $userType;
        return $this;
    }

}