<?php

namespace App\Entity;

use DateTime;
use DH\DoctrineAuditBundle\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 * @Auditable()
 */
class Absence {

    use IdTrait;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull()
     * @var DateTime
     */
    private $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $lessonStart;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int|null
     */
    private $lessonEnd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Teacher")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Teacher|null
     */
    private $teacher;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StudyGroup")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var StudyGroup|null
     */
    private $studyGroup;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Room")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Room|null
     */
    private $room;

    /**
     * @return DateTime|null
     */
    public function getDate(): ?DateTime {
        return $this->date;
    }

    /**
     * @param DateTime|null $date
     * @return Absence
     */
    public function setDate(?DateTime $date): Absence {
        $this->date = $date;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    /**
     * @param int|null $lessonStart
     * @return Absence
     */
    public function setLessonStart(?int $lessonStart): Absence {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }

    /**
     * @param int|null $lessonEnd
     * @return Absence
     */
    public function setLessonEnd(?int $lessonEnd): Absence {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    /**
     * @return Teacher|null
     */
    public function getTeacher(): ?Teacher {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return Absence
     */
    public function setTeacher(Teacher $teacher): Absence {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return StudyGroup|null
     */
    public function getStudyGroup(): ?StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return Absence
     */
    public function setStudyGroup(StudyGroup $studyGroup): Absence {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Room|null
     */
    public function getRoom(): ?Room {
        return $this->room;
    }

    /**
     * @param Room $room
     * @return Absence
     */
    public function setRoom(Room $room): Absence {
        $this->room = $room;
        return $this;
    }
}