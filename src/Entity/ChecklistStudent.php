<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class ChecklistStudent {

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Checklist::class, inversedBy: 'students')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Checklist $checklist;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private Student $student;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isChecked = false;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    #[Assert\Length(max: 255)]
    private ?string $comment = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Gedmo\Timestampable(on: 'update')]
    private DateTime $updatedAt;

    public function getChecklist(): Checklist {
        return $this->checklist;
    }

    public function setChecklist(Checklist $checklist): ChecklistStudent {
        $this->checklist = $checklist;
        return $this;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): ChecklistStudent {
        $this->student = $student;
        return $this;
    }

    public function isChecked(): bool {
        return $this->isChecked;
    }

    public function setIsChecked(bool $isChecked): ChecklistStudent {
        $this->isChecked = $isChecked;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): ChecklistStudent {
        $this->comment = $comment;
        return $this;
    }

    public function getUpdatedAt(): DateTime {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): ChecklistStudent {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}