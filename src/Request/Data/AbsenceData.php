<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AbsenceData {

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $objective;

    /**
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @Assert\Choice({"study_group", "teacher"})
     * @var string
     */
    private $type;

    /**
     * @Serializer\Type("datetime")
     * @Assert\NotBlank()
     * @Assert\Date()
     * @var \DateTime
     */
    private $date;

    /**
     * @Serializer\Type("int")
     * @var int|null
     */
    private $lessonStart = null;

    /**
     * @Serializer\Type("int")
     * @Assert\GreaterThanOrEqual(propertyPath="lessonStart")
     * @var int|null
     */
    private $lessonEnd = null;

    /**
     * @return string
     */
    public function getObjective(): string {
        return $this->objective;
    }

    /**
     * @param string $objective
     * @return AbsenceData
     */
    public function setObjective(string $objective): AbsenceData {
        $this->objective = $objective;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param string $type
     * @return AbsenceData
     */
    public function setType(string $type): AbsenceData {
        $this->type = $type;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return AbsenceData
     */
    public function setDate(\DateTime $date): AbsenceData {
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
     * @return AbsenceData
     */
    public function setLessonStart(?int $lessonStart): AbsenceData {
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
     * @return AbsenceData
     */
    public function setLessonEnd(?int $lessonEnd): AbsenceData {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }
}