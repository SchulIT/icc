<?php

namespace App\Entity;

use App\Notification\NotificationDeliveryTarget;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class UserNotificationSetting {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private User $user;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING)]
    private string $type;

    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, enumType: NotificationDeliveryTarget::class)]
    private NotificationDeliveryTarget $target;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isEnabled = true;

    public function getUser(): User {
        return $this->user;
    }

    public function setUser(User $user): UserNotificationSetting {
        $this->user = $user;
        return $this;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): UserNotificationSetting {
        $this->type = $type;
        return $this;
    }

    public function getTarget(): NotificationDeliveryTarget {
        return $this->target;
    }

    public function setTarget(NotificationDeliveryTarget $target): UserNotificationSetting {
        $this->target = $target;
        return $this;
    }

    public function isEnabled(): bool {
        return $this->isEnabled;
    }

    public function setIsEnabled(bool $isEnabled): UserNotificationSetting {
        $this->isEnabled = $isEnabled;
        return $this;
    }
}