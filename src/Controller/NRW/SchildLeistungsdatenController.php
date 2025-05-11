<?php

namespace App\Controller\NRW;

use App\Book\Grade\Export\Schild\Exporter;
use App\Book\Grade\Export\Schild\Request as SchildRequest;
use App\Controller\AbstractController;
use App\Repository\TuitionGradeCategoryRepositoryInterface;
use App\Request\JsonPayload;
use App\Section\SectionResolverInterface;
use App\Settings\TuitionGradebookSettings;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/gradebook/schild')]
class SchildLeistungsdatenController extends AbstractController {

    #[Route('', name: 'nrw_schild_leistungsdaten_export')]
    public function index(TuitionGradeCategoryRepositoryInterface $categoryRepository, TuitionGradebookSettings $gradebookSettings): Response {
        return $this->render('books/grades/export/schild_leistungsdaten.html.twig', [
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
}