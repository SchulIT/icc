<?php

namespace App\LearningManagementSystem\Export;

use App\Framework\Csv\CsvHelper;
use App\LearningManagementSystem\Entity\LearningManagementSystem;
use App\Common\Entity\Student;
use App\Common\Entity\StudyGroup;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class LearningManagementSystemInfoCsvExporter {
    public function __construct(private readonly StudentRepositoryInterface $studentRepository, private readonly Sorter $sorter,
                                private readonly CsvHelper $csvHelper, private readonly SectionResolverInterface $sectionResolver, private readonly TranslatorInterface $translator) { }

    /**
     * @param LearningManagementSystem $lms
     * @param StudyGroup $studyGroup
     * @return string[][]
     */
    public function getRows(LearningManagementSystem $lms, StudyGroup $studyGroup) {
        $rows = [ ];

        // Header
        $rows[] = [
            $this->translator->trans('label.firstname'),
            $this->translator->trans('label.lastname'),
            $this->translator->trans('label.grade'),
            $this->translator->trans('label.email'),
            $this->translator->trans('label.username'),
            $this->translator->trans('label.password'),
            $this->translator->trans('lists.lms.is_consent_obtained'),
            $this->translator->trans('lists.lms.is_consented'),
            $this->translator->trans('lists.lms.is_audio_consented'),
            $this->translator->trans('lists.lms.is_video_consented')
        ];

        $students = $this->studentRepository->findAllByStudyGroups([$studyGroup]);
        $this->sorter->sort($students, StudentStrategy::class);

        foreach($students as $student) {
            $row = [
                $student->getFirstname(),
                $student->getLastname(),
                $student->getGrade($this->sectionResolver->getCurrentSection())?->getName(),
                $student->getEmail()
            ];

            $info = $student->getLearningManagementSystemInfo($lms);

            if($info !== null) {
                $row[] = $info->getUsername();
                $row[] = $info->getPassword();
                $row[] = $info->isConsentObtained() ? '✓' : '✗';
                $row[] = $info->isAudioConsented() ? '✓' : '✗';
                $row[] = $info->isVideoConsented() ? '✓' : '✗';
            }

            $rows[] = $row;
        }

        return $rows;
    }

    public function getCsvResponse(LearningManagementSystem $lms, StudyGroup $studyGroup): Response {
        return $this->csvHelper->getCsvResponse(
            sprintf('%s_%s.csv', $lms->getName(), $studyGroup->getName()),
            $this->getRows($lms, $studyGroup)
        );
    }
}