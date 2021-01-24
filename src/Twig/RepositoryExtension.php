<?php

namespace App\Twig;

use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Repository\StudyGroupRepositoryInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RepositoryExtension extends AbstractExtension {

    private $studyGroupRepository;

    public function __construct(StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->studyGroupRepository = $studyGroupRepository;
    }

    public function getFunctions() {
        return [
            new TwigFunction('get_study_group_by_grade', [ $this, 'getStudyGroupByGrade' ])
        ];
    }

    /**
     * @param Grade $grade
     * @return StudyGroup|null
     */
    public function getStudyGroupByGrade(Grade $grade): ?StudyGroup {
        return $this->studyGroupRepository->findOneByGrade($grade);
    }
}