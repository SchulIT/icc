<?php

namespace App\Export;

use App\Csv\CsvHelper;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Repository\TuitionRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class TuitionCsvExporter {

    private $tuitionRepository;
    private $csvHelper;
    private $translator;

    public function __construct(TuitionRepositoryInterface $tuitionRepository, CsvHelper $csvHelper, TranslatorInterface $translator) {
        $this->tuitionRepository = $tuitionRepository;
        $this->csvHelper = $csvHelper;
        $this->translator = $translator;
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

        // Rows
        /** @var StudyGroupMembership $membership */
        foreach($tuition->getStudyGroup()->getMemberships() as $membership) {
            $rows[] = [
                $membership->getStudent()->getLastname(),
                $membership->getStudent()->getFirstname(),
                $membership->getStudent()->getGrade()->getName(),
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