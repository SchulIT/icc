<?php

namespace App\Consent\Controller;

use App\Framework\Controller\AbstractController;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;
use App\Common\Entity\User;
use App\Framework\Feature\Feature;
use App\Framework\Feature\FeatureManager;
use App\Framework\Feature\IsFeatureEnabled;
use App\LearningManagementSystem\Repository\LearningManagementSystemRepositoryInterface;
use App\Privacy\Repository\PrivacyCategoryRepositoryInterface;
use App\LearningManagementSystem\Repository\StudentLearningManagementInformationRepositoryInterface;
use App\Common\Section\SectionResolverInterface;
use App\Consent\Settings\ConsentsSettings;
use App\Framework\Utils\ArrayUtils;
use App\Common\View\Filter\StudentFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[IsFeatureEnabled(Feature::Consents)]
class StudentConsentsController extends AbstractController {

    #[Route('/consents', name: 'student_consents')]
    public function show(#[CurrentUser] User $user, StudentFilter $studentFilter, SectionResolverInterface $sectionResolver, Request $request,
                         ConsentsSettings $consentsSettings,
                         PrivacyCategoryRepositoryInterface $privacyCategoryRepository, LearningManagementSystemRepositoryInterface $lmsRepository,
                         StudentLearningManagementInformationRepositoryInterface $studentLearningManagementInformationRepository, FeatureManager $featureManager): Response {
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionResolver->getCurrentSection(), $user);

        $privacyCategories = [ ];
        $lms = [ ];
        $lmsConsents = [ ];

        if($consentsSettings->showPrivacyConsents() && $featureManager->isFeatureEnabled(Feature::Privacy) && $studentFilterView->getCurrentStudent() !== null) {
            $privacyCategories = $privacyCategoryRepository->findAll();
        }

        if($consentsSettings->showLmsConsents() && $featureManager->isFeatureEnabled(Feature::LMS) && $studentFilterView->getCurrentStudent() !== null) {
            $lms = $lmsRepository->findAll();
            $lmsConsents = ArrayUtils::createArrayWithKeys(
                $studentLearningManagementInformationRepository->findByStudent($studentFilterView->getCurrentStudent()),
                fn(StudentLearningManagementSystemInformation $info) => $info->getLms()->getId()
            );
        }

        return $this->render('consents/index.html.twig', [
            'isPrivacyEnabled' => $featureManager->isFeatureEnabled(Feature::Privacy),
            'isLmsEnabled' => $featureManager->isFeatureEnabled(Feature::LMS),
            'privacyCategories' => $privacyCategories,
            'learningManagementSystems' => $lms,
            'lmsConsents' => $lmsConsents,
            'studentFilter' => $studentFilterView,
            'student' => $studentFilterView->getCurrentStudent(),
            'info' => $consentsSettings->getInfoText()
        ]);
    }
}
