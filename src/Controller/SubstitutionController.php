<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\User;
use App\Repository\SubstitutionRepositoryInterface;
use App\View\Filter\GradeFilter;
use App\View\Filter\GradeFilterView;
use App\View\Filter\StudentFilter;
use App\View\Filter\StudentFilterView;
use App\View\Filter\TeacherFilter;
use Symfony\Component\Routing\Annotation\Route;

class SubstitutionController extends AbstractControllerWithMessages {


    /**
     * @Route("/substitutions", name="substitutions")
     */
    public function index(SubstitutionRepositoryInterface $substitutionRepository, StudentFilter $studentFilter,
                          GradeFilter $gradeFilter, TeacherFilter $teacherFilter,
                          ?int $studentId = null, ?int $gradeId = null, ?string $teacherAcronym = null) {
        /** @var User $user */
        $user = $this->getUser();

        $studentFilterView = $studentFilter->handle($studentId, $user);
        $gradeFilterView = $gradeFilter->handle($gradeId, $user);
        $teacherFilterView = $teacherFilter->handle($teacherAcronym, $user);

        /*
         * TODO: Grouping -> grades vs teachers
         */

        return $this->renderWithMessages('substitutions/index.html.twig', [

        ]);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Substitutions();
    }
}