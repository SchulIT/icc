<?php

namespace App\Controller;

use App\Entity\BookComment;
use App\Entity\Student;
use App\Entity\User;
use App\Form\BookCommentType;
use App\Repository\BookCommentRepositoryInterface;
use App\Section\SectionResolverInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/book/comment')]
#[IsGranted('ROLE_BOOK_ENTRY_CREATOR')]
class BookCommentController extends AbstractController {

    public function __construct(private BookCommentRepositoryInterface $repository, private SectionResolverInterface $sectionResolver, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    private function redirectToBookOfFirstStudent(BookComment $comment): Response {
        $currentSection = $this->sectionResolver->getCurrentSection();
        /** @var Student|null $firstStudent */
        $firstStudent = $comment->getStudents()->first();

        if($firstStudent !== null && $currentSection !== null) {
            $grade = $firstStudent->getGrade($currentSection);
            return $this->redirectToRoute('book', [
                'grade' => $grade->getUuid()->toString()
            ]);
        }

        return $this->redirectToRoute('book');
    }

    #[Route(path: '/add', name: 'add_book_comment')]
    public function add(Request $request, DateHelper $dateHelper): Response {
        /** @var User $user */
        $user = $this->getUser();

        $comment = (new BookComment())
            ->setDate($dateHelper->getToday());

        if($user->getTeacher() !== null) {
            $comment->setTeacher($user->getTeacher());
        }
        $form = $this->createForm(BookCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'book.comment.add.success');
            $this->repository->persist($comment);

            return $this->redirectToBookOfFirstStudent($comment);
        }

        return $this->render('books/comment/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}', name: 'show_book_comment')]
    public function show(BookComment $comment): Response {
        return $this->render('books/comment/show.html.twig', [
            'comment' => $comment
        ]);
    }

    #[Route(path: '/{uuid}/edit', name: 'edit_book_comment')]
    public function edit(BookComment $comment, Request $request): Response {
        $form = $this->createForm(BookCommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'book.comment.add.success');
            $this->repository->persist($comment);

            return $this->redirectToBookOfFirstStudent($comment);
        }

        return $this->render('books/comment/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_book_student')]
    public function remove(BookComment $comment, Request $request, TranslatorInterface $translator): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.comment.remove.confirm',
            'message_parameters' => [
                '%date%' => $comment->getDate()->format($translator->trans('date.format'))
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($comment);
            $this->addFlash('success', 'book.comment.remove.success');

            return $this->redirectToRoute('book');
        }

        return $this->render('books/comment/remove.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment
        ]);
    }
}