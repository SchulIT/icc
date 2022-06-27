<?php

namespace App\Controller;

use App\Import\AbsencesImportStrategy;
use App\Import\AppointmentCategoriesImportStrategy;
use App\Import\AppointmentsImportStrategy;
use App\Import\ExamsImportStrategy;
use App\Import\FreeTimespanImportStrategy;
use App\Import\GradeMembershipImportStrategy;
use App\Import\GradesImportStrategy;
use App\Import\GradeTeachersImportStrategy;
use App\Import\Importer;
use App\Import\ImportException;
use App\Import\ImportResult;
use App\Import\InfotextsImportStrategy;
use App\Import\PrivacyCategoryImportStrategy;
use App\Import\RoomImportStrategy;
use App\Import\StudentsImportStrategy;
use App\Import\StudyGroupImportStrategy;
use App\Import\StudyGroupMembershipImportStrategy;
use App\Import\SubjectsImportStrategy;
use App\Import\SubstitutionsImportStrategy;
use App\Import\TeachersImportStrategy;
use App\Import\TimetableLessonsImportStrategy;
use App\Import\TimetableSupervisionsImportStrategy;
use App\Import\TuitionsImportStrategy;
use App\Request\Data\AbsencesData;
use App\Request\Data\AppointmentCategoriesData;
use App\Request\Data\AppointmentsData;
use App\Request\Data\ExamsData;
use App\Request\Data\FreeLessonTimespansData;
use App\Request\Data\GradeMembershipsData;
use App\Request\Data\GradesData;
use App\Request\Data\GradeTeachersData;
use App\Request\Data\InfotextsData;
use App\Request\Data\PrivacyCategoriesData;
use App\Request\Data\RoomsData;
use App\Request\Data\StudentsData;
use App\Request\Data\StudyGroupMembershipsData;
use App\Request\Data\StudyGroupsData;
use App\Request\Data\SubjectsData;
use App\Request\Data\SubstitutionsData;
use App\Request\Data\TeachersData;
use App\Request\Data\TimetableLessonsData;
use App\Request\Data\TimetableSupervisionsData;
use App\Request\Data\TuitionsData;
use App\Response\ErrorResponse;
use App\Response\ImportResponse;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/import")
 * @Security("is_granted('ROLE_IMPORT')")
 */
class ImportController extends AbstractController {

    private Importer $importer;
    private SerializerInterface $serializer;

    public function __construct(Importer $importer, SerializerInterface $serializer, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->importer = $importer;
        $this->serializer = $serializer;
    }

