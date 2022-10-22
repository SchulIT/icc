<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentGroupMembershipStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupCsvExporter {

    public function __construct(private StudyGroupRepositoryInterface $studyGroupRepository, private CsvHelper $csvHelper, private TranslatorInterface $translator, private Sorter $sorter)
    {
    }

    /**
     * @return string[][]
     */
    public function getRows(StudyGroup $studyGroup): array {
        /*
         * Re-query the study group because the SQL query is optimized for many left-joins
         * (we have to navigate Tuition -> StudyGroup -> StudyGroupMembership -> Student)
         */
        $studyGroup = $this->studyGroupRepository->findOneById($studyGroup->getId());

        $rows = [ ];

        // Header
        $rows[] = [
            $this->translator->trans('label.lastname'),
            $this->translator->trans('label.firstname'),
            $this->translator->trans('label.grade'),
            $this->translator->trans('label.email')
        ];

        $memberships = $studyGroup->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        // Rows
        /** @var StudyGroupMembership $membership */
        foreach($memberships as $membership) {
            $grade = $membership->getStudent()->getGrade($studyGroup->getSection());

            $rows[] = [
                $membership->getStudent()->getLastname(),
                $membership->getStudent()->getFirstname(),
                $grade !== null ? $grade->getName() : null,
                $membership->getStudent()->getEmail()
            ];
        }

        return $rows;
    }

    public function getCsvResponse(StudyGroup $studyGroup): Response {
        return $this->csvHelper->getCsvResponse(
            sprintf('%s.csv', $studyGroup->getName()),
            $this->getRows($studyGroup)
        );
    }
}