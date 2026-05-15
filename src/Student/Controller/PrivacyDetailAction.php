<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Common\Entity\Student;
use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use App\Framework\Feature\IsFeatureEnabled;
use App\Privacy\Repository\PrivacyCategoryRepositoryInterface;
use App\Common\Voter\ListsVoter;
use App\Common\Voter\StudentVoter;
use App\Common\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Privacy)]
class PrivacyDetailAction extends AbstractController {
    #[Route('/student/{uuid}/privacy', name: 'student_privacy_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        PrivacyCategoryRepositoryInterface $privacyCategoryRepository,
        FeatureManager $featureManager,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted(ListsVoter::Privacy);
        $sectionFilterView = $sectionFilter->handle($section);

        $privacyCategories = $privacyCategoryRepository->findAll();

        return $this->render('student/privacy.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'privacyCategories' => $privacyCategories
        ]);
    }
}
