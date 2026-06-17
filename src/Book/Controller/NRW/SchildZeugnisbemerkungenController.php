<?php

declare(strict_types=1);

namespace App\Book\Controller\NRW;

use App\Book\ReportRemark\Export\Schild\Exporter;
use App\Book\ReportRemark\Export\Schild\ExportRequest;
use App\Book\ReportRemark\Export\Schild\ExportRequestType;
use App\Common\Section\SectionResolverInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/export/schild/zeugnisbemerkungen')]
class SchildZeugnisbemerkungenController extends AbstractController {
    #[Route('', name: 'export_schild_zeugnisbemerkungen')]
    public function index(
        Request $request,
        Exporter $exporter,
        SectionResolverInterface $sectionResolver,
    ): Response {
        $exportRequest = new ExportRequest();
        $exportRequest->section = $sectionResolver->getCurrentSection();

        $form = $this->createForm(ExportRequestType::class, $exportRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $exporter->exportResponse($exportRequest);
        }

        return $this->render('books/export/schild_zeugnisbemerkungen.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
