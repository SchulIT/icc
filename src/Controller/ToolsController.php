<?php

namespace App\Controller;

use App\Export\Untis\Timetable\Configuration;
use App\Export\Untis\Timetable\ConfigurationType;
use App\Export\Untis\Timetable\TimetableGpuExporter;
use App\Export\Untis\Timetable\Week;
use App\Form\GradeTuitionTeachersIntersectionType;
use App\Form\TuitionReportInputType;
use App\Menu\AdminToolsMenuBuilder;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Sorting\Sorter;
use App\Tools\GradeTuitionTeachersIntersectionInput;
use App\Tools\GradeTuitionTeachersIntersectionTool;
use App\Tools\TuitionReport;
use App\Tools\TuitionReportInput;
use App\View\Filter\SectionFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tools')]
class ToolsController extends AbstractController {

    #[Route('', name: 'tools')]
    public function index(AdminToolsMenuBuilder $menuBuilder): Response {
        $toolsMenu = $menuBuilder->toolsMenu([]);

        $firstKey = array_key_first($toolsMenu->getChildren());
        $first = $toolsMenu->getChildren()[$firstKey];

        return $this->redirect($first->getUri());
    }

    #[Route('/teacher_intersection', name: 'grade_tuition_teachers_intersection')]
    public function gradeTuitionTeachersIntersection(Request $request, GradeTuitionTeachersIntersectionTool $tool): Response {
        $input = new GradeTuitionTeachersIntersectionInput();
        $form = $this->createForm(GradeTuitionTeachersIntersectionType::class, $input);
        $form->handleRequest($request);

        $intersections = null;

        if($form->isSubmitted() && $form->isValid()) {
            $intersections = $tool->computeIntersections($input);
        }

        return $this->render('admin/tools/teacher_intersection.html.twig', [
            'form' => $form->createView(),
            'intersections' => $intersections
        ]);
    }

    #[Route('/tuition_report', name: 'tuition_report_tool')]
    public function tuitionReport(Request $request, TuitionReport $tuitionReport): Response {
        $input = new TuitionReportInput();
        $form = $this->createForm(TuitionReportInputType::class, $input);
        $form->handleRequest($request);

        $result = null;

        if($form->isSubmitted() && $form->isValid()) {
            if($request->request->get('export') === 'csv') {
                return $tuitionReport->generateReportAsCsvResponse($input->section, $input->types);
            }

            $result = $tuitionReport->generateReport($input->section, $input->types);
        }

        return $this->render('admin/tools/tuition_report.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }

    #[Route('/gpu001', name: 'untis_timetable_export')]
    public function exportGpu001(Request $request, TimetableGpuExporter $timetableGpuExporter, TimetableWeekRepositoryInterface $weekRepository, Sorter $sorter): Response {
        $configuration = new Configuration();

        foreach($weekRepository->findAll() as $week) {
            $weekItem = new Week();
            $weekItem->week = $week;

            $configuration->weeks[] = $weekItem;
        }

        $form = $this->createForm(ConfigurationType::class, $configuration);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            return $timetableGpuExporter->generateCsvResponse($configuration);
        }

        return $this->render('admin/tools/gpu001.html.twig', [
            'form' => $form->createView()
        ]);
    }
}