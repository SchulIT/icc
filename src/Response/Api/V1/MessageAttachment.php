<?php

namespace App\Response\Api\V1;

use DateTime;
use DateTimeImmutable;
use App\Entity\MessageAttachment as MessageAttachmentEntity;
use JMS\Serializer\Annotation as Serializer;

class MessageAttachment {

    use UuidTrait;

    #[Serializer\SerializedName('filename')]
    #[Serializer\Type('string')]
    private ?string $filename = null;

    #[Serializer\SerializedName('size')]
    #[Serializer\Type('int')]
    private ?int $size = null;

    #[Serializer\SerializedName('updated_at')]
    #[Serializer\Type('DateTimeImmutable')]
    private ?DateTime $updatedAt = null;

    public function getFilename(): string {
        return $this->filename;
    }

    public function setFilename(string $filename): MessageAttachment {
        $this->filename = $filename;
        return $this;
    }

    public function getSize(): int {
        return $this->size;
    }

    public function setSize(int $size): MessageAttachment {
        $this->size = $size;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): MessageAttachment {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public static function fromEntity(MessageAttachmentEntity $messageAttachmentEntity): self {
        return (new self())
            ->setUuid($messageAttachmentEntity->getUuid())
            ->setFilename($messageAttachmentEntity->getFilename())
            ->setSize($messageAttachmentEntity->getSize())
            ->setUpdatedAt($messageAttachmentEntity->getUpdatedAt());
    }
}