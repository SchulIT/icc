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
use App\Import\LearningManagementSystemsImportStrategy;
use App\Import\PrivacyCategoryImportStrategy;
use App\Import\RoomImportStrategy;
use App\Import\StudentLearningManagementSystemInformationImportStrategy;
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
use App\Request\Data\LearningManagementSystemsData;
use App\Request\Data\PrivacyCategoriesData;
use App\Request\Data\RoomsData;
use App\Request\Data\StudentLearningManagementSystemsData;
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
use OpenApi\Attributes as OA;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
#[Security("is_granted('ROLE_IMPORT')")]
class ImportController extends AbstractController {

    public function __construct(private readonly Importer $importer, private readonly SerializerInterface $serializer, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    private function fromResult(ImportResult $importResult): Response {
        $response = new ImportResponse($importResult->getAdded(), $importResult->getUpdated(), $importResult->getRemoved(), $importResult->getIgnored());
        $json = $this->serializer->serialize($response, 'json');

        return new Response(
            $json,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Importiert Termine. Wichtig: Zunächst müssen die Kategorien festgelegt werden (entweder durch Import oder Anlegen
     * über das Web Interface).
     *
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_appointments', tags: ['Terminplan'])]
    #[OA\RequestBody(content: new Model(type: AppointmentsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/appointments', name: 'import_appointments', methods: ['POST'])]
    public function appointments(AppointmentsData $appointmentsData, AppointmentsImportStrategy $strategy): Response {
        $result = $this->importer->import($appointmentsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Terminkategorien.
     *
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_appointment_categories', tags: ['Terminplan'])]
    #[OA\RequestBody(content: new Model(type: AppointmentCategoriesData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/appointments/categories', methods: ['POST'])]
    public function appointmentCategories(AppointmentCategoriesData $appointmentCategoriesData, AppointmentCategoriesImportStrategy $strategy): Response {
        $result = $this->importer->import($appointmentCategoriesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Klausuren.
     *
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_exams', tags: ['Klausurplan'])]
    #[OA\RequestBody(content: new Model(type: ExamsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/exams', methods: ['POST'])]
    public function exams(ExamsData $examsData, ExamsImportStrategy $strategy): Response {
        $result = $this->importer->import($examsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Klassen.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_grades', tags: ['Klassen'])]
    #[OA\RequestBody(content: new Model(type: GradesData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/grades', methods: ['POST'])]
    public function grades(GradesData $gradesData, GradesImportStrategy $strategy): Response {
        $result = $this->importer->import($gradesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Klassenleitungen.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_grade_teachers', tags: ['Klassen'])]
    #[OA\RequestBody(content: new Model(type: GradeTeachersData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/grades/teachers', methods: ['POST'])]
    public function gradeTeachers(GradeTeachersData $gradeTeachersData, GradeTeachersImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($gradeTeachersData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Klassenmitgliedschaften.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_grade_memberships', tags: ['Klassen'])]
    #[OA\RequestBody(content: new Model(type: GradeMembershipsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/grades/memberships', methods: ['POST'])]
    public function gradeMemberships(GradeMembershipsData $membershipsData, GradeMembershipImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($membershipsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Lernende.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_students', tags: ['Stammdaten'])]
    #[OA\RequestBody(content: new Model(type: StudentsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/students', methods: ['POST'])]
    public function students(StudentsData $studentsData, StudentsImportStrategy $strategy): Response {
        $result = $this->importer->import($studentsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Lerngruppen.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_studygroups', tags: ['Unterricht'])]
    #[OA\RequestBody(content: new Model(type: StudyGroupsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/studygroups', methods: ['POST'])]
    public function studyGroups(StudyGroupsData $studyGroupsData, StudyGroupImportStrategy $strategy): Response {
        $result = $this->importer->import($studyGroupsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Mitgliedschaften in Lerngruppen.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_studygroups_memberships', tags: ['Unterricht'])]
    #[OA\RequestBody(content: new Model(type: StudyGroupMembershipsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/studygroups/memberships', methods: ['POST'])]
    public function studyGroupsMemberships(StudyGroupMembershipsData $membershipsData, StudyGroupMembershipImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($membershipsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Fächer.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_subjects', tags: ['Stammdaten'])]
    #[OA\RequestBody(content: new Model(type: SubjectsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/subjects', methods: ['POST'])]
    public function subjects(SubjectsData $subjectsData, SubjectsImportStrategy $strategy): Response {
        $result = $this->importer->import($subjectsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Vertretungen.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_substitutions', tags: ['Vertretungsplan'])]
    #[OA\RequestBody(content: new Model(type: SubstitutionsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/substitutions', methods: ['POST'])]
    public function substitutions(SubstitutionsData $substitutionsData, SubstitutionsImportStrategy $strategy): Response {
        $result = $this->importer->import($substitutionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Lehrkräfte.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_teachers', tags: ['Stammdaten'])]
    #[OA\RequestBody(content: new Model(type: TeachersData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/teachers', methods: ['POST'])]
    public function teachers(TeachersData $teachersData, TeachersImportStrategy $strategy): Response {
        $result = $this->importer->import($teachersData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Stundenplanstunden.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_timetable_lessons', tags: ['Stundenplan'])]
    #[OA\RequestBody(content: new Model(type: TimetableLessonsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/timetable/lessons', methods: ['POST'])]
    public function timetableLessons(TimetableLessonsData $lessonsData, TimetableLessonsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($lessonsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Aufsichten (Stundenplan).
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_timetable_supervisions', tags: ['Stundenplan'])]
    #[OA\RequestBody(content: new Model(type: TimetableSupervisionsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/timetable/supervisions', methods: ['POST'])]
    public function timetableSupervisions(TimetableSupervisionsData $supervisionsData, TimetableSupervisionsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($supervisionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Unterrichte.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_tuitions', tags: ['Unterricht'])]
    #[OA\RequestBody(content: new Model(type: TuitionsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/tuitions', methods: ['POST'])]
    public function tuitions(TuitionsData $tuitionsData, TuitionsImportStrategy $strategy): Response {
        $result = $this->importer->import($tuitionsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Tagestexte.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_infotexts', tags: ['Vertretungsplan'])]
    #[OA\RequestBody(content: new Model(type: InfotextsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/infotexts', methods: ['POST'])]
    public function infotexts(InfotextsData $infotextsData, InfotextsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($infotextsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Absenzen
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_absences', tags: ['Vertretungsplan'])]
    #[OA\RequestBody(content: new Model(type: AbsencesData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/absences', methods: ['POST'])]
    public function absences(AbsencesData $absencesData, AbsencesImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($absencesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Imports Datenschutzkategorien (SchILD/SVWS)
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_privacy_categories', tags: ['Datenschutz'])]
    #[OA\RequestBody(content: new Model(type: PrivacyCategoriesData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/privacy/categories', methods: ['POST'])]
    public function privacyCategories(PrivacyCategoriesData $categoriesData, PrivacyCategoryImportStrategy $strategy): Response {
        $result = $this->importer->import($categoriesData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Räume.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_rooms', tags: ['Stundenplan'])]
    #[OA\RequestBody(content: new Model(type: RoomsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/rooms', methods: ['POST'])]
    public function rooms(RoomsData $roomsData, RoomImportStrategy $strategy): Response {
        $result = $this->importer->import($roomsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert unterrichtsfreie Tage bzw. Stunden.
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_free_timespans', tags: ['Vertretungsplan'])]
    #[OA\RequestBody(content: new Model(type: FreeLessonTimespansData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/free_lessons', methods: ['POST'])]
    public function freeLessons(FreeLessonTimespansData $timespansData, FreeTimespanImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($timespansData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Lernplattformen (SchILD/SVWS).
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_lms', tags: ['Lernplattformen'])]
    #[OA\RequestBody(content: new Model(type: LearningManagementSystemsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/lms', methods: ['POST'])]
    public function lms(LearningManagementSystemsData $learningManagementSystemsData, LearningManagementSystemsImportStrategy $strategy): Response {
        $result = $this->importer->import($learningManagementSystemsData, $strategy);
        return $this->fromResult($result);
    }

    /**
     * Importiert Informationen zu Lernplattformzustimmungen von Schülerinnen und Schülern (SchILD/SVWS).
     * @throws ImportException
     */
    #[OA\Post(operationId: 'import_student_lms', tags: ['Lernplattformen'])]
    #[OA\RequestBody(content: new Model(type: StudentLearningManagementSystemsData::class))]
    #[OA\Response(response: '200', description: 'Import erfolgreich.', content: new Model(type: ImportResponse::class))]
    #[OA\Response(response: '400', description: 'Fehlerhafte Anfrage.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '401', description: 'Kein Authentifizierungstoken angegeben.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '403', description: 'Fehlerhafte Rechte.', content: new Model(type: ErrorResponse::class))]
    #[OA\Response(response: '500', description: 'Serverfehler')]
    #[Route(path: '/lms/students', methods: ['POST'])]
    public function lmsInfo(StudentLearningManagementSystemsData $data, StudentLearningManagementSystemInformationImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($data, $strategy);
        return $this->fromResult($result);
    }
}