<?php

namespace App\Student\Controller;

use App\Framework\Controller\AbstractController;
use App\Checklist\Entity\Checklist;
use App\Checklist\Entity\ChecklistStudent;
use App\Common\Entity\Student;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Checklist\Repository\ChecklistStudentRepositoryInterface;
use App\Checklist\Voter\ChecklistVoter;
use App\Common\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Checklists)]
class ChecklistDetailAction extends AbstractController {

    #[Route('/student/{uuid}/checklists', name: 'student_checklists_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        ChecklistStudentRepositoryInterface $checklistStudentRepository,

        SectionFilter $sectionFilter,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $checklists = $checklistStudentRepository->findAllByStudent($student);
        $checklists = array_filter(
            $checklists,
            fn(ChecklistStudent $checklistStudent): bool => $this->isGranted(ChecklistVoter::View, $checklistStudent->getChecklist())
        );

        $sectionFilterView = $sectionFilter->handle($section);

        return $this->render('student/checklists.html.twig', [
            'student' => $student,
            'checklists' => $checklists,
            'sectionFilter' => $sectionFilterView,
        ]);
    }
}