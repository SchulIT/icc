<?php

namespace App\Import;

use App\Entity\TimetableSupervision;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\TimetableSupervisionData;
use App\Request\Data\TimetableSupervisionsData;
use Ramsey\Uuid\Uuid;

class TimetableSupervisionsImportStrategy implements ReplaceImportStrategyInterface {

    public function __construct(private TimetableSupervisionRepositoryInterface $supervisionRepository, private TeacherRepositoryInterface $teacherRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->supervisionRepository;
    }

    /**
     * @param TimetableSupervisionsData $data
     * @return TimetableSupervisionData[]
     */
    public function getData($data): array {
        return $data->getSupervisions();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return TimetableSupervision::class;
    }

    /**
     * @param TimetableSupervisionsData $data
     */
    public function removeAll($data): void {
        $this->supervisionRepository->removeBetween($data->getStartDate(), $data->getEndDate());
    }

    /**
     * @param TimetableSupervisionData $data
     * @param TimetableSupervisionsData $requestData
     */
    public function persist($data, $requestData): void {
        if($data->getDate() < $requestData->getStartDate()) {
            return;
        }

        $supervision = (new TimetableSupervision())
            ->setExternalId($data->getId() ?? Uuid::uuid4()->toString())
            ->setDate($data->getDate())
            ->setIsBefore($data->isBefore())
            ->setLesson($data->getLesson())
            ->setLocation($data->getLocation())
            ->setTeacher($this->teacherRepository->findOneByAcronym($data->getTeacher()));

        $this->supervisionRepository->persist($supervision);
    }
}