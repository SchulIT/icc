<?php

namespace App\Timetable\Controller;

use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use App\Timetable\Import\Json\TimetableLessonsData;
use App\Timetable\Import\Json\TimetableSupervisionsData;
use App\Timetable\Import\TimetableLessonsImportStrategy;
use App\Timetable\Import\TimetableSupervisionsImportStrategy;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {
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
    public function timetableLessons(#[JsonPayload] TimetableLessonsData $lessonsData, TimetableLessonsImportStrategy $strategy): Response {
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
    public function timetableSupervisions(#[JsonPayload] TimetableSupervisionsData $supervisionsData, TimetableSupervisionsImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($supervisionsData, $strategy);
        return $this->fromResult($result);
    }
}