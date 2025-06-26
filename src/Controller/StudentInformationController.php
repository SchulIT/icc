<?php

namespace App\Controller;

use App\Converter\StudentStringConverter;
use App\Entity\StudentInformation;
use App\Entity\User;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Form\StudentInformationType;
use App\Repository\StudentInformationRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\StudentInformationStrategy;
use App\Sorting\Sorter;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/student/extra', priority: 10)]
class StudentInformationController extends AbstractController {
    public function __construct(private readonly StudentInformationRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'student_information_index')]
    public function index(GradeFilter $gradeFilter, StudentFilter $studentFilter, SectionFilter $sectionFilter, Sorter $sorter, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user, $studentFilterView->getCurrentStudent() === null);

        $info = [ ];

        if($gradeFilterView->getCurrentGrade() !== null) {
            $info = $this->repository->findByGrade($gradeFilterView->getCurrentGrade(),  $sectionFilterView->getCurrentSection(), null, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $info = $this->repository->findByStudents([$studentFilterView->getCurrentStudent()], null, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        $sorter->sort($info, StudentInformationStrategy::class);

        return $this->render('student/extra/index.html.twig', [
            'info' => $info,
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'sectionFilter' => $sectionFilterView
        ]);
    }

    #[Route('/add', name: 'add_student_information')]
    public function add(Request $request, SectionResolverInterface $sectionResolver): Response {
        $info = new StudentInformation();
        $form = $this->createForm(StudentInformationType::class, $info);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($info);
            $this->addFlash('success', 'students.extra.add.success');

            return $this->redirectToRoute('student_information_index', [
               'grade' => $info->getStudent()->getGrade($sectionResolver->getCurrentSection())?->getUuid()
            ]);
        }

        return $this->render('student/extra/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_student_information')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentInformation $info, Request $request, SectionResolverInterface $sectionResolver): Response {
        $form = $this->createForm(StudentInformationType::class, $info);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($info);
            $this->addFlash('success', 'students.extra.edit.success');

            return $this->redirectToRoute('student_information_index', [
                'grade' => $info->getStudent()->getGrade($sectionResolver->getCurrentSection())?->getUuid()
            ]);
        }

        return $this->render('student/extra/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_student_information')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] StudentInformation $information, Request $request, StudentStringConverter $stringConverter, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'students.extra.remove.confirm',
            'message_parameters' => [
                '%student%' => $stringConverter->convert($information->getStudent()),
                '%from%' => $information->getFrom()->format($translator->trans('date.format')),
                '%until%' => $information->getUntil()->format($translator->trans('date.format'))
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($information);
            $this->addFlash('success', 'students.extra.remove.success');

            return $this->redirectToRoute('student_information_index', [
                'student' => $information->getStudent()->getUuid()
            ]);
        }

        return $this->render('student/extra/remove.html.twig', [
            'form' => $form->createView(),
            'info' => $information
        ]);
    }
}