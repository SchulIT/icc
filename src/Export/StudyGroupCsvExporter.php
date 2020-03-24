<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class StudyGroupCsvExporter {

    private $studyGroupRepository;
    private $csvHelper;
    private $translator;

    public function __construct(StudyGroupRepositoryInterface $studyGroupRepository, CsvHelper $csvHelper, TranslatorInterface $translator) {
        $this->studyGroupRepository = $studyGroupRepository;
        $this->csvHelper = $csvHelper;
        $this->translator = $translator;
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

        // Rows
        /** @var StudyGroupMembership $membership */
        foreach($studyGroup->getMemberships() as $membership) {
            $rows[] = [
                $membership->getStudent()->getLastname(),
                $membership->getStudent()->getFirstname(),
                $membership->getStudent()->getGrade()->getName(),
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