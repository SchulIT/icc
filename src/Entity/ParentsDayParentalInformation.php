<?php

namespace App\Entity;

use Ambta\DoctrineEncryptBundle\Configuration\Encrypted;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity]
#[ORM\UniqueConstraint(fields: ['parentsDay', 'teacher', 'student'])]
class ParentsDayParentalInformation {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: ParentsDay::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?ParentsDay $parentsDay;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Teacher $teacher;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?Student $student;

    #[ORM\Column(type: 'boolean')]
    private bool $isAppointmentCancelled = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isAppointmentNotNecessary = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isAppointmentRequested = false;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 255)]
    #[Encrypted]
    private ?string $comment = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getParentsDay(): ParentsDay {
        return $this->parentsDay;
    }

    public function setParentsDay(ParentsDay $parentsDay): ParentsDayParentalInformation {
        $this->parentsDay = $parentsDay;
        return $this;
    }

    public function getTeacher(): Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): ParentsDayParentalInformation {
        $this->teacher = $teacher;
        return $this;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): ParentsDayParentalInformation {
        $this->student = $student;
        return $this;
    }

    public function isAppointmentCancelled(): bool {
        return $this->isAppointmentCancelled;
    }

    public function setIsAppointmentCancelled(bool $isAppointmentCancelled): ParentsDayParentalInformation {
        $this->isAppointmentCancelled = $isAppointmentCancelled;
        return $this;
    }

    public function isAppointmentNotNecessary(): bool {
        return $this->isAppointmentNotNecessary;
    }

    public function setIsAppointmentNotNecessary(bool $isAppointmentNotNecessary): ParentsDayParentalInformation {
        $this->isAppointmentNotNecessary = $isAppointmentNotNecessary;
        return $this;
    }

    public function isAppointmentRequested(): bool {
        return $this->isAppointmentRequested;
    }

    public function setIsAppointmentRequested(bool $isAppointmentRequested): ParentsDayParentalInformation {
        $this->isAppointmentRequested = $isAppointmentRequested;
        return $this;
    }

    public function getComment(): ?string {
        return $this->comment;
    }

    public function setComment(?string $comment): ParentsDayParentalInformation {
        $this->comment = $comment;
        return $this;
    }

    #[Assert\Callback]
    public function ensureRequestedAndNotNecessaryNotCheckedAtTheSameTime(ExecutionContextInterface $context, mixed $payload): void {
        if($this->isAppointmentRequested === true && $this->isAppointmentNotNecessary === true) {
            $context->buildViolation('This should not be set to true if the other option is set to true as well.')
                ->atPath('isAppointmentRequested')
                ->addViolation();

            $context->buildViolation('This should not be set to true if the other option is set to true as well.')
                ->atPath('isAppointmentNotNecessary')
                ->addViolation();
        }
    }
}