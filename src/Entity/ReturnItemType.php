<?php

namespace App\Entity;

use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[Auditable]
class ReturnItemType {

    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: Types::STRING, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 1, max: 255)]
    private ?string $displayName = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank]
    private ?string $note = null;

    #[ORM\Column(type: Types::TEXT, nullable: false)]
    #[Assert\NotBlank]
    private ?string $notificationNote = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getDisplayName(): ?string {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): void {
        $this->displayName = $displayName;
    }

    public function getNote(): ?string {
        return $this->note;
    }

    public function setNote(?string $note): void {
        $this->note = $note;
    }

    public function getNotificationNote(): ?string {
        return $this->notificationNote;
    }

    public function setNotificationNote(?string $notificationNote): ReturnItemType {
        $this->notificationNote = $notificationNote;
        return $this;
    }
}