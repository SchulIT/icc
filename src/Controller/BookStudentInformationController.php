<?php

namespace App\Controller;

use App\Converter\StudentStringConverter;
use App\Entity\BookStudentInformation;
use App\Entity\User;
use App\Form\BookStudentInformationType;
use App\Repository\BookStudentInformationRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Sorting\BookStudentInformationStrategy;
use App\Sorting\Sorter;
use App\View\Filter\GradeFilter;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/book/extra/students')]
class BookStudentInformationController extends AbstractController {
    public function __construct(private readonly BookStudentInformationRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'book_student_information_index')]
    public function index(GradeFilter $gradeFilter, StudentFilter $studentFilter, SectionFilter $sectionFilter, Sorter $sorter, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user);
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user, $studentFilterView->getCurrentStudent() === null);

        $info = [ ];

        if($gradeFilterView->getCurrentGrade() !== null) {
            $info = $this->repository->findByGrade($gradeFilterView->getCurrentGrade(), $sectionFilterView->getCurrentSection(), $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $info = $this->repository->findByStudents([$studentFilterView->getCurrentStudent()], $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());
        }

        $sorter->sort($info, BookStudentInformationStrategy::class);

        return $this->render('books/extra/student_information/index.html.twig', [
            'info' => $info,
            'gradeFilter' => $gradeFilterView,
            'studentFilter' => $studentFilterView,
            'sectionFilter' => $sectionFilterView
        ]);
    }

    #[Route('/add', name: 'add_student_book_information')]
    public function add(Request $request, SectionResolverInterface $sectionResolver): Response {
        $info = new BookStudentInformation();
        $form = $this->createForm(BookStudentInformationType::class, $info);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($info);
            $this->addFlash('success', 'book.extra.student_info.add.success');

            return $this->redirectToRoute('book_student_information_index', [
               'grade' => $info->getStudent()->getGrade($sectionResolver->getCurrentSection())?->getUuid()
            ]);
        }

        return $this->render('books/extra/student_information/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_student_book_information')]
    public function edit(BookStudentInformation $info, Request $request, SectionResolverInterface $sectionResolver): Response {
        $form = $this->createForm(BookStudentInformationType::class, $info);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($info);
            $this->addFlash('success', 'book.extra.student_info.edit.success');

            return $this->redirectToRoute('book_student_information_index', [
                'grade' => $info->getStudent()->getGrade($sectionResolver->getCurrentSection())?->getUuid()
            ]);
        }

        return $this->render('books/extra/student_information/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_student_book_information')]
    public function remove(BookStudentInformation $information, Request $request, StudentStringConverter $stringConverter, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.extra.student_info.remove.confirm',
            'message_parameters' => [
                '%student%' => $stringConverter->convert($information->getStudent()),
                '%from%' => $information->getFrom()->format($translator->trans('date.format')),
                '%until%' => $information->getUntil()->format($translator->trans('date.format'))
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($information);
            $this->addFlash('success', 'book.extra.student_info.remove.success');

            return $this->redirectToRoute('book_student_information_index', [
                'student' => $information->getStudent()->getUuid()
            ]);
        }

        return $this->render('books/extra/student_information/remove.html.twig', [
            'form' => $form->createView(),
            'info' => $information
        ]);
    }
}