<?php

namespace App\Entity;

use App\Validator\NoParentsDayAppointmentCollision;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[NoParentsDayAppointmentCollision]
class ParentsDayAppointment {

    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: ParentsDay::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ParentsDay $parentsDay;

    #[ORM\Column(type: 'boolean')]
    private bool $isBlocked = false;

    #[ORM\Column(type: 'time')]
    #[Assert\NotNull]
    private DateTime $start;

    #[ORM\Column(type: 'time')]
    #[Assert\NotNull]
    #[Assert\GreaterThan(propertyPath: 'start')]
    private DateTime $end;

    /**
     * @var Collection<Student>
     */
    #[ORM\ManyToMany(targetEntity: Student::class)]
    #[ORM\JoinTable]
    private Collection $students;

    /**
     * @var Collection<Teacher>
     */
    #[ORM\ManyToMany(targetEntity: Teacher::class)]
    #[ORM\JoinTable]
    #[Assert\Count(min: 1)]
    private Collection $teachers;

    #[ORM\Column(type: 'boolean')]
    private bool $isCancelled = false;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $cancelReason = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?User $cancelledBy = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
        $this->students = new ArrayCollection();
        $this->teachers = new ArrayCollection();
    }

    public function getParentsDay(): ParentsDay {
        return $this->parentsDay;
    }

    public function setParentsDay(ParentsDay $parentsDay): ParentsDayAppointment {
        $this->parentsDay = $parentsDay;
        return $this;
    }

    public function isBlocked(): bool {
        return $this->isBlocked;
    }

    public function setIsBlocked(bool $isBlocked): ParentsDayAppointment {
        $this->isBlocked = $isBlocked;
        return $this;
    }

    public function getStart(): DateTime {
        return $this->start;
    }

    public function setStart(DateTime $start): ParentsDayAppointment {
        $this->start = $start;
        return $this;
    }

    public function getEnd(): DateTime {
        return $this->end;
    }

    public function setEnd(DateTime $end): ParentsDayAppointment {
        $this->end = $end;
        return $this;
    }

    public function addStudent(Student $student): void {
        $this->students->add($student);
    }

    public function removeStudent(Student $student): void {
        $this->students->removeElement($student);
    }

    /**
     * @return Collection<Student>
     */
    public function getStudents(): Collection {
        return $this->students;
    }

    public function addTeacher(Teacher $teacher): void {
        $this->teachers->add($teacher);
    }

    public function removeTeacher(Teacher $teacher): void {
        $this->teachers->removeElement($teacher);
    }

    /**
     * @return Collection<Teacher>
     */
    public function getTeachers(): Collection {
        return $this->teachers;
    }

    public function isCancelled(): bool {
        return $this->isCancelled;
    }

    public function setIsCancelled(bool $isCancelled): ParentsDayAppointment {
        $this->isCancelled = $isCancelled;
        return $this;
    }

    public function getCancelReason(): ?string {
        return $this->cancelReason;
    }

    public function setCancelReason(?string $cancelReason): ParentsDayAppointment {
        $this->cancelReason = $cancelReason;
        return $this;
    }

    public function getStartDateTime(): DateTime {
        $date = clone $this->getParentsDay()->getDate();
        $date->setTime($this->getStart()->format('H'), $this->getStart()->format('i'), 0);

        return $date;
    }

    public function getEndDateTime(): DateTime {
        $date = clone $this->getParentsDay()->getDate();
        $date->setTime($this->getEnd()->format('H'), $this->getEnd()->format('i'), 0);

        return $date;
    }

    public function getCancelledBy(): ?User {
        return $this->cancelledBy;
    }

    public function setCancelledBy(?User $cancelledBy): ParentsDayAppointment {
        $this->cancelledBy = $cancelledBy;
        return $this;
    }
}