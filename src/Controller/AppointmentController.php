<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\AppointmentCategory;
use App\Entity\DeviceToken;
use App\Entity\DeviceTokenType;
use App\Entity\MessageScope;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use App\Export\AppointmentIcsExporter;
use App\Form\DeviceTokenType as DeviceTokenTypeForm;
use App\Grouping\AppointmentDateStrategy;
use App\Grouping\Grouper;
use App\Repository\AppointmentRepositoryInterface;
use App\Security\Devices\DeviceManager;
use App\Sorting\AppointmentDateGroupStrategy;
use App\Sorting\AppointmentDateStrategy as AppointmentDateSortingStrategy;
use App\Sorting\Sorter;
use App\Utils\ColorUtils;
use App\View\Filter\AppointmentCategoriesFilter;
use App\View\Filter\GradeFilter;
use App\View\Filter\StudentFilter;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/appointments")
 */
class AppointmentController extends AbstractControllerWithMessages {

    /**
     * @Route("", name="appointments")
     */
    public function index(AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, GradeFilter $gradeFilter) {
        /** @var User $user */
        $user = $this->getUser();

        $categoryFilterView = $categoryFilter->handle([ ]);
        $studentFilterView = $studentFilter->handle(null, $user);
        $gradeFilterView = $gradeFilter->handle(null, $user);

        return $this->renderWithMessages('appointments/index.html.twig', [
            'categoryFilter' => $categoryFilterView,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView
        ]);
    }

    /**
     * @Route("/xhr", name="appointments_xhr", methods={"GET"})
     */
    public function indexXhr(AppointmentRepositoryInterface $appointmentRepository, ColorUtils $colorUtils,
                             AppointmentCategoriesFilter $categoryFilter, StudentFilter $studentFilter, GradeFilter $gradeFilter, Request $request,
                             ?int $studentId = null, ?int $gradeId = null, ?string $query = null, ?bool $showAll = false) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        $categoryFilterView = $categoryFilter->handle(explode(',', $request->query->get('categoryIds', '')));
        $studentFilterView = $studentFilter->handle($studentId, $user);
        $gradeFilterView = $gradeFilter->handle($gradeId, $user);

        $appointments = [ ];

        $includeHiddenFromStudents = $isStudent === false;
        $today = null;

        if($studentFilterView->getCurrentStudent() !== null) {
            $appointments = $appointmentRepository->findAllForStudents([$studentFilterView->getCurrentStudent()], $today, $includeHiddenFromStudents);
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $appointments = $appointmentRepository->findAllForGrade($gradeFilterView->getCurrentGrade(), $today, $includeHiddenFromStudents);
        } else {
            if($isStudent || $isParent) {
                $appointments = $appointmentRepository->findAllForStudents($user->getStudents()->toArray(), $today, $includeHiddenFromStudents);
            } else {
                $appointments = $appointmentRepository->findAll([ ], null, $today);
            }
        }

        if(!empty($categoryFilterView->getCurrentCategories())) {
            $selectedCategoryIds = array_map(function(AppointmentCategory $category) {
                return $category->getId();
            }, $categoryFilterView->getCurrentCategories());

            $appointments = array_filter($appointments, function(Appointment $appointment) use($selectedCategoryIds) {
                return in_array($appointment->getCategory()->getId(), $selectedCategoryIds);
            });
        }

        $json = [ ];

        foreach($appointments as $appointment) {
            $json[] = [
                'id' => $appointment->getId(),
                'allDay' => $appointment->isAllDay(),
                'title' => $appointment->getTitle(),
                'textColor' => $colorUtils->getForeground($appointment->getCategory()->getColor()),
                'backgroundColor' => $appointment->getCategory()->getColor(),
                'start' => $appointment->getStart()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'end' => $appointment->getEnd()->format($appointment->isAllDay() ? 'Y-m-d' : 'Y-m-d H:i'),
                'extendedProps' => [
                    'content' => $appointment->getContent(),
                    'study_groups' => implode(', ', $appointment->getStudyGroups()->map(function(StudyGroup $studyGroup) {
                        return $studyGroup->getName();
                    })->toArray())
                ]
            ];
        }

        return $this->json($json);
    }

    /**
     * @Route("/export", name="appointments_export")
     */
    public function export(Request $request, DeviceManager $manager) {
        /** @var User $user */
        $user = $this->getUser();

        $deviceToken = (new DeviceToken())
            ->setType(DeviceTokenType::Calendar())
            ->setUser($user);

        $form = $this->createForm(DeviceTokenTypeForm::class, $deviceToken);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $deviceToken = $manager->persistDeviceToken($deviceToken);
        }

        return $this->renderWithMessages('appointments/export.html.twig', [
            'form' => $form->createView(),
            'token' => $deviceToken
        ]);
    }

    /**
     * @Route("/ics/download", name="appointments_ics")
     * @Route("/ics/download/{token}", name="appointments_ics_token")
     */
    public function ics(AppointmentIcsExporter $exporter) {
        /** @var User $user */
        $user = $this->getUser();

        return $exporter->getIcsResponse($user);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Appointments();
    }
}