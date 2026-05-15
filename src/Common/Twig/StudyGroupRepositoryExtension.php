<?php

declare(strict_types=1);

namespace App\Common\Twig;

use App\Common\Entity\Grade;
use App\Common\Entity\Section;
use App\Common\Entity\StudyGroup;
use App\Common\Repository\StudyGroupRepositoryInterface;
use Twig\Attribute\AsTwigFunction;

readonly class StudyGroupRepositoryExtension {

    public function __construct(
        private StudyGroupRepositoryInterface $studyGroupRepository
    ) {

    }

    #[AsTwigFunction('get_study_group_by_grade')]
    public function getStudyGroupByGrade(Grade $grade, Section $section): ?StudyGroup {
        return $this->studyGroupRepository->findOneByGrade($grade, $section);
    }
}