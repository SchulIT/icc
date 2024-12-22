<?php

namespace App\Controller;

use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Export\Untis\Timetable\Configuration;
use App\Export\Untis\Timetable\ConfigurationType;
use App\Export\Untis\Timetable\TimetableGpuExporter;
use App\Export\Untis\Timetable\Week;
use App\Form\GradeTuitionTeachersIntersectionType;
use App\Form\TuitionReportInputType;
use App\Menu\AdminToolsMenuBuilder;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Tools\GradeTuitionTeachersIntersectionInput;
use App\Tools\GradeTuitionTeachersIntersectionTool;
use App\Tools\MissingUsersReportHelper;
use App\Tools\SubstitutionEvaluation\ReportInput;
use App\Tools\SubstitutionEvaluation\ReportInputType;
use App\Tools\SubstitutionEvaluation\ReportManager;
use App\Tools\TuitionReport;
use App\Tools\TuitionReportInput;
use App\View\Filter\StudyGroupFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools')]
class ToolsController extends AbstractController {

    #[Route('', name: 'tools')]
    public function index(AdminToolsMenuBuilder $menuBuilder): Response {
        $toolsMenu = $menuBuilder->toolsMenu([]);

        foreach($toolsMenu->getChildren() as $child) {
            if(!empty($child->getUri())) {
                return $this->redirect($child->getUri());
            }
        }

        throw new AccessDeniedHttpException();
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

    #[Route('/missing_users', name: 'missing_users_tool')]
    public function missingUsers(Request $request, StudyGroupFilter $studyGroupFilter, SectionResolverInterface $sectionResolver, MissingUsersReportHelper $missingUsersReportHelper, Sorter $sorter): Response {
        /** @var User $user */
        $user = $this->getUser();

        $studyGroupFilterView = $studyGroupFilter->handle($request->query->get('study_group'), $sectionResolver->getCurrentSection(), $user);

        $missingStudents = [ ];
        $missingParents = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            $students = $studyGroupFilterView->getCurrentStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();
            $sorter->sort($students, StudentStrategy::class);
            $missingStudents = $missingUsersReportHelper->getMissingStudents($students);
            $missingParents = $missingUsersReportHelper->getMissingParents($students);
        }

        return $this->render('admin/tools/missing_users.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'missingStudents' => $missingStudents,
            'missingParents' => $missingParents
        ]);
    }

    #[Route('/substitution_report', name: 'substitution_report_tool')]
    public function substitutionReport(ReportManager $manager, Request $request): Response {
        $input = new ReportInput();
        $form = $this->createForm(ReportInputType::class, $input);
        $form->handleRequest($request);

        $result = [ ];

        if($form->isSubmitted() && $form->isValid()) {
            $result = $manager->evaluate($input);
        }

        return $this->render('admin/tools/substitution_report.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}