<?php

namespace App\Tools\TimetableGpuExport\Controller;

use App\Tools\TimetableGpuExport\Configuration;
use App\Tools\TimetableGpuExport\ConfigurationType;
use App\Tools\TimetableGpuExport\TimetableGpuExporter;
use App\Tools\TimetableGpuExport\Week;
use App\Timetable\Repository\TimetableWeekRepositoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools/gpu001', name: 'untis_timetable_export')]
class UntisTimetableExportAction extends AbstractController {
    public function __construct(private readonly TimetableGpuExporter $timetableGpuExporter, private readonly TimetableWeekRepositoryInterface $weekRepository) { }

    public function __invoke(Request $request): Response {
        $configuration = new Configuration();

        foreach($this->weekRepository->findAll() as $week) {
            $weekItem = new Week();
            $weekItem->week = $week;

            $configuration->weeks[] = $weekItem;
        }

        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            return $this->timetableGpuExporter->generateCsvResponse($configuration);
        }

        return $this->render('admin/tools/gpu001.html.twig', [
            'form' => $form->createView()
        ]);
    }
}