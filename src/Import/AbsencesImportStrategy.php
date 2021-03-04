<?php

namespace App\Import;

use App\Entity\Absence;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AbsenceData;
use App\Request\Data\AbsencesData;

class AbsencesImportStrategy implements ReplaceImportStrategyInterface {

    private $repository;
    private $teacherRepository;
    private $studyGroupRepository;
    private $roomRepository;

    public function __construct(AbsenceRepositoryInterface $repository, TeacherRepositoryInterface $teacherRepository, StudyGroupRepositoryInterface $studyGroupRepository, RoomRepositoryInterface $roomRepository) {
        $this->repository = $repository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->roomRepository = $roomRepository;
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll(): void {
        $this->repository->removeAll();
    }

    /**
     * @param AbsenceData $data
     */
    public function persist($data): void {
        $absence = (new Absence())
            ->setDate($data->getDate())
            ->setLessonStart($data->getLessonStart())
            ->setLessonEnd($data->getLessonEnd());

        if($data->getType() === 'teacher') {
            $teacher = $this->teacherRepository->findOneByAcronym($data->getObjective());

            if($teacher !== null) {
                $absence->setTeacher($teacher);
            } else {
                return;
            }
        } else if($data->getType() === 'study_group') {
            $studyGroup = $this->studyGroupRepository->findOneByExternalId($data->getObjective());

            if($studyGroup !== null) {
                $absence->setStudyGroup($studyGroup);
            } else {
                return;
            }
        } else if($data->getType() === 'room') {
            $room = $this->roomRepository->findOneByExternalId($data->getObjective());

            if($room !== null) {
                $absence->setRoom($room);
            } else {
                return;
            }
        }

        $this->repository->persist($absence);
    }

    /**
     * @param AbsencesData $data
     * @return AbsenceData[]
     */
    public function getData($data): array {
        return $data->getAbsences();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Absence::class;
    }
}