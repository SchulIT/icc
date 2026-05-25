<?php

declare(strict_types=1);

namespace App\Framework\Controller;

use App\Framework\Import\Importer;
use App\Framework\Import\ImportResult;
use App\Framework\Import\Json\ImportResponse;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/api/import')]
abstract class AbstractImportController extends AbstractController {

    public function __construct(
        protected SerializerInterface $serializer,
        protected Importer $importer
    ) {

    }

    protected function fromResult(ImportResult $importResult): Response {
        $response = new ImportResponse($importResult->getAdded(), $importResult->getUpdated(), $importResult->getRemoved(), $importResult->getIgnored());
        $json = $this->serializer->serialize($response, 'json');

        return new Response(
            $json,
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }
}
