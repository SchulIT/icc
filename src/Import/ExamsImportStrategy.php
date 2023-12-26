<?php

namespace App\Import;

use App\Entity\Exam;
use App\Entity\ExamStudent;
use App\Entity\ExamSupervision;
use App\Entity\Section;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Event\ExamImportEvent;
use App\Exam\ExamStudentsResolver;
use App\Repository\ExamRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\StudentRepositoryInterface;
use App\Repository\TeacherRepositoryInterface;
use App\Repository\TransactionalRepositoryInterface;
use App\Repository\TuitionRepositoryInterface;
use App\Request\Data\ExamData;
use App\Request\Data\ExamsData;
use App\Section\SectionResolverInterface;
use App\Settings\GeneralSettings;
use App\Settings\ImportSettings;
use App\Utils\ArrayUtils;
use App\Utils\CollectionUtils;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class ExamsImportStrategy implements ImportStrategyInterface, PostActionStrategyInterface {

    public function __construct(private readonly ExamRepositoryInterface $examRepository, private readonly TuitionRepositoryInterface $tuitionRepository, private readonly StudentRepositoryInterface $studentRepository, private readonly TeacherRepositoryInterface $teacherRepository, private readonly EventDispatcherInterface $dispatcher, private readonly RoomRepositoryInterface $roomRepository, private readonly SectionResolverInterface $sectionResolver, private readonly ExamStudentsResolver $examStudentsResolver)
    {
    }

    /**
     * @param ExamsData $requestData
     * @return Exam[]
     */
    public function getExistingEntities($requestData): array {
        return ArrayUtils::createArrayWithKeys(
            $this->examRepository->findAllExternalWithRange($requestData->getStartDate(), $requestData->getEndDate()),
            fn(Exam $exam) => $exam->getExternalId()
        );
    }

    /**
     * @param ExamData $data
     * @param ExamsData $requestData
     * @return Exam
     */
    public function createNewEntity($data, $requestData) {
        $exam = (new Exam())
            ->setExternalId($data->getId());
        $this->updateEntity($exam, $data, $requestData);

        return $exam;
    }

    /**
     * @param ExamData $object
     * @param Exam[] $existingEntities
     * @return Exam|null
     */
    public function getExistingEntity($object, array $existingEntities) {
        return $existingEntities[$object->getId()] ?? null;
    }

    /**
     * @param Exam $entity
     */
    public function getEntityId($entity): int {
        return $entity->getId();
    }

    /**
     * @param Exam $entity
     * @param ExamsData $requestData
     * @param ExamData $data
     * @throws ImportException
     * @throws SectionNotResolvableException
     */
    public function updateEntity($entity, $data, $requestData): void {
        $entity->setDate($data->getDate());
        $entity->setDescription($data->getDescription());
        $entity->setLessonStart($data->getLessonStart());
        $entity->setLessonEnd($data->getLessonEnd());

        $section = $this->sectionResolver->getSectionForDate($entity->getDate());

        if($section === null) {
            throw new SectionNotResolvableException($entity->getDate());
        }

        /*
         * for compatibility reasons only!
         */
        $rooms = $data->getRooms();
        $room = array_shift($rooms);

        if(!empty($room)) {
            $roomEntity = $this->roomRepository->findOneByExternalId($room);

            if($roomEntity === null) {
                throw new ImportException(sprintf('Room "%s" on substitution ID "%s" was not found.', $room, $data->getId()));
            }

            $entity->setRoom($roomEntity);
        } else {
            $entity->setRoom(null);
        }

        // Remove all supervisions which are outside the exam lesson bounds
        $remove = $entity->getSupervisions()->filter(fn(ExamSupervision $supervision) => $supervision->getLesson() < $entity->getLessonStart() || $supervision->getLesson() > $entity->getLessonEnd());

        foreach($remove as $item) {
            $entity->removeSupervision($item);
        }

        $supervisions = $data->getSupervisions();

        for($lesson = $data->getLessonStart(), $idx = 0; $lesson <= $data->getLessonEnd(); $lesson++, $idx++) {
            $supervision = $entity->getSupervisions()->filter(fn(ExamSupervision $supervision) => $supervision->getLesson() === $lesson)->first();

            if($supervision === false) {
                $supervision = (new ExamSupervision())
                    ->setExam($entity)
                    ->setLesson($lesson);
                $entity->addSupervision($supervision);
            }

            if(!isset($supervisions[$idx])) {
                $entity->removeSupervision($supervision);
                continue;
            }

            $teacher = $this->teacherRepository->findOneByExternalId($supervisions[$idx]);

            if($teacher !== null) {
                $supervision->setTeacher($teacher);
            } else {
                $entity->removeSupervision($supervision);
            }
        }

        $students = [ ];
        $tuitions = [ ];

        foreach($data->getTuitions() as $tuitionData) {
            $resolvedTuitions = $this->tuitionRepository->findAllByGradeAndSubjectOrCourseWithoutTeacher($tuitionData->getGrades(), $tuitionData->getSubjectOrCourse(), $section);

            if(count($resolvedTuitions) > 1) {
                $resolvedTuitions = $this->tuitionRepository->findAllByGradeTeacherAndSubjectOrCourse($tuitionData->getGrades(), $tuitionData->getTeachers(), $tuitionData->getSubjectOrCourse(), $section);
            }

            if(count($resolvedTuitions) === 0) {
                throw new ImportException(sprintf('Tuition for (%s; %s; %s) on exam ID "%s" was not found.', implode(',', $tuitionData->getGrades()), implode(',', $tuitionData->getTeachers()), $tuitionData->getSubjectOrCourse(), $data->getId()));
            } else if(count($resolvedTuitions) === 1) {
                $resolvedTuition = array_shift($resolvedTuitions);
                $tuitions[] = $resolvedTuition;

                if($data->isComputeStudentsFromRules() === false) {
                    $students = array_merge(
                        $students,
                        array_map(
                            fn(Student $student) => (new ExamStudent())->setExam($entity)->setStudent($student)->setTuition($resolvedTuition),
                            $this->studentRepository->findAllByExternalId($tuitionData->getStudents())
                        )
                    );
                }
            } else {
                throw new ImportException(sprintf('Tuition for (%s; %s; %s) on exam ID "%s" is ambigious.', implode(',', $tuitionData->getGrades()), implode(',', $tuitionData->getTeachers()), $tuitionData->getSubjectOrCourse(), $data->getId()));
            }
        }

        CollectionUtils::synchronize(
            $entity->getTuitions(),
            $tuitions,
            fn(Tuition $tuition) => $tuition->getId()
        );

        if($data->isComputeStudentsFromRules()) {
            $students = $this->examStudentsResolver->resolveExamStudentsByRules($entity);
        } else {
            // for compatibility reasons...
            $examStudents = array_map(
                fn(Student $student) => (new ExamStudent())->setExam($entity)->setStudent($student)->setTuition(null),
                $this->studentRepository->findAllByExternalId($data->getStudents())
            );

            $students = array_merge(
                $students,
                $this->examStudentsResolver->resolveExamStudentsFromGivenStudents($entity, $examStudents)
            );
            // end
        }

        $this->examStudentsResolver->setExamStudents($entity, $students);
    }

    /**
     * @param Exam $entity
     */
    public function persist($entity): void {
        $this->examRepository->persist($entity);
    }

    /**
     * @param Exam $entity
     */
    public function remove($entity, $requestData): bool {
        $this->examRepository->remove($entity);
        return true;
    }

    /**
     * @inheritDoc
     */
    public function getRepository(): TransactionalRepositoryInterface {
        return $this->examRepository;
    }

    /**
     * @param ExamsData $data
     * @return ExamData[]
     */
    public function getData($data): array {
        return $data->getExams();
    }

    /**
     * @inheritDoc
     */
    public function getEntityClassName(): string {
        return Exam::class;
    }

    public function onFinished(ImportResult $result) {
        /** @var ExamsData $request */
        $request = $result->getRequest();

        if($request->isSuppressNotifications() === false) {
            $this->dispatcher->dispatch(new ExamImportEvent($result->getAdded(), $result->getUpdated(), $result->getRemoved()));
        }
    }
}