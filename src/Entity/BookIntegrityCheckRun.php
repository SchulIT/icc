<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class BookIntegrityCheckRun {
    use IdTrait;
    use UuidTrait;

    #[ORM\ManyToOne(targetEntity: Student::class)]
    #[ORM\JoinColumn(unique: true, nullable: false, onDelete: 'CASCADE')]
    private Student $student;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?DateTime $lastRun = null;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): BookIntegrityCheckRun {
        $this->student = $student;
        return $this;
    }

    /**
     * @param DateTime $lastRun
     * @return BookIntegrityCheckRun
     */
    public function setLastRun(DateTime $lastRun): BookIntegrityCheckRun {
        $this->lastRun = $lastRun;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getLastRun(): ?DateTime {
        return $this->lastRun;
    }
}