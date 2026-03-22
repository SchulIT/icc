<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\Student;
use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Feature\IsFeatureEnabled;
use App\Repository\ReturnItemRepositoryInterface;
use App\Security\Voter\StudentVoter;
use App\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::ReturnItem)]
class ReturnItemsDetailsAction extends AbstractController {
    #[Route('/student/{uuid}/returns', name: 'student_return_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        ReturnItemRepositoryInterface $returnItemRepository,
        FeatureManager $featureManager,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted('ROLE_RETURN_ITEM_CREATOR');

        $sectionFilterView = $sectionFilter->handle($section);

        $returnItems = $returnItemRepository->findByStudent($student, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());

        return $this->render('student/return_items.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'returnItems' => $returnItems
        ]);
    }
}
