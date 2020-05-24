<?php

namespace App\Response\Api\V1;

use JMS\Serializer\Annotation as Serializer;
use App\Entity\StudyGroupMembership as StudyGroupMembershipEntity;

class StudyGroupMembership {

    /**
     * @Serializer\SerializedName("study_group")
     * @Serializer\Type("App\Response\Api\V1\StudyGroup")
     *
     * @var StudyGroup
     */
    private $studyGroup;

    /**
     * @Serializer\SerializedName("student")
     * @Serializer\Type("App\Response\Api\V1\Student")
     *
     * @var Student
     */
    private $student;

    /**
     * @Serializer\SerializedName("type")
     * @Serializer\Type("string")
     *
     * @var string
     */
    private $type;

    /**
     * @return StudyGroup
     */
    public function getStudyGroup(): StudyGroup {
        return $this->studyGroup;
    }

    /**
     * @param StudyGroup $studyGroup
     * @return StudyGroupMembership
     */
    public function setStudyGroup(StudyGroup $studyGroup): StudyGroupMembership {
        $this->studyGroup = $studyGroup;
        return $this;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return StudyGroupMembership
     */
    public function setStudent(Student $student): StudyGroupMembership {
        $this->student = $student;
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
     * @return StudyGroupMembership
     */
    public function setType(string $type): StudyGroupMembership {
        $this->type = $type;
        return $this;
    }

    public static function fromEntity(StudyGroupMembershipEntity $studyGroupMembershipEntity): self {
        return (new static())
            ->setType($studyGroupMembershipEntity->getType())
            ->setStudent(Student::fromEntity($studyGroupMembershipEntity->getStudent()))
            ->setStudyGroup(StudyGroup::fromEntity($studyGroupMembershipEntity->getStudyGroup()));
    }
}