<?php

namespace App\Request\Data;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class StudyGroupMembershipData {

    /**
     * Student ID.
     *
     * @Serializer\Type("string")
     * @Assert\NotBlank()
     * @var string
     */
    private $student;

    /**
     * List of external study group IDs which the student belongs to.
     *
     * @Serializer\Type("array<string>")
     * @var string[]
     */
    private $studyGroups;
}