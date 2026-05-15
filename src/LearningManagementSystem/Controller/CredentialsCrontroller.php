<?php

namespace App\LearningManagementSystem\Controller;

use App\Framework\Controller\AbstractController;
use App\LearningManagementSystem\Entity\StudentLearningManagementSystemInformation;
use App\Common\Entity\User;
use App\LearningManagementSystem\Repository\StudentLearningManagementSystemInformationRepository;
use App\Common\Section\SectionResolverInterface;
use App\LearningManagementSystem\Voter\CredentialsVoter;
use App\Framework\Sorting\Sorter;
use App\LearningManagementSystem\Sorting\StudentLearningManagementSystemInformationStrategy;
use App\Common\View\Filter\StudentFilter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class CredentialsCrontroller extends AbstractController {

    #[Route('/credentials', name: 'credentials')]
    public function index(#[CurrentUser] User $user, Request $request, StudentLearningManagementSystemInformationRepository $repository,
                          StudentFilter $studentFilter, SectionResolverInterface $sectionResolver, Sorter $sorter): Response {
        $studentFilterView = $studentFilter->handle($request->query->get('student'), $sectionResolver->getCurrentSection(), $user, $user->isStudentOrParent());

        $credentials = [ ];

        if($studentFilterView->getCurrentStudent() !== null) {
            $credentials = $repository->findByStudent($studentFilterView->getCurrentStudent());
        }

        $credentials = array_filter(
            $credentials,
            fn(StudentLearningManagementSystemInformation $info) => $this->isGranted(CredentialsVoter::View, $info)
        );

        $sorter->sort($credentials, StudentLearningManagementSystemInformationStrategy::class);


        return $this->render('credentials/index.html.twig', [
            'credentials' => $credentials,
            'studentFilter' => $studentFilterView,
        ]);
    }
}