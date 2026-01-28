<?php

namespace App\Controller\NRW;

use App\Book\Attendance\Export\Schild\ErrorResponse;
use App\Book\Attendance\Export\Schild\Exporter;
use App\Book\Attendance\Export\Schild\Request as SchildRequest;
use App\Book\Attendance\Export\Schild\SectionNotFoundException;
use App\Book\Attendance\Export\Schild\StudentNotFoundException;
use App\Controller\AbstractController;
use App\Request\JsonPayload;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

#[Route('/book/attendance/schild')]
class SchildLernabschnittsdatenController extends AbstractController {

    #[Route('', name: 'nrw_schild_lernabschnittsdaten_export')]
    public function index(): Response {
        return $this->render('books/attendance/export/schild_lernabschnittsdaten.html.twig');
    }

    #[Route('/json', name: 'nrw_schild_lernabschnittsdaten_export_json')]
    public function request(#[JsonPayload] SchildRequest $request, SerializerInterface $serializer, Exporter $exporter): JsonResponse {
        $response = $exporter->export($request);
        $statusCode = Response::HTTP_OK;

        if($response instanceof ErrorResponse) {
            $statusCode = Response::HTTP_BAD_REQUEST;
        }

        return new JsonResponse($serializer->serialize($response, 'json'), status: $statusCode, json: true);
    }
}