<?php

namespace App\Timetable\Import;

use App\Framework\Import\ReplaceImportStrategyInterface;
use App\Timetable\Entity\TimetableSupervision;
use App\Common\Repository\TeacherRepositoryInterface;
use App\Timetable\Repository\TimetableSupervisionRepositoryInterface;
use App\Framework\Repository\TransactionalRepositoryInterface;
use App\Timetable\Import\Json\TimetableSupervisionData;
use App\Timetable\Import\Json\TimetableSupervisionsData;
use Exception;
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

        $teacher = $this->teacherRepository->findOneByAcronym($data->getTeacher());

        if($teacher === null) {
            throw new Exception(sprintf('Lehrkraft %s nicht gefunden', $data->getTeacher()));
        }

        $supervision = (new TimetableSupervision())
            ->setExternalId($data->getId() ?? Uuid::uuid4()->toString())
            ->setDate($data->getDate())
            ->setIsBefore($data->isBefore())
            ->setLesson($data->getLesson())
            ->setLocation($data->getLocation())
            ->setTeacher($teacher);

        $this->supervisionRepository->persist($supervision);
    }
}