<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class TimetableLessonAdditionalInformation {
    use IdTrait;
    use UuidTrait;

    #[ORM\Column(type: 'date')]
    #[Assert\NotNull]
    private DateTime $date;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThan(0)]
    private int $lessonStart;

    #[ORM\Column(type: 'integer')]
    #[Assert\GreaterThanOrEqual(propertyPath: 'lessonStart')]
    private int $lessonEnd;

    #[ORM\ManyToOne(targetEntity: StudyGroup::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private StudyGroup $studyGroup;

    #[ORM\ManyToOne(targetEntity: Teacher::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Teacher $author;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $commentTeacher;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(allowNull: true)]
    private ?string $commentStudents;

    public function __construct() {
        $this->uuid = Uuid::uuid4();
    }

    public function getDate(): DateTime {
        return $this->date;
    }

    public function setDate(DateTime $date): TimetableLessonAdditionalInformation {
        $this->date = $date;
        return $this;
    }

    public function getLessonStart(): int {
        return $this->lessonStart;
    }

    public function setLessonStart(int $lessonStart): TimetableLessonAdditionalInformation {
        $this->lessonStart = $lessonStart;
        return $this;
    }

    public function getLessonEnd(): int {
        return $this->lessonEnd;
    }

    public function setLessonEnd(int $lessonEnd): TimetableLessonAdditionalInformation {
        $this->lessonEnd = $lessonEnd;
        return $this;
    }

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): TimetableLessonAdditionalInformation {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public function getCommentTeacher(): ?string {
        return $this->commentTeacher;
    }

    public function setCommentTeacher(?string $commentTeacher): TimetableLessonAdditionalInformation {
        $this->commentTeacher = $commentTeacher;
        return $this;
    }

    public function getCommentStudents(): ?string {
        return $this->commentStudents;
    }

    public function setCommentStudents(?string $commentStudents): TimetableLessonAdditionalInformation {
        $this->commentStudents = $commentStudents;
        return $this;
    }

    public function getAuthor(): Teacher {
        return $this->author;
    }

    public function setAuthor(Teacher $author): TimetableLessonAdditionalInformation {
        $this->author = $author;
        return $this;
    }
}