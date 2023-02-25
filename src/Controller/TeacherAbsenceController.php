<?php

namespace App\Controller;

use App\Entity\DateLesson;
use App\Entity\TeacherAbsence;
use App\Entity\TeacherAbsenceLesson;
use App\Entity\TimetableLesson;
use App\Entity\User;
use App\Form\TeacherAbsenceLessonType;
use App\Form\TeacherAbsenceType;
use App\Repository\TeacherAbsenceRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\TeacherAbsenceVoter;
use App\Settings\DashboardSettings;
use App\Settings\TimetableSettings;
use App\Sorting\Sorter;
use App\Sorting\TeacherAbsenceLessonStrategy;
use App\Sorting\TimetableLessonStrategy;
use App\Timetable\TimetableTimeHelper;
use App\View\Filter\SectionFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/absence/teachers')]
class TeacherAbsenceController extends AbstractController {

    public const ItemsPerPage = 25;

    private const CSRF_TOKEN_ID = 'teacher_absence';

    use DateTimeHelperTrait;

    public function __construct(private readonly TeacherAbsenceRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'teacher_absences')]
    public function index(TeacherFilter $teacherFilter, Request $request, SectionFilter $sectionFilter) {
        /** @var User $user */
        $user = $this->getUser();
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $hideProcessed = $request->query->get('hide_processed') === '✓';

        if($this->isGranted(TeacherAbsenceVoter::CanViewAny)) {
            $teacherFilterView = $teacherFilter->handle($request->query->get('teacher'), $sectionFilterView->getCurrentSection(), $user, false);
        } else {
            $teacherFilterView = $teacherFilter->handle(null, $sectionFilterView->getCurrentSection(), $user, true);
        }

        $page = $request->query->getInt('page', 1);
        $paginator = $this->repository->getPaginator(self::ItemsPerPage, $page, $hideProcessed, null, null, $teacherFilterView->getCurrentTeacher());

        $pages = ceil((float)$paginator->count() / self::ItemsPerPage);

        return $this->render('absences/teachers/index.html.twig', [
            'page' => $page,
            'pages' => $pages,
            'teacherFilter' => $teacherFilterView,
            'sectionFilter' => $sectionFilterView,
            'absences' => $paginator->getIterator(),
            'hideProcessed' => $hideProcessed
        ]);
    }

    #[Route('/add', name: 'add_teacher_absence')]
    public function add(Request $request, TimetableLessonRepositoryInterface $timetableLessonRepository, Sorter $sorter,
                        TimetableSettings $timetableSettings, DateHelper $dateHelper, DashboardSettings $dashboardSettings, TimetableTimeHelper $timeHelper): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::NewAbsence);

        /** @var User $user */
        $user = $this->getUser();

        $absence = new TeacherAbsence();
        if ($user->getTeacher() !== null) {
            $absence->setTeacher($user->getTeacher());
        }
        $absence->setFrom($timeHelper->getLessonDateForDateTime($this->getTodayOrNextDay($dateHelper, $dashboardSettings->getNextDayThresholdTime())));
        $absence->setUntil(new DateLesson());
        $absence->getUntil()->setDate(clone $absence->getFrom()->getDate());
        $absence->getUntil()->setLesson($timetableSettings->getMaxLessons());

        $form = $this->createForm(TeacherAbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addAbsenceLessons($absence, $timetableLessonRepository);

            $this->repository->persist($absence);
            $this->addFlash('success', 'absences.teachers.add.success');
            return $this->redirectToRoute('show_teacher_absence', [
                'uuid' => $absence->getUuid()->toString()
            ]);
        }

        return $this->render('absences/teachers/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    private function addAbsenceLessons(TeacherAbsence $absence, TimetableLessonRepositoryInterface $timetableLessonRepository) {
        $lessons = array_filter(
            $timetableLessonRepository->findAllByTeacher($absence->getFrom()->getDate(), $absence->getUntil()->getDate(), $absence->getTeacher()),
            fn(TimetableLesson $lesson) => (new DateLesson())->setLesson($lesson->getLessonStart())->setDate($lesson->getDate())->isBetween($absence->getFrom(), $absence->getUntil())
        );

        $existingLessons = $absence->getLessons()->map(fn(TeacherAbsenceLesson $lesson) => $lesson->getLesson()?->getUuid()->toString())->toArray();

        foreach ($lessons as $lesson) {
            if(!in_array($lesson->getUuid()->toString(), $existingLessons)) {
                $absenceLesson = (new TeacherAbsenceLesson())
                    ->setAbsence($absence)
                    ->setLesson($lesson);

                $absence->addLesson($absenceLesson);
            }
        }
    }

    #[Route('/{uuid}', name: 'show_teacher_absence')]
    public function show(TeacherAbsence $absence, Sorter $sorter): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Show, $absence);
        $sortedLessons = $absence->getLessons()->toArray();
        $sorter->sort($sortedLessons, TeacherAbsenceLessonStrategy::class);

        return $this->render('absences/teachers/show.html.twig', [
            'absence' => $absence,
            'lessons' => $sortedLessons
        ]);
    }

    #[Route('/{uuid}/processed', name: 'mark_teacher_absence_processed')]
    public function markProcessed(TeacherAbsence $absence, Request $request): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Process, $absence);

        if(!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $absence->setProcessedAt(new DateTime());
            $absence->setProcessedBy($this->getUser());
            $this->repository->persist($absence);
        }

        return $this->redirectToRoute('show_teacher_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_teacher_absence')]
    public function edit(TeacherAbsence $absence, Request $request, TimetableLessonRepositoryInterface $timetableLessonRepository): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Edit, $absence);

        $form = $this->createForm(TeacherAbsenceType::class, $absence);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addAbsenceLessons($absence, $timetableLessonRepository);

            $this->repository->persist($absence);
            $this->addFlash('success', 'absences.teachers.edit.success');
            return $this->redirectToRoute('show_teacher_absence', [
                'uuid' => $absence->getUuid()->toString()
            ]);
        }

        return $this->render('absences/teachers/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/missing/add', name: 'add_missing_absence_lessons')]
    public function addMissingAbsenceLessons(TeacherAbsence $absence, Request $request, TimetableLessonRepositoryInterface $timetableLessonRepository): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Edit, $absence);

        if(!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            $this->addAbsenceLessons($absence, $timetableLessonRepository);
            $this->repository->persist($absence);
        }

        return $this->redirectToRoute('show_teacher_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route('/{uuid}/missing/remove', name: 'remove_missing_absence_lessons')]
    public function removeMissingAbsenceLessons(TeacherAbsence $absence, Request $request): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Edit, $absence);

        if(!$this->isCsrfTokenValid(self::CSRF_TOKEN_ID, $request->query->get('_csrf_token'))) {
            $this->addFlash('error', 'CSRF token invalid.');
        } else {
            foreach ($absence->getLessons() as $lesson) {
                if ($lesson->getLesson() === null) {
                    $this->repository->remove($lesson);
                }
            }

            $this->repository->persist($absence);
        }

        return $this->redirectToRoute('show_teacher_absence', [
            'uuid' => $absence->getUuid()
        ]);
    }

    #[Route('/l/{uuid}/edit', name: 'edit_teacher_absence_lesson')]
    public function editLesson(TeacherAbsenceLesson $lesson, Request $request): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Edit, $lesson->getAbsence());

        $form = $this->createForm(TeacherAbsenceLessonType::class, $lesson);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($lesson);
            $this->addFlash('success', 'absences.teachers.edit.lesson.success');

            return $this->redirectToRoute('show_teacher_absence', [
                'uuid' => $lesson->getAbsence()->getUuid()
            ]);
        }

        return $this->render('absences/teachers/edit_lesson.html.twig', [
            'lesson' => $lesson,
            'absence' => $lesson->getAbsence(),
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_teacher_absence')]
    public function remove(TeacherAbsence $absence, Request $request): Response {
        $this->denyAccessUnlessGranted(TeacherAbsenceVoter::Remove, $absence);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'absences.teachers.remove.confirm'
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($absence);

            $this->addFlash('success', 'absences.teachers.remove.success');

            return $this->redirectToRoute('teacher_absences');
        }

        return $this->render('absences/teachers/remove.html.twig', [
            'form' => $form->createView(),
            'absence' => $absence
        ]);
    }
}