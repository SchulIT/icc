<?php

namespace App\Entity;

use App\Validator\DateLessonGreaterThan;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity()
 */
class SickNote {

    use IdTrait;
    use UuidTrait;

    /**
     * @ORM\ManyToOne(targetEntity="Student")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @var Student
     */
    private $student;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @var DateLesson
     */
    private $from;

    /**
     * @ORM\Embedded(class="DateLesson")
     * @DateLessonGreaterThan(propertyPath="from")
     * @var DateLesson
     */
    private $until;

    /**
     * @Gedmo\Blameable(on="create")
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn()
     * @var User
     */
    private $createdBy;

    /**
     * @ORM\Column(type="datetime")
     * @Gedmo\Timestampable(on="create")
     * @var DateTime
     */
    private $createdAt;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return SickNote
     */
    public function setStudent(Student $student): SickNote {
        $this->student = $student;
        return $this;
    }

    /**
     * @return DateLesson
     */
    public function getFrom(): DateLesson {
        return $this->from;
    }

    /**
     * @param DateLesson $from
     * @return SickNote
     */
    public function setFrom(DateLesson $from): SickNote {
        $this->from = $from;
        return $this;
    }

    /**
     * @return DateLesson
     */
    public function getUntil(): DateLesson {
        return $this->until;
    }

    /**
     * @param DateLesson $until
     * @return SickNote
     */
    public function setUntil(DateLesson $until): SickNote {
        $this->until = $until;
        return $this;
    }

    /**
     * @return User
     */
    public function getCreatedBy(): User {
        return $this->createdBy;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime {
        return $this->createdAt;
    }
}