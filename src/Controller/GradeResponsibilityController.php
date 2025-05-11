<?php

namespace App\Controller;

use App\Entity\GradeResponsibility;
use App\Form\GradeResponsibilityType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\GradeResponsibilityRepositoryInterface;
use App\Repository\SectionRepositoryInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book/responsibility')]
class GradeResponsibilityController extends AbstractController {

    public function __construct(private readonly GradeResponsibilityRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('/add', name: 'add_grade_responsibility')]
    public function add(Request $request, GradeRepositoryInterface $gradeRepository, SectionRepositoryInterface $sectionRepository): Response {
        $grade = $gradeRepository->findOneByUuid($request->query->get('grade'));
        $section = $sectionRepository->findOneByUuid($request->query->get('section'));

        $responsibility = new GradeResponsibility();
        $responsibility->setGrade($grade);
        $responsibility->setSection($section);

        $form = $this->createForm(GradeResponsibilityType::class, $responsibility);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($responsibility);
            $this->addFlash('success', 'book.responsibilities.add.success');

            return $this->redirectToRoute('book', [
                'section' => $responsibility->getSection()->getUuid(),
                'grade' => $responsibility->getGrade()->getUuid()
            ]);
        }

        return $this->render('books/responsibility/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_grade_responsibility')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] GradeResponsibility $responsibility, Request $request): Response {
        $form = $this->createForm(GradeResponsibilityType::class, $responsibility);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($responsibility);
            $this->addFlash('success', 'book.responsibilities.edit.success');

            return $this->redirectToRoute('book', [
                'section' => $responsibility->getSection()->getUuid(),
                'grade' => $responsibility->getGrade()->getUuid()
            ]);
        }

        return $this->render('books/responsibility/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_grade_responsibility')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] GradeResponsibility $responsibility, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.responsibilities.remove.confirm',
            'message_parameters' => [
                '%grade%' => $responsibility->getGrade()->getName(),
                '%task%' => $responsibility->getTask(),
                '%person%' => $responsibility->getPerson()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($responsibility);
            $this->addFlash('success', 'book.responsibilities.remove.success');

            return $this->redirectToRoute('book', [
                'section' => $responsibility->getSection()->getUuid(),
                'grade' => $responsibility->getGrade()->getUuid()
            ]);
        }

        return $this->render('books/responsibility/remove.html.twig', [
            'form' => $form->createView()
        ]);
    }
}