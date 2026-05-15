<?php

namespace App\Common\Export;

use App\Framework\Csv\CsvHelper;
use App\Common\Entity\StudyGroup;
use App\Common\Entity\StudyGroupMembership;
use App\Common\Entity\Tuition;
use App\Common\Repository\StudyGroupRepositoryInterface;
use App\Common\Repository\TuitionRepositoryInterface;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\StudentGroupMembershipStrategy;
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
            $this->translator->trans('label.email'),
            $this->translator->trans('label.birthday'),
            $this->translator->trans('label.gender')
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
                $grade?->getName(),
                $membership->getStudent()->getEmail(),
                $membership->getStudent()->getBirthday()?->format('Y-m-d'),
                $membership->getStudent()->getGender()->value
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