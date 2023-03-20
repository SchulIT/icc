<?php

namespace App\Controller;

use App\Book\Grade\GradeOverviewHelper;
use App\Entity\Section;
use App\Entity\StudyGroupMembership;
use App\Entity\Tuition;
use App\Entity\User;
use App\Repository\TuitionRepositoryInterface;
use App\View\Filter\SectionFilter;
use App\View\Filter\StudentFilter;
use App\View\Filter\TuitionFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book/gradebook')]
class TuitionGradebookController extends AbstractController {

    #[Route('', name: 'gradebook')]
    public function index(Request $request, TuitionFilter $tuitionFilter, StudentFilter $studentFilter,
                          SectionFilter $sectionFilter, TuitionRepositoryInterface $tuitionRepository,
                          GradeOverviewHelper $gradeOverviewHelper): Response {
        /** @var User $user */
        $user = $this->getUser();
        
        $sectionFilterView = $sectionFilter->handle($request->query->get('section'));
        $tuitionFilterView = $tuitionFilter->handle($request->query->get('tuition'), $sectionFilterView->getCurrentSection(), $user);
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionFilterView->getCurrentSection(), $user, false);
        
        $ownTuitions = $this->resolveOwnTuitions($sectionFilterView->getCurrentSection(), $user, $tuitionRepository);

        $overview = null;
        $template = 'books/grades/tuition.html.twig';

        if($tuitionFilterView->getCurrentTuition() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForTuition($tuitionFilterView->getCurrentTuition());
        } else if($studentFilterView->getCurrentStudent() !== null) {
            $template = 'books/grades/student.html.twig';
            $overview = $gradeOverviewHelper->computeOverviewForStudent($studentFilterView->getCurrentStudent(), $sectionFilterView->getCurrentSection());
        }

        return $this->render($template, [
            'sectionFilter' => $sectionFilterView,
            'tuitionFilter' => $tuitionFilterView,
            'studentFilter' => $studentFilterView,
            'ownTuitions' => $ownTuitions,
            'overview' => $overview
        ]);
    }

    /**
     * @return Tuition[]
     */
    private function resolveOwnTuitions(?Section $currentSection, User $user, TuitionRepositoryInterface $tuitionRepository): array {
        if($currentSection === null) {
            return [ ];
        }

        $tuitions = [ ];

        if ($user->isStudentOrParent()) {
            $tuitions = $tuitionRepository->findAllByStudents($user->getStudents()->toArray(), $currentSection);
        } else if ($user->isTeacher()) {
            $tuitions = $tuitionRepository->findAllByTeacher($user->getTeacher(), $currentSection);
        }

        return array_filter($tuitions, fn(Tuition $tuition) => $tuition->getGradeCategories()->count() > 0);
    }
}