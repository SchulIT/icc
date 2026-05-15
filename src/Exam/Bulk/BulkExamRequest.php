<?php

namespace App\Exam\Bulk;

use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\Subject;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

class BulkExamRequest {

    #[Assert\NotNull]
    public Section|null $section = null;

    #[GreaterThanOrEqual(1)]
    public int $numberOfExams = 3;

    /**
     * @var Grade[]
     */
    #[Assert\Count(min: 1)]
    public array $grades = [ ];

    /**
     * @var Subject[]
     */
    #[Assert\Count(min: 1)]
    public array $subjects = [ ];

    public bool $addStudents = true;

    public bool $canEdit = true;
}