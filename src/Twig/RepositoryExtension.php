<?php

namespace App\Twig;

use App\Entity\Grade;
use App\Entity\Section;
use App\Entity\StudyGroup;
use App\Repository\StudyGroupRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension {

    public function __construct(private StudyGroupRepositoryInterface $studyGroupRepository)
    {
    }

    public function getFunctions(): array {
        return [
            new TwigFunction('get_study_group_by_grade', [ $this, 'getStudyGroupByGrade' ])
        ];
    }

    public function getStudyGroupByGrade(Grade $grade, Section $section): ?StudyGroup {
        return $this->studyGroupRepository->findOneByGrade($grade, $section);
    }
}