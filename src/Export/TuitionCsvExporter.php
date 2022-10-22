<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentGroupMembershipStrategy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TuitionCsvExporter {

    public function __construct(private TuitionRepositoryInterface $tuitionRepository, private CsvHelper $csvHelper, private TranslatorInterface $translator, private Sorter $sorter)
    {
    }

    /**
     * @return string[][]
     */
    public function getRows(Tuition $tuition): array {
        /*
         * Re-query the tuition because the SQL query is optimized for many left-joins
         * (we have to navigate Tuition -> StudyGroup -> StudyGroupMembership -> Student)
         */
        $tuition = $this->tuitionRepository->findOneById($tuition->getId());

        $rows = [ ];

        // Header
        $rows[] = [
            $this->translator->trans('label.lastname'),
            $this->translator->trans('label.firstname'),
            $this->translator->trans('label.grade'),
            $this->translator->trans('label.email'),
            $this->translator->trans('label.type')
        ];

        $memberships = $tuition->getStudyGroup()->getMemberships()->toArray();
        $this->sorter->sort($memberships, StudentGroupMembershipStrategy::class);

        // Rows
        /** @var StudyGroupMembership $membership */
        foreach($memberships as $membership) {
            $grade = $membership->getStudent()->getGrade($tuition->getSection());

            $rows[] = [
                $membership->getStudent()->getLastname(),
                $membership->getStudent()->getFirstname(),
                $grade !== null ? $grade->getName() : null,
                $membership->getStudent()->getEmail(),
                $membership->getType()
            ];
        }

        return $rows;
    }

    public function getCsvResponse(Tuition $tuition): Response {
        return $this->csvHelper->getCsvResponse(
            sprintf('%s.csv', $tuition->getName()),
            $this->getRows($tuition)
        );
    }
}