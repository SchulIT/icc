<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\DeviceToken;
use App\Entity\DeviceTokenType;
use App\Entity\MessageScope;
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
use App\View\Filter\AppointmentCategoryFilter;
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
    public function index(AppointmentRepositoryInterface $appointmentRepository, DateHelper $dateHelper, Sorter $sorter, Grouper $grouper,
                          AppointmentCategoryFilter $categoryFilter, StudentFilter $studentFilter, GradeFilter $gradeFilter,
                          ?int $studentId = null, ?int $gradeId = null, ?int $categoryId = null, ?string $query = null, ?bool $showAll = false) {
        /** @var User $user */
        $user = $this->getUser();
        $isStudent = $user->getUserType()->equals(UserType::Student());
        $isParent = $user->getUserType()->equals(UserType::Parent());

        $categoryFilterView = $categoryFilter->handle($categoryId);
        $studentFilterView = $studentFilter->handle($studentId, $user);
        $gradeFilterView = $gradeFilter->handle($gradeId, $user);

        $appointments = [ ];

        $includeHiddenFromStudents = $isStudent === false;
        $today = $showAll ? null : $dateHelper->getToday();

        if($studentFilterView->getCurrentStudent() !== null) {
            $appointments = $appointmentRepository->findAllForStudents([$studentFilterView->getCurrentStudent()], $today, $includeHiddenFromStudents);
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $appointments = $appointmentRepository->findAllForGrade($gradeFilterView->getCurrentGrade(), $today, $includeHiddenFromStudents);
        } else {
            if($isStudent || $isParent) {
                $appointments = $appointmentRepository->findAllForStudents($user->getStudents()->toArray(), $today, $includeHiddenFromStudents);
            } else {
                $appointments = $appointmentRepository->findAll(null, null, $today);
            }
        }

        if($categoryFilterView->getCurrentCategory() !== null) {
            $appointments = array_filter($appointments, function(Appointment $appointment) use($categoryFilterView) {
                return $appointment->getCategory()->getId() === $categoryFilterView->getCurrentCategory()->getId();
            });
        }

        $groups = $grouper->group($appointments, AppointmentDateStrategy::class);
        $sorter->sort($groups, AppointmentDateGroupStrategy::class);
        $sorter->sortGroupItems($groups, AppointmentDateSortingStrategy::class);

        return $this->render('appointments/index.html.twig', [
            'categoryFilter' => $categoryFilterView,
            'studentFilter' => $studentFilterView,
            'gradeFilter' => $gradeFilterView,
            'groups' => $groups,
            'query' => $query,
            'showAll' => $showAll
        ]);
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

        return $this->render('appointments/export.html.twig', [
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