    private function fromResult(ImportResult $importResult): Response {
        $response = new ImportResponse($importResult->getAdded(), $importResult->getUpdated(), $importResult->getRemoved(), $importResult->getIgnored());
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
     * @OA\Post(operationId="import_appointments")
     * @OA\RequestBody(
     *     @Model(type=AppointmentsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function appointments(AppointmentsData $appointmentsData, AppointmentsImportStrategy $strategy): Response {
        $result = $this->importer->import($appointmentsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports appointment categories.
     *
     * @Route("/appointments/categories", methods={"POST"})
     * @OA\Post(operationId="import_appointment_categories")
     * @OA\RequestBody(
     *     @Model(type=AppointmentCategoriesData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function appointmentCategories(AppointmentCategoriesData $appointmentCategoriesData, AppointmentCategoriesImportStrategy $strategy): Response {
        $result = $this->importer->import($appointmentCategoriesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports exams.
     *
     * @Route("/exams", methods={"POST"})
     * @OA\Post(operationId="import_exams")
     * @OA\RequestBody(
     *     @Model(type=ExamsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function exams(ExamsData $examsData, ExamsImportStrategy $strategy): Response {
        $result = $this->importer->import($examsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports grades.
     *
     * @Route("/grades", methods={"POST"})
     * @OA\Post(operationId="import_grades")
     * @OA\RequestBody(
     *     @Model(type=GradesData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function grades(GradesData $gradesData, GradesImportStrategy $strategy): Response {
        $result = $this->importer->import($gradesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports grade teachers.
     *
     * @Route("/grades/teachers", methods={"POST"})
     * @OA\Post(operationId="import_grade_teachers")
     * @OA\RequestBody(
     *     @Model(type=GradeTeachersData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function gradeTeachers(GradeTeachersData $gradeTeachersData, GradeTeachersImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($gradeTeachersData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports grade memberships.
     *
     * @Route("/grades/memberships", methods={"POST"})
     * @OA\Post(operationId="import_grade_memberships")
     * @OA\RequestBody(
     *     @Model(type=GradeMembershipsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function gradeMemberships(GradeMembershipsData $membershipsData, GradeMembershipImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($membershipsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports students.
     *
     * @Route("/students", methods={"POST"})
     * @OA\Post(operationId="import_students")
     * @OA\RequestBody(
     *     @Model(type=StudentsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function students(StudentsData $studentsData, StudentsImportStrategy $strategy): Response {
        $result = $this->importer->import($studentsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports study groups.
     *
     * @Route("/studygroups", methods={"POST"})
     * @OA\Post(operationId="import_studygroups")
     * @OA\RequestBody(
     *     @Model(type=StudyGroupsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function studyGroups(StudyGroupsData $studyGroupsData, StudyGroupImportStrategy $strategy): Response {
        $result = $this->importer->import($studyGroupsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports study group memberships.
     *
     * @Route("/studygroups/memberships", methods={"POST"})
     * @OA\Post(operationId="import_studygroups_memberships")
     * @OA\RequestBody(
     *     @Model(type=StudyGroupMembershipsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function studyGroupsMemberships(StudyGroupMembershipsData $membershipsData, StudyGroupMembershipImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($membershipsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports subjects.
     *
     * @Route("/subjects", methods={"POST"})
     * @OA\Post(operationId="import_subjects")
     * @OA\RequestBody(
     *     @Model(type=SubjectsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function subjects(SubjectsData $subjectsData, SubjectsImportStrategy $strategy): Response {
        $result = $this->importer->import($subjectsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports study groups.
     *
     * @Route("/substitutions", methods={"POST"})
     * @OA\Post(operationId="import_substitutions")
     * @OA\RequestBody(
     *     @Model(type=SubstitutionsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function substitutions(SubstitutionsData $substitutionsData, SubstitutionsImportStrategy $strategy): Response {
        $result = $this->importer->import($substitutionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports teachers.
     *
     * @Route("/teachers", methods={"POST"})
     * @OA\Post(operationId="import_teachers")
     * @OA\RequestBody(
     *     @Model(type=TeachersData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function teachers(TeachersData $teachersData, TeachersImportStrategy $strategy): Response {
        $result = $this->importer->import($teachersData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports timetable lessons. Note: you must import periods first.
     *
     * @Route("/timetable/lessons", methods={"POST"})
     * @OA\Post(operationId="import_timetable_lessons")
     * @OA\RequestBody(
     *     @Model(type=TimetableLessonsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function timetableLessons(TimetableLessonsData $lessonsData, TimetableLessonsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($lessonsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports timetable supervisions. Note: you must import periods first.
     *
     * @Route("/timetable/supervisions", methods={"POST"})
     * @OA\Post(operationId="import_timetable_supervisions")
     * @OA\RequestBody(
     *     @Model(type=TimetableSupervisionsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function timetableSupervisions(TimetableSupervisionsData $supervisionsData, TimetableSupervisionsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($supervisionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports tuitions.
     *
     * @Route("/tuitions", methods={"POST"})
     * @OA\Post(operationId="import_tuitions")
     * @OA\RequestBody(
     *     @Model(type=TuitionsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function tuitions(TuitionsData $tuitionsData, TuitionsImportStrategy $strategy): Response {
        $result = $this->importer->import($tuitionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports infotexts.
     *
     * @Route("/infotexts", methods={"POST"})
     * @OA\Post(operationId="import_infotexts")
     * @OA\RequestBody(
     *     @Model(type=InfotextsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function infotexts(InfotextsData $infotextsData, InfotextsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($infotextsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports absences.
     *
     * @Route("/absences", methods={"POST"})
     * @OA\Post(operationId="import_absences")
     * @OA\RequestBody(
     *     @Model(type=AbsencesData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function absences(AbsencesData $absencesData, AbsencesImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($absencesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports privacy categories.
     *
     * @Route("/privacy/categories", methods={"POST"})
     * @OA\Post(operationId="import_privacy_categories")
     * @OA\RequestBody(
     *     @Model(type=PrivacyCategoriesData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function privacyCategories(PrivacyCategoriesData $categoriesData, PrivacyCategoryImportStrategy $strategy): Response {
        $result = $this->importer->import($categoriesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports rooms.
     *
     * @Route("/rooms", methods={"POST"})
     * @OA\Post(operationId="import_rooms")
     * @OA\RequestBody(
     *     @Model(type=RoomsData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function rooms(RoomsData $roomsData, RoomImportStrategy $strategy): Response {
        $result = $this->importer->import($roomsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports rooms.
     *
     * @Route("/free_lessons", methods={"POST"})
     * @OA\Post(operationId="import_lessons")
     * @OA\RequestBody(
     *     @Model(type=FreeLessonTimespansData::class)
     * )
     * @OA\Response(
     *     response=200,
     *     description="Import was successful",
     *     @Model(type=ImportResponse::class)
     * )
     * @OA\Response(
     *     response=400,
     *     description="Import was not successful",
     *     @Model(type=ErrorResponse::class)
     * )
     * @throws ImportException
     */
    public function freeLessons(FreeLessonTimespansData $timespansData, FreeTimespanImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($timespansData, $strategy);
        return $this->fromResult($result);
    }
}