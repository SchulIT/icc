<?php

namespace App\LearningManagementSystem\Controller;

use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use App\LearningManagementSystem\Import\Json\LearningManagementSystemsData;
use App\LearningManagementSystem\Import\Json\StudentLearningManagementSystemsData;
use App\LearningManagementSystem\Import\LearningManagementSystemsImportStrategy;
use App\LearningManagementSystem\Import\StudentLearningManagementSystemInformationImportStrategy;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {
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
    public function lms(#[JsonPayload] LearningManagementSystemsData $learningManagementSystemsData, LearningManagementSystemsImportStrategy $strategy): Response {
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
    public function lmsInfo(#[JsonPayload] StudentLearningManagementSystemsData $data, StudentLearningManagementSystemInformationImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($data, $strategy);
        return $this->fromResult($result);
    }
}