<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Book\Grade\Category;
use App\Book\Grade\GradeOverviewHelper;
use App\Book\Settings\TuitionGradebookSettings;
use App\Common\Entity\Student;
use App\Common\View\Filter\SectionFilter;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Framework\Utils\ArrayUtils;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Book)]
#[IsFeatureEnabled(Feature::GradeBook)]
class GradeDetailsActionController extends AbstractController {

    #[Route('/student/{uuid}/grades', name: 'student_grade_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        GradeOverviewHelper $gradeOverviewHelper,
        TuitionGradebookSettings $gradebookSettings,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted('ROLE_BOOK_VIEWER');

        $sectionFilterView = $sectionFilter->handle($section);

        $overview = null;
        $categories = [ ];

        if($sectionFilterView->getCurrentSection() !== null) {
            $overview = $gradeOverviewHelper->computeOverviewForStudent($student, $sectionFilterView->getCurrentSection());
            $categories = ArrayUtils::createArrayWithKeys(
                $overview->getCategories(),
                fn(Category $category) => $category->getCategory()->getId()
            );
        }

        return $this->render('student/grades.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'overview' => $overview,
            'key' => $gradebookSettings->getEncryptedMasterKey(),
            'ttl' => $gradebookSettings->getTtlForSessionStorage(),
            'categories' => $categories,
        ]);
    }
}
