<?php

namespace App\Exam\Controller;

use App\Exam\Import\ExamsImportStrategy;
use App\Exam\Import\Json\ExamsData;
use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {
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
    public function exams(#[JsonPayload] ExamsData $examsData, ExamsImportStrategy $strategy): Response {
        $result = $this->importer->import($examsData, $strategy);
        return $this->fromResult($result);
    }
}