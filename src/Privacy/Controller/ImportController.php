<?php

namespace App\Privacy\Controller;

use App\Framework\Controller\AbstractImportController;
use App\Framework\Http\ValueResolver\JsonPayload;
use App\Framework\Import\ImportException;
use App\Framework\Import\Json\ImportResponse;
use App\Framework\Json\Response\ErrorResponse;
use App\Privacy\Import\Json\PrivacyCategoriesData;
use App\Privacy\Import\PrivacyCategoryImportStrategy;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/api/import')]
class ImportController extends AbstractImportController {
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
    public function privacyCategories(#[JsonPayload] PrivacyCategoriesData $categoriesData, PrivacyCategoryImportStrategy $strategy): Response {
        $result = $this->importer->import($categoriesData, $strategy);
        return $this->fromResult($result);
    }
}