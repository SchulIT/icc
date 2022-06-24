<?php

namespace App\Import;

use App\Entity\Grade;
use App\Entity\Room;
use App\Entity\Section;
use App\Entity\StudyGroup;
use App\Entity\Substitution;
use App\Entity\Teacher;
use App\Event\SubstitutionImportEvent;
use App\Repository\GradeRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\SubstitutionData;
use App\Request\Data\SubstitutionsData;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;
use DateTime;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class SubstitutionsImportStrategy implements ImportStrategyInterface, PostActionStrategyInterface {

    use ContextAwareTrait;

    private $substitutionRepository;
    private $teacherRepository;
    private $studyGroupRepository;
    private $tuitionRepository;
    private $roomRepository;
    private $gradeRepository;
    private $sectionRepository;
    private $dispatcher;

    public function __construct(SubstitutionRepositoryInterface $substitutionRepository, TeacherRepositoryInterface $teacherRepository,
                                StudyGroupRepositoryInterface $studyGroupRepository, TuitionRepositoryInterface $tuitionRepository, RoomRepositoryInterface $roomRepository,
                                GradeRepositoryInterface $gradeRepository, SectionRepositoryInterface $sectionRepository, EventDispatcherInterface $eventDispatcher) {
        $this->substitutionRepository = $substitutionRepository;
        $this->teacherRepository = $teacherRepository;
        $this->studyGroupRepository = $studyGroupRepository;
        $this->tuitionRepository = $tuitionRepository;
        $this->roomRepository = $roomRepository;
        $this->gradeRepository = $gradeRepository;
        $this->sectionRepository = $sectionRepository;
        $this->dispatcher = $eventDispatcher;
    }

    /**
     * @param SubstitutionsData $requestData
     * @return array<string, Substitution>
     * @throws ImportException
     */
    public function getExistingEntities($requestData): array {
        $dateTime = $this->getContext($requestData);

        if ($dateTime !== null) {
            $substitutions = $this->substitutionRepository->findAllByDate($dateTime);
        } else {
            $substitutions = $this->substitutionRepository->findAll();
        }

        return ArrayUtils::createArrayWithKeys(
            $substitutions,
            function (Substitution $substitution) {
                return $substitution->getExternalId();
            }
        );
    }

    /**
     * @param SubstitutionData $data
     * @param SubstitutionsData $requestData
     * @return Substitution
     * @throws ImportException
     */
    public function createNewEntity($data, $requestData) {
        $substitution = (new Substitution())
            ->setExternalId($data->getId());
        $this->updateEntity($substitution, $data, $requestData);

        return $substitution;
    }

    /**
     * @param SubstitutionData $object
     * @param array<string, Substitution> $existingEntities
     * @return Substitution|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Substitution $entity
     * @return int
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Substitution $entity
     * @param SubstitutionData $data
     * @param SubstitutionsData $requestData
     * @throws ImportException
     * @throws SectionNotResolvableException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $teacherIdSelector = function(Teacher $teacher) {
            return $teacher->getId();
        };

        $teachers = $this->teacherRepository->findAllByExternalId($data->getTeachers());

        if(count($teachers) !== count($data->getTeachers())) {
            $this->throwMissingTeacher($data->getTeachers(), $teachers, $data->getId());
        }

        CollectionUtils::synchronize($entity->getTeachers(), $teachers, $teacherIdSelector);

        $replacementTeachers = $this->teacherRepository->findAllByExternalId($data->getReplacementTeachers());

        if(count($replacementTeachers) !== count($data->getReplacementTeachers())) {
            $this->throwMissingTeacher($data->getReplacementTeachers(), $replacementTeachers, $data->getId());
        }

        $section = $this->sectionRepository->findOneByDate($data->getDate());

        if($section === null) {
            throw new SectionNotResolvableException($data->getDate());
        }

        CollectionUtils::synchronize($entity->getReplacementTeachers(), $replacementTeachers, $teacherIdSelector);

        $entity->setDate($data->getDate());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());
        $entity->setSubject($data->getSubject());
        $entity->setReplacementSubject($data->getReplacementSubject());
        $entity->setRemark($data->getText());
        $entity->setType($data->getType());
        $entity->setStartsBefore($data->startsBefore());

        $rooms = $this->roomRepository->findAllByExternalIds($data->getRooms());
        CollectionUtils::synchronize(
            $entity->getRooms(),
            $rooms,
            function(Room $room) {
                return $room->getId();
            }
        );

        if(count($rooms) > 0 || count($data->getRooms()) === 0) {
            $entity->setRoomName(null);
        } else {
            $entity->setRoomName(implode(', ', $data->getRooms()));
        }

        $replacementRooms = $this->roomRepository->findAllByExternalIds($data->getReplacementRooms());
        CollectionUtils::synchronize(
            $entity->getReplacementRooms(),
            $replacementRooms,
            function(Room $room) {
                return $room->getId();
            }
        );

        if(count($replacementRooms) > 0 || count($data->getReplacementRooms()) === 0) {
            $entity->setReplacementRoomName(null);
        } else {
            $entity->setReplacementRoomName(implode(', ', $data->getReplacementRooms()));
        }

        $studyGroups = $this->resolveStudyGroup($section, $data->getSubject(), $data->getGrades(), $data->getTeachers(), $data->getId());

        CollectionUtils::synchronize(
            $entity->getStudyGroups(),
            $studyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );

        if($data->getSubject() === $data->getReplacementSubject()) {
            $replacementStudyGroups = $studyGroups;
        } else {
            $replacementStudyGroups = $this->resolveStudyGroup($section, $data->getReplacementSubject(), $data->getReplacementGrades(), $data->getReplacementTeachers(), $data->getId());
        }
        
        CollectionUtils::synchronize(
            $entity->getReplacementStudyGroups(),
            $replacementStudyGroups,
            function (StudyGroup $studyGroup) {
                return $studyGroup->getId();
            }
        );

        $replacementGrades = $this->gradeRepository->findAllByExternalId($data->getReplacementGrades());

        if(count($replacementGrades) != count($data->getReplacementGrades())) {
            $this->throwMissingGrade($data->getReplacementGrades(), $replacementGrades, $data->getId());
        }

        $replacementStudyGroupGrades = [ ];
        /** @var StudyGroup $studyGroup */
        foreach($entity->getReplacementStudyGroups() as $studyGroup) {
            foreach($studyGroup->getGrades() as $grade) {
                if(!in_array($grade, $replacementStudyGroupGrades)) {
                    $replacementStudyGroupGrades[] = $grade;
                }
            }
        }

        if(count($replacementGrades) !== count($replacementStudyGroupGrades)) {
            CollectionUtils::synchronize(
                $entity->getReplacementGrades(),
                $replacementGrades,
                function(Grade $grade) {
                    return $grade->getId();
                }
            );
        } else {
            $entity->getReplacementGrades()->clear();
        }
    }

    /**
     * @param string|null $subject
     * @param string[] $grades
     * @param string[] $teachers
     * @param string $id
     * @return StudyGroup[]
     * @throws ImportException
     */
    private function resolveStudyGroup(Section $section, ?string $subject, array $grades, array $teachers, string $id): array {
        $result = [ ];

        if(empty($subject)) {
            foreach($grades as $grade) {
                $studyGroup = $this->studyGroupRepository->findOneByGradeName($grade, $section);

                if($studyGroup === null) {
                    throw new ImportException(sprintf('Study group for grade "%s" on substitution ID "%s" was not found.', $grade, $id));
                }

                $result[] = $studyGroup;
            }
        } else if(count($grades) > 0 && count($teachers) > 0) {
            $tuitions = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($grades, $teachers, $subject, $section);

            if(count($tuitions) === 1 && $tuitions[0]->getStudyGroup() !== null) {
                $result[] = $tuitions[0]->getStudyGroup();
            } else {
                return [ ];
            }
        }

        return $result;
    }

    /**
     * @param Substitution $entity
     */
    public function persist($entity): void {
        $this->substitutionRepository->persist($entity);
    }

    /**
     * @param Substitution $entity
     */
    public function remove($entity, $requestData): bool {
        $this->substitutionRepository->remove($entity);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->substitutionRepository;
    }

    /**
     * @param string[] $teachers
     * @param Teacher[] $foundTeachers
     * @param string $substitutionId
     * @throws ImportException
     */
    private function throwMissingTeacher(array $teachers, array $foundTeachers, string $substitutionId) {
        $foundTeacherExternalIds = array_map(function(Teacher $teacher) {
            return $teacher->getExternalId();
        }, $foundTeachers);

        foreach($teachers as $teacher) {
            if(!in_array($teacher, $foundTeacherExternalIds)) {
                throw new ImportException(sprintf('Teacher "%s" on substitution ID "%s" was not found.', $teacher, $substitutionId));
            }
        }
    }

    /**
     * @param string[]$grades
     * @param Grade[] $foundGrades
     * @param string $substitutionId
     * @throws ImportException
     */
    private function throwMissingGrade(array $grades, array $foundGrades, string $substitutionId) {
        $foundGradeExternalIds = array_map(function(Grade $grade) {
            return $grade->getExternalId();
        }, $foundGrades);

        foreach($grades as $grade) {
            if(!in_array($grade, $foundGradeExternalIds)) {
                throw new ImportException(sprintf('Grade "%s" on substitution ID "%s" was not found.', $grade, $substitutionId));
            }
        }
    }

    /**
     * @param SubstitutionsData $data
     * @return SubstitutionData[]
     */
    public function getData($data): array {
        return $data->getSubstitutions();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Substitution::class;
    }

    public function onFinished(ImportResult $result) {
        /** @var SubstitutionsData $request */
        $request = $result->getRequest();

        if($request->isSuppressNotifications() === false) {
            $this->dispatcher->dispatch(new SubstitutionImportEvent($result->getAdded(), $result->getUpdated(), $result->getRemoved()));
        }
    }
}