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
use App\Section\SectionResolverInterface;

class AbsencesImportStrategy implements ReplaceImportStrategyInterface {

    use ContextAwareTrait;

    private $repository;
    private $teacherRepository;
    private $studyGroupRepository;
    private $roomRepository;
    private $sectionResolver;

    public function __construct(AbsenceRepositoryInterface $repository, TeacherRepositoryInterface $teacherRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository, RoomRepositoryInterface $roomRepository, SectionResolverInterface $sectionResolver) {
        $this->repository = $repository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->roomRepository = $roomRepository;
        $this->sectionResolver = $sectionResolver;
    }

    public function getRepository(): TransactionalRepositoryInterface {
        return $this->repository;
    }

    public function removeAll($requestData): void {
        $dateTime = $this->getContext($requestData);
        $this->repository->removeAll($dateTime);
    }

    /**
     * @param AbsenceData $data
     * @throws SectionNotResolvableException
     */
    public function persist($data, $requestData): void {
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
            $section = $this->sectionResolver->getSectionForDate($absence->getDate());

            if($section === null) {
                throw new SectionNotResolvableException($absence->getDate());
            }

            $studyGroup = $this->studyGroupRepository->findOneByExternalId($data->getObjective(), $section);

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