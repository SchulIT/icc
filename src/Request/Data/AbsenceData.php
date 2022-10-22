<?php

namespace App\Request\Data;

use DateTime;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AbsenceData {

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    private ?string $objective = null;

    /**
     * @Serializer\Type("string")
     */
    #[Assert\NotBlank]
    #[Assert\Choice(['study_group', 'teacher', 'room'])]
    private ?string $type = null;

    /**
     * @Serializer\Type("DateTime<'Y-m-d\TH:i:s'>")
     */
    #[Assert\NotNull]
    private ?DateTime $date = null;

    /**
     * @Serializer\Type("int")
     */
    private ?int $lessonStart = null;

    /**
     * @Serializer\Type("int")
     */
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private ?int $lessonEnd = null;

    public function getObjective(): string {
        return $this->objective;
    }

    public function setObjective(string $objective): AbsenceData {
        $this->objective = $objective;
        return $this;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): AbsenceData {
        $this->type = $type;
        return $this;
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): AbsenceData {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): ?int {
        return $this->lessonStart;
    }

    public function setLessonStart(?int $lessonStart): AbsenceData {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): ?int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(?int $lessonEnd): AbsenceData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }
}