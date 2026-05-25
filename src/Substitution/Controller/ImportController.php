<?php

namespace App\Substitution\Controller;

use App\Common\Import\SubstitutionsImportStrategy;
use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use App\Substitution\Import\AbsencesImportStrategy;
use App\Substitution\Import\FreeTimespanImportStrategy;
use App\Substitution\Import\InfotextsImportStrategy;
use App\Substitution\Import\Json\AbsencesData;
use App\Substitution\Import\Json\FreeLessonTimespansData;
use App\Substitution\Import\Json\InfotextsData;
use App\Substitution\Import\Json\SubstitutionsData;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {

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
    public function substitutions(#[JsonPayload] SubstitutionsData $substitutionsData, SubstitutionsImportStrategy $strategy): Response {
        $result = $this->importer->import($substitutionsData, $strategy);
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
    public function infotexts(#[JsonPayload] InfotextsData $infotextsData, InfotextsImportStrategy $strategy): Response {
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
    public function absences(#[JsonPayload] AbsencesData $absencesData, AbsencesImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($absencesData, $strategy);
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
    public function freeLessons(#[JsonPayload] FreeLessonTimespansData $timespansData, FreeTimespanImportStrategy $strategy): Response {
        $result = $this->importer->replaceImport($timespansData, $strategy);
        return $this->fromResult($result);
    }
}