<?php

namespace App\Controller;

use App\Import\AppointmentCategoriesImportStrategy;
use App\Import\AppointmentsImportStrategy;
use App\Import\ExamsImportStrategy;
use App\Import\GradesImportStrategy;
use App\Import\GradeTeachersImportStrategy;
use App\Import\Importer;
use App\Import\ImportResult;
use App\Import\StudentsImportStrategy;
use App\Import\StudyGroupImportStrategy;
use App\Import\StudyGroupMembershipImportStrategy;
use App\Import\SubjectsImportStrategy;
use App\Import\SubstitutionsImportStrategy;
use App\Import\TeachersImportStrategy;
use App\Import\TimetableLessonsImportStrategy;
use App\Import\TimetablePeriodsImportStrategy;
use App\Import\TimetableSupervisionsImportStrategy;
use App\Import\TuitionsImportStrategy;
use App\Request\Data\AppointmentCategoriesData;
use App\Request\Data\AppointmentsData;
use App\Request\Data\ExamsData;
use App\Request\Data\GradesData;
use App\Request\Data\GradeTeachersData;
use App\Request\Data\StudentsData;
use App\Request\Data\StudyGroupMembershipsData;
use App\Request\Data\StudyGroupsData;
use App\Request\Data\SubjectsData;
use App\Request\Data\SubstitutionsData;
use App\Request\Data\TeachersData;
use App\Request\Data\TimetableLessonsData;
use App\Request\Data\TimetablePeriodsData;
use App\Request\Data\TimetableSupervisionsData;
use App\Request\Data\TuitionsData;
use App\Response\ErrorResponse;
use App\Response\ImportResponse;
use App\Utils\RefererHelper;
use Exception;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/import")
 * @Security("is_granted('ROLE_IMPORT')")
 */
class ImportController extends AbstractController {

    private $importer;
    private $serializer;

    public function __construct(Importer $importer, SerializerInterface $serializer, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->importer = $importer;
        $this->serializer = $serializer;
    }

    private function fromException(Exception $exception): Response {
        $response = new ErrorResponse($exception->getMessage());
        $json = $this->serializer->serialize($response, 'json');

        return new Response(
            $json,
            $exception->getCode(),
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    private function fromResult(ImportResult $importResult): Response {
        $response = new ImportResponse($importResult->getAdded(), $importResult->getUpdated(), $importResult->getRemoved());
        $json = $this->serializer->serialize($response, 'json');

        return new Response(
            $json,
            200,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Imports appointments. Note: you first must create appointment categories from the web interface.
     *
     * @Route("/appointments", methods={"POST"}, name="import_appointments")
     * @SWG\Post(operationId="import_appointments")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=AppointmentsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function appointments(AppointmentsData $appointmentsData, AppointmentsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($appointmentsData->getAppointments(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports appointment categories.
     *
     * @Route("/appointments/categories", methods={"POST"})
     * @SWG\Post(operationId="import_appointment_categories")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=AppointmentCategoriesData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function appointmentCategories(AppointmentCategoriesData $appointmentCategoriesData, AppointmentCategoriesImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($appointmentCategoriesData->getAppointments(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports exams.
     *
     * @Route("/exams", methods={"POST"})
     * @SWG\Post(operationId="import_exams")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=ExamsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function exams(ExamsData $examsData, ExamsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($examsData->getExams(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports grades.
     *
     * @Route("/grades", methods={"POST"})
     * @SWG\Post(operationId="import_grades")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=GradesData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function grades(GradesData $gradesData, GradesImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($gradesData->getGrades(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports grade teachers.
     *
     * @Route("/grades/teachers", methods={"POST"})
     * @SWG\Post(operationId="import_grade_teachers")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=GradeTeachersData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function gradeTeachers(GradeTeachersData $gradeTeachersData, GradeTeachersImportStrategy $strategy): Response {
        try {
            $result = $this->importer->importRelations($gradeTeachersData->getGradeTeachers(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports students.
     *
     * @Route("/students", methods={"POST"})
     * @SWG\Post(operationId="import_students")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=StudentsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function students(StudentsData $studentsData, StudentsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($studentsData->getStudents(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports study groups.
     *
     * @Route("/studygroups", methods={"POST"})
     * @SWG\Post(operationId="import_studygroups")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=StudyGroupsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function studyGroups(StudyGroupsData $studyGroupsData, StudyGroupImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($studyGroupsData->getStudyGroups(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports study group memberships.
     *
     * @Route("/studygroups/memberships", methods={"POST"})
     * @SWG\Post(operationId="import_studygroups_memberships")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=StudyGroupMembershipsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function studyGroupsMemberships(StudyGroupMembershipsData $membershipsData, StudyGroupMembershipImportStrategy $strategy): Response {
        try {
            $result = $this->importer->importRelations($membershipsData->getMemberships(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports subjects.
     *
     * @Route("/subjects", methods={"POST"})
     * @SWG\Post(operationId="import_subjects")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=SubjectsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function subjects(SubjectsData $subjectsData, SubjectsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($subjectsData->getSubjects(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports study groups.
     *
     * @Route("/substitutions", methods={"POST"})
     * @SWG\Post(operationId="import_substitutions")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=SubstitutionsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function substitutions(SubstitutionsData $substitutionsData, SubstitutionsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($substitutionsData->getSubstitutions(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports teachers.
     *
     * @Route("/teachers", methods={"POST"})
     * @SWG\Post(operationId="import_teachers")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TeachersData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function teachers(TeachersData $teachersData, TeachersImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($teachersData->getTeachers(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports timetable lessons. Note: you must import periods first.
     *
     * @Route("/timetable/lessons", methods={"POST"})
     * @SWG\Post(operationId="import_timetable_lessons")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TimetableLessonsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function timetableLessons(TimetableLessonsData $lessonsData, TimetableLessonsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($lessonsData->getLessons(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports timetable periods.
     *
     * @Route("/timetable/periods", methods={"POST"})
     * @SWG\Post(operationId="import_timetable_periods")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TimetablePeriodsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function timetablePeriods(TimetablePeriodsData $periodsData, TimetablePeriodsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($periodsData->getPeriods(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }

    /**
     * Imports timetable supervisions. Note: you must import periods first.
     *
     * @Route("/timetable/supervisions", methods={"POST"})
     * @SWG\Post(operationId="import_timetable_supervisions")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TimetableSupervisionsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function timetableSupervisions(TimetableSupervisionsData $supervisionsData, TimetableSupervisionsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($supervisionsData->getSupervisions(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {

        }
    }

    /**
     * Imports tuitions.
     *
     * @Route("/tuitions", methods={"POST"})
     * @SWG\Post(operationId="import_tuitions")
     * @SWG\Parameter(
     *     name="payload",
     *     in="body",
     *     @Model(type=TuitionsData::class)
     * )
     * @SWG\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     */
    public function tuitions(TuitionsData $tuitionsData, TuitionsImportStrategy $strategy): Response {
        try {
            $result = $this->importer->import($tuitionsData->getTuitions(), $strategy);
            return $this->fromResult($result);
        } catch (Exception $e) {
            return $this->fromException($e);
        }
    }
}