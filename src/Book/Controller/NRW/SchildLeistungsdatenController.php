<?php

namespace App\Book\Controller\NRW;

use App\Book\Grade\Export\Schild\BulkRequest as SchildBulkRequest;
use App\Book\Grade\Export\Schild\Exporter;
use App\Book\Grade\Export\Schild\Request as SchildRequest;
use App\Book\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Book\Settings\TuitionGradebookSettings;
use App\Framework\Controller\AbstractController;
use App\Framework\Http\ValueResolver\JsonPayload;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/export/schild/leistungsdaten')]
class SchildLeistungsdatenController extends AbstractController {

    #[Route('', name: 'nrw_schild_leistungsdaten_export')]
    public function index(TuitionGradeCategoryRepositoryInterface $categoryRepository, TuitionGradebookSettings $gradebookSettings): Response {
        return $this->render('books/export/schild_leistungsdaten.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage(),
        ]);
    }

    #[Route('/json', name: 'nrw_schild_leistungsdaten_export_json')]
    public function request(#[JsonPayload] SchildRequest $request, SerializerInterface $serializer, Exporter $exporter): Response {
        $response = $exporter->export($request);

        return new JsonResponse($serializer->serialize($response, 'json'), json: true);
    }

    #[Route('/json/bulk', name: 'nrw_schild_leistungsdaten_export_json_bulk')]
    public function bulk(#[JsonPayload] SchildBulkRequest $request, SerializerInterface $serializer, Exporter $exporter): Response {
        $response = $exporter->exportBulk($request);

        return new JsonResponse($serializer->serialize($response, 'json'), json: true);
    }
}