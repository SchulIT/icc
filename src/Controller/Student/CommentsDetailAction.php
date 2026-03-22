<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\Student;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Repository\BookCommentRepositoryInterface;
use App\Security\Voter\StudentVoter;
use App\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Book)]
class CommentsDetailAction extends AbstractController {
    #[Route('/student/{uuid}/comments', name: 'student_comments_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        BookCommentRepositoryInterface $commentRepository,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted('ROLE_BOOK_VIEWER');

        $sectionFilterView = $sectionFilter->handle($section);
        $comments = $commentRepository->findAllByDateAndStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());

        return $this->render('student/comments.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'comments' => $comments
        ]);
    }
}
