<?php

namespace App\Import;

use App\Entity\Absence;
use App\Repository\AbsenceRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Request\Data\AbsenceData;
use App\Request\Data\AbsencesData;

class AbsencesImportStrategy implements ReplaceImportStrategyInterface {

    private $repository;
    private $teacherRepository;
    private $studyGroupRepository;

    public function __construct(AbsenceRepositoryInterface $repository, TeacherRepositoryInterface $teacherRepository, StudyGroupRepositoryInterface $studyGroupRepository) {
        $this->repository = $repository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
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
            }
        } else if($data->getType() === 'study_group') {
            $studyGroup = $this->studyGroupRepository->findOneByExternalId($data->getObjective());

            if($studyGroup !== null) {
                $absence->setStudyGroup($studyGroup);
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
}