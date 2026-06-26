<?php

declare(strict_types=1);

namespace App\Appointment\Controller;

use App\Appointment\Import\AppointmentCategoriesImportStrategy;
use App\Appointment\Import\AppointmentsImportStrategy;
use App\Appointment\Import\Json\AppointmentCategoriesData;
use App\Appointment\Import\Json\AppointmentsData;
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
    #[Route(path: '/appointments', name: 'api_appointments', methods: ['POST'])]
    public function appointments(#[JsonPayload] AppointmentsData $appointmentsData, AppointmentsImportStrategy $strategy): Response {
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
    public function appointmentCategories(#[JsonPayload] AppointmentCategoriesData $appointmentCategoriesData, AppointmentCategoriesImportStrategy $strategy): Response {
        $result = $this->importer->import($appointmentCategoriesData, $strategy);
        return $this->fromResult($result);
    }
}
