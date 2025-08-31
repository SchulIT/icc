<?php

namespace App\Controller;

use App\Entity\StudentLearningManagementSystemInformation;
use App\Entity\User;
use App\Repository\StudentLearningManagementSystemInformationRepository;
use App\Section\SectionResolverInterface;
use App\Security\Voter\CredentialsVoter;
use App\Sorting\Sorter;
use App\Sorting\StudentLearningManagementSystemInformationStrategy;
use App\View\Filter\StudentFilter;
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