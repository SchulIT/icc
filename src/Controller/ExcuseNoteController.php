<?php

namespace App\Controller;

use App\Entity\DateLesson;
use App\Settings\TimetableSettings;
use ArrayIterator;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\ExcuseNote;
use App\Entity\User;
use App\Form\ExcuseType;
use App\Repository\ExcuseNoteRepositoryInterface;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use SchulIT\CommonBundle\Form\ConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/book/excuses')]
class ExcuseNoteController extends AbstractController {

    private const ItemsPerPage = 25;

    public function __construct(private ExcuseNoteRepositoryInterface $repository)
    {
    }

    #[Route(path: '', name: 'excuse_notes')]
    public function index(SectionFilter $sectionFilter, StudentFilter $studentFilter, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user);

        $page = $request->query->getInt('page', 1);
        $count = 0;
        $pages = 0;
        $notes = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $paginator = $this->repository->getPaginator(
                self::ItemsPerPage,
                $page,
                $studentFilterView->getCurrentStudent(),
                $sectionFilterView->getCurrentSection()->getStart(),
                $sectionFilterView->getCurrentSection()->getEnd()
            );

            /** @var ArrayIterator $iterator */
            $iterator = $paginator->getIterator();
            $notes = $iterator->getArrayCopy();
            $count = $paginator->count();
            $pages = ceil((float)$count / self::ItemsPerPage);
        }

        return $this->render('books/excuse_note/index.html.twig', [
            'studentFilter' => $studentFilterView,
            'sectionFilter' => $sectionFilterView,
            'page' => $page,
            'pages' => $pages,
            'notes' => $notes
        ]);
    }

    #[Route(path: '/add', name: 'add_excuse')]
    public function add(Request $request, TimetableSettings $timetableSettings): Response {
        /** @var User $user */
        $user = $this->getUser();

        $excuse = new ExcuseNote();
        $excuse->setUntil(new DateLesson());
        $excuse->getUntil()->setLesson($timetableSettings->getMaxLessons());

        if($user->getTeacher() !== null) {
            $excuse->setExcusedBy($user->getTeacher());
        }

        $form = $this->createForm(ExcuseType::class, $excuse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($excuse);

            $this->addFlash('success', 'book.excuse_note.add.success');
            return $this->redirectToRoute('excuse_notes');
        }

        return $this->render('books/excuse_note/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_excuse')]
    public function edit(ExcuseNote $excuse, Request $request): Response {
        $form = $this->createForm(ExcuseType::class, $excuse);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($excuse);

            $this->addFlash('success', 'book.excuse_note.edit.success');
            return $this->redirectToRoute('excuse_notes');
        }

        return $this->render('books/excuse_note/edit.html.twig', [
            'form' => $form->createView(),
            'note' => $excuse
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_excuse')]
    public function remove(ExcuseNote $excuse, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.excuse_note.remove.confirm',
            'message_parameters' => [
                '%firstname%' => $excuse->getStudent()->getFirstname(),
                '%lastname%' => $excuse->getStudent()->getLastname(),
                '%start_date%' => $excuse->getFrom()->getDate()->format($translator->trans('date.format')),
                '%start_lesson%' => $excuse->getFrom()->getLesson(),
                '%end_date%' => $excuse->getUntil()->getDate()->format($translator->trans('date.format')),
                '%end_lesson%' => $excuse->getUntil()->getLesson()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($excuse);
            $this->addFlash('success', 'book.excuse_note.remove.success');

            return $this->redirectToRoute('excuse_notes');
        }

        return $this->render('books/excuse_note/remove.html.twig', [
            'form' => $form->createView(),
            'note' => $excuse
        ]);
    }
}