<?php

namespace App\Response\Api\V1;

use App\Entity\Section;
use JMS\Serializer\Annotation as Serializer;
use App\Entity\StudyGroupMembership as StudyGroupMembershipEntity;

class StudyGroupMembership {

    /**
     * @Serializer\SerializedName("study_group")
     * @Serializer\Type("App\Response\Api\V1\StudyGroup")
     */
    private ?StudyGroup $studyGroup = null;

    /**
     * @Serializer\SerializedName("student")
     * @Serializer\Type("App\Response\Api\V1\Student")
     */
    private ?Student $student = null;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     */
    private ?string $type = null;

    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    public function setStudyGroup(StudyGroup $studyGroup): StudyGroupMembership {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    public function getStudent(): Student {
        return $this->student;
    }

    public function setStudent(Student $student): StudyGroupMembership {
        $this->student = $student;
        return $this;
    }

    public function getType(): string {
        return $this->type;
    }

    public function setType(string $type): StudyGroupMembership {
        $this->type = $type;
        return $this;
    }

    public static function fromEntity(StudyGroupMembershipEntity $studyGroupMembershipEntity, ?Section $section = null): self {
        return (new self())
            ->setType($studyGroupMembershipEntity->getType())
            ->setStudent(Student::fromEntity($studyGroupMembershipEntity->getStudent(), $section))
            ->setStudyGroup(StudyGroup::fromEntity($studyGroupMembershipEntity->getStudyGroup()));
    }
}