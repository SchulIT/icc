<?php

namespace App\Common\Controller;

use App\Common\Import\GradeMembershipImportStrategy;
use App\Common\Import\GradesImportStrategy;
use App\Common\Import\GradeTeachersImportStrategy;
use App\Common\Import\Json\GradeMembershipsData;
use App\Common\Import\Json\GradesData;
use App\Common\Import\Json\GradeTeachersData;
use App\Common\Import\Json\RoomsData;
use App\Common\Import\Json\StudentsData;
use App\Common\Import\Json\StudyGroupMembershipsData;
use App\Common\Import\Json\StudyGroupsData;
use App\Common\Import\Json\SubjectsData;
use App\Common\Import\Json\TeachersData;
use App\Common\Import\Json\TuitionsData;
use App\Common\Import\RoomImportStrategy;
use App\Common\Import\StudentsImportStrategy;
use App\Common\Import\StudyGroupImportStrategy;
use App\Common\Import\StudyGroupMembershipImportStrategy;
use App\Common\Import\SubjectsImportStrategy;
use App\Common\Import\TeachersImportStrategy;
use App\Common\Import\TuitionsImportStrategy;
use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {
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
    public function grades(#[JsonPayload] GradesData $gradesData, GradesImportStrategy $strategy): Response {
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
    public function gradeTeachers(#[JsonPayload] GradeTeachersData $gradeTeachersData, GradeTeachersImportStrategy $strategy): Response {
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
    public function gradeMemberships(#[JsonPayload] GradeMembershipsData $membershipsData, GradeMembershipImportStrategy $strategy): Response {
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
    public function students(#[JsonPayload] StudentsData $studentsData, StudentsImportStrategy $strategy): Response {
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
    public function studyGroups(#[JsonPayload] StudyGroupsData $studyGroupsData, StudyGroupImportStrategy $strategy): Response {
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
    public function studyGroupsMemberships(#[JsonPayload] StudyGroupMembershipsData $membershipsData, StudyGroupMembershipImportStrategy $strategy): Response {
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
    public function subjects(#[JsonPayload] SubjectsData $subjectsData, SubjectsImportStrategy $strategy): Response {
        $result = $this->importer->import($subjectsData, $strategy);
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
    public function teachers(#[JsonPayload] TeachersData $teachersData, TeachersImportStrategy $strategy): Response {
        $result = $this->importer->import($teachersData, $strategy);
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
    public function tuitions(#[JsonPayload] TuitionsData $tuitionsData, TuitionsImportStrategy $strategy): Response {
        $result = $this->importer->import($tuitionsData, $strategy);
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
    public function rooms(#[JsonPayload] RoomsData $roomsData, RoomImportStrategy $strategy): Response {
        $result = $this->importer->import($roomsData, $strategy);
        return $this->fromResult($result);
    }
}