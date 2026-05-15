<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Common\Entity\User;
use App\Framework\Repository\PaginationQuery;
use App\Common\Repository\StudentRepositoryInterface;
use App\Common\Voter\StudentVoter;
use App\Common\View\Filter\GradeFilter;
use App\Common\View\Filter\SectionFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class IndexAction extends AbstractController {

    #[Route('/student', name: 'students')]
    #[IsGranted(StudentVoter::ShowAny)]
    public function __invoke(
        #[CurrentUser] User $user,
        StudentRepositoryInterface $studentRepository,
        SectionFilter $sectionFilter,
        GradeFilter $gradeFilter,
        Request $request,
        #[MapQueryParameter] int|null $page = 1,
        #[MapQueryParameter] int|null $limit = 25,
        #[MapQueryParameter(name: 'q', filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $query = null
    ): Response {
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $gradeFilterView = $gradeFilter->handle($request->query->get('grade'), $sectionFilterView->getCurrentSection(), $user);

        $students = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $students = $studentRepository->findAllPaginated(
                new PaginationQuery(page: $page, limit: $limit),
                $sectionFilterView->getCurrentSection(),
                $query,
                $gradeFilterView->getCurrentGrade()
            );
        }

        return $this->render('student/index.html.twig', [
            'students' => $students,
            'query' => $query,
            'sectionFilter' => $sectionFilterView,
            'gradeFilter' => $gradeFilterView,
        ]);
    }
}
