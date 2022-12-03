<?php

namespace App\Entity;

use DateTime;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Auditable]
#[ORM\Entity]
class Absence {

    use IdTrait;

    #[Assert\NotNull]
    #[ORM\Column(type: 'date')]
    private ?DateTime $date = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lessonStart = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $lessonEnd = null;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Teacher $teacher = null;

    #[ORM\ManyToOne(targetEntity: StudyGroup::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?StudyGroup $studyGroup = null;

    #[ORM\ManyToOne(targetEntity: Room::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Room $room = null;

    public function getDate(): ?DateTime {
        return $this->date;
    }

    public function setDate(?DateTime $date): Absence {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    public function setLessonStart(?int $lessonStart): Absence {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(?int $lessonEnd): Absence {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher): Absence {
        $this->teacher = $teacher;
        return $this;
    }

    public function getStudyGroup(): ?StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): Absence {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public function getRoom(): ?Room {
        return $this->room;
    }

    public function setRoom(Room $room): Absence {
        $this->room = $room;
        return $this;
    }
}