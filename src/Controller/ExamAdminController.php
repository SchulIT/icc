<?php

namespace App\Controller;

use App\Entity\ExamStudent;
use App\Exam\ExamSplitConfiguration;
use App\Exam\ExamSplitter;
use App\Exam\ExamStudentsResolver;
use App\Exam\ReassignmentsHelper;
use App\Form\ExamSplitConfigurationType;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Rooms\Reservation\ResourceAvailabilityHelper;
use App\Section\SectionResolverInterface;
use App\Sorting\ExamStudentStrategy;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\Exam;
use App\Entity\Student;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Form\ExamBulkType;
use App\Form\ExamStudentsType;
use App\Form\ExamType;
use App\Grouping\ExamWeekStrategy;
use App\Grouping\Grouper;
use App\Repository\ExamRepositoryInterface;
use App\Security\Voter\ExamVoter;
use App\Sorting\ExamDateGroupStrategy as ExamDateSortingStrategy;
use App\Sorting\ExamWeekGroupStrategy;
use App\Sorting\Sorter;
use App\Utils\CollectionUtils;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TeacherFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/exams')]
class ExamAdminController extends AbstractController {

    private const NumberOfExams = 25;
    private const ReassignCsrfId = 'reassign_exams';

    public function __construct(RefererHelper $redirectHelper, private ExamRepositoryInterface $repository, private readonly ExamStudentsResolver $examStudentsResolver, private readonly TranslatorInterface $translator) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'admin_exams')]
    public function index(SectionFilter $sectionFilter, GradeFilter $gradeFilter, TeacherFilter $teacherFilter, StudentFilter $studentFilter, Grouper $grouper, ExamRepositoryInterface $examRepository, Sorter $sorter, Request $request): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Manage);

        $page = $request->query->getInt('page');

        /** @var User $user */
        $user = $this->getUser();
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionFilterView->getCurrentSection(), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $sectionFilterView->getCurrentSection(), $user, $request->query->get('teacher') !== '✗' && $gradeFilterView->getCurrentGrade() === null);

        $paginator = $this->repository->getPaginator(self::NumberOfExams,
            $page,
            $gradeFilterView->getCurrentGrade(),
            $teacherFilterView->getCurrentTeacher(),
            $studentFilterView->getCurrentStudent(),
            null,
            false,
            null,
            null,
            $sectionFilterView->getCurrentSection()
        );
        $pages = 1;

        if($paginator->count() > 0) {
            $pages = ceil((float)$paginator->count() / self::NumberOfExams);
        }

        $exams = [ ];

        foreach($paginator->getIterator() as $exam) {
            if($this->isGranted(ExamVoter::Edit, $exam)) {
                $exams[] = $exam;
            }
        }

        $groups = $grouper->group($exams, ExamWeekStrategy::class);
        $sorter->sort($groups, ExamWeekGroupStrategy::class);
        $sorter->sortGroupItems($groups, ExamDateSortingStrategy::class);

        return $this->render('admin/exams/index.html.twig', [
            'groups' => $groups,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
            'teacherFilter' => $teacherFilterView,
            'studentFilter' => $studentFilterView,
            'page' => $page,
            'pages' => $pages
        ]);
    }

    #[Route(path: '/add', name: 'new_exam')]
    public function add(Request $request): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Add);
        $exam = new Exam();

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->get('addStudents')->getData() === true) {
                $this->examStudentsResolver->setExamStudents(
                    $exam,
                    $this->examStudentsResolver->resolveExamStudentsFromMembership($exam)
                );
            }

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.add.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/bulk', name: 'bulk_exams')]
    public function addBulk(Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_EXAMS_CREATOR');

        $defaultData = [
            'number' => 3,
            'tuitions' => [ ],
            'add_students' => true
        ];

        $form = $this->createForm(ExamBulkType::class, $defaultData);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $this->bulkCreateExams($data['number'], $data['tuitions'], $data['add_students']);

            $this->addFlash('success', 'admin.exams.bulk.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/bulk.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/reassign', name: 'reassign_student_exams')]
    public function reassign(StudentFilter $studentFilter, ReassignmentsHelper $reassignmentsHelper, SectionResolverInterface $sectionResolver, DateHelper $dateHelper, Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_EXAMS_ADMIN');

        /** @var User $user */
        $user = $this->getUser();

        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionResolver->getCurrentSection(), $user);

        try {
            $date = new DateTime($request->query->get('date'));
            $date->setTime(0,0,0);
        } catch(Exception $e) {
            $date = $dateHelper->getToday();
        }

        $reassignments = null;
        $isCsrfTokenInvalid = false;

        if($studentFilterView->getCurrentStudent() !== null && $sectionResolver->getCurrentSection() !== null) {
            $reassignments = $reassignmentsHelper->computeReassigns($studentFilterView->getCurrentStudent(), $sectionResolver->getCurrentSection(), $date);

            if($request->getMethod() === Request::METHOD_POST) {
                if($this->isCsrfTokenValid(self::ReassignCsrfId, $request->request->get('_token'))) {
                    $reassignmentsHelper->applyReassignment($studentFilterView->getCurrentStudent(), $reassignments);
                    $this->addFlash('success', 'admin.exams.reassign.success');

                    return $this->redirectToRoute('reassign_student_exams', [
                        'student' => $studentFilterView->getCurrentStudent()->getUuid()
                    ]);
                } else {
                    $isCsrfTokenInvalid = true;
                }
            }
        }

        return $this->render('admin/exams/reassign.html.twig', [
            'studentFilter' => $studentFilterView,
            'date' => $date,
            'reassignments' => $reassignments,
            'isCsrfTokenInvalid' => $isCsrfTokenInvalid,
            'csrfTokenId' => self::ReassignCsrfId
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_exam')]
    public function edit(Exam $exam, Request $request): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Edit, $exam);

        $form = $this->createForm(ExamType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if($form->get('addStudents')->getData() === true) {
                $this->examStudentsResolver->setExamStudents(
                    $exam,
                    $this->examStudentsResolver->resolveExamStudentsFromMembership($exam)
                );
            }

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.edit.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/edit.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    #[Route(path: '/{uuid}/unplan', name: 'unplan_exam')]
    public function unplan(Exam $exam, Request $request, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Unplan, $exam);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.exams.unplan.confirm',
            'message_parameters' => [
                '%date%' => $exam->getDate()->format($translator->trans('date.format')),
                '%lessons%' => $translator->trans('label.exam_lessons', [
                    '%start%' => $exam->getLessonStart(),
                    '%end%' => $exam->getLessonEnd(),
                    '%count%' => $exam->getLessonEnd() - $exam->getLessonStart() + 1
                ])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $exam->setDate(null);
            $exam->setLessonStart(0);
            $exam->setLessonEnd(0);

            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.unplan.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/unplan.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_exam')]
    public function remove(Exam $exam, Request $request, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(ExamVoter::Remove, $exam);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.exams.remove.confirm',
            'message_parameters' => [
                '%date%' => $exam->getDate() !== null ? $exam->getDate()->format($translator->trans('date.format')) : 'N/A',
                '%lessons%' => $translator->trans('label.exam_lessons', [
                    '%start%' => $exam->getLessonStart(),
                    '%end%' => $exam->getLessonEnd(),
                    '%count%' => $exam->getLessonEnd() - $exam->getLessonStart() + 1
                ])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($exam);

            $this->addFlash('success', 'admin.exams.remove.success');

            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/remove.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    #[Route(path: '/{uuid}/students', name: 'edit_exam_students')]
    public function students(Exam $exam, Request $request): Response {
        $exam = $this->repository->findOneById($exam->getId()); // Hack to get a sorted list of exam students

        $form = $this->createForm(ExamStudentsType::class, $exam);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($exam);

            $this->addFlash('success', 'admin.exams.students.success');
            return $this->redirectToRoute('admin_exams');
        }

        return $this->render('admin/exams/students.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    #[Route('/{uuid}/split', name: 'split_exam')]
    public function split(Exam $exam, Request $request, ExamSplitter $examSplitter, ResourceReservationRepositoryInterface $reservationRepository): Response {
        $this->denyAccessUnlessGranted('ROLE_EXAMS_ADMIN');

        $configuration = new ExamSplitConfiguration();
        $form = $this->createForm(ExamSplitConfigurationType::class, $configuration, ['exam_id' => $exam->getId()]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $examSplitter->split($exam, $configuration);

            foreach($result->exams as $resultingExam) {
                if($exam->getRoom() === null) {
                    continue;
                }

                $reservations = $reservationRepository->findAllByResourceAndDate($resultingExam->getRoom(), $resultingExam->getDate());

                for($lessonNumber = $resultingExam->getLessonStart(); $lessonNumber <= $resultingExam->getLessonEnd(); $lessonNumber++) {
                    foreach($reservations as $reservation) {
                        if($reservation->getLessonStart() <= $lessonNumber && $lessonNumber <= $reservation->getLessonEnd()) {
                            $form->addError(new FormError($this->translator->trans('admin.exams.split.error.room_unavailable', [
                                '%room%' => $exam->getRoom()->getName(),
                                '%teacher%' => $reservation->getTeacher()->getAcronym(),
                                '%lesson%' => $lessonNumber
                            ])));
                        }
                    }
                }
            }

            if(count($result->studentsNotMatched) > 0) {
                $form->addError(new FormError($this->translator->trans('admin.exams.split.error.students_left')));
            } else if(count($form->getErrors()) === 0) {
                $this->repository->remove($exam);

                foreach($result->exams as $exam) {
                    $this->repository->persist($exam);
                }

                $this->addFlash('success', 'admin.exams.split.success');
                return $this->redirectToRoute('admin_exams');
            }
        }

        return $this->render('admin/exams/split.html.twig', [
            'form' => $form->createView(),
            'exam' => $exam
        ]);
    }

    /**
     * @param Tuition[] $tuitions
     */
    private function bulkCreateExams(int $number, array $tuitions, bool $addStudents): void {
        $this->repository->beginTransaction();

        foreach($tuitions as $tuition) {
            $students = $tuition->getStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();

            for($i = 0; $i < $number; $i++) {
                $exam = new Exam();
                $exam->addTuition($tuition);

                if($addStudents === true) {
                    foreach ($students as $student) {
                        $exam->addStudent((new ExamStudent())->setStudent($student)->setTuition($tuition)->setExam($exam));
                    }
                }

                $this->repository->persist($exam);
            }
        }

        $this->repository->commit();
    }
}