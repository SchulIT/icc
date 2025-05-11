<?php

namespace App\Controller\Tools;

use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Section\SectionResolverInterface;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Tools\MissingUsersReportHelper;
use App\View\Filter\StudyGroupFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools/missing_users', name: 'missing_users_tool')]
class MissingUsersAction extends AbstractController {
    public function __construct(private readonly StudyGroupFilter $studyGroupFilter,
                                private readonly SectionResolverInterface $sectionResolver,
                                private readonly MissingUsersReportHelper $missingUsersReportHelper,
                                private readonly Sorter $sorter) { }

    public function __invoke(Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $studyGroupFilterView = $this->studyGroupFilter->handle($request->query->get('study_group'), $this->sectionResolver->getCurrentSection(), $user);

        $missingStudents = [ ];
        $missingParents = [ ];

        if($studyGroupFilterView->getCurrentStudyGroup() !== null) {
            $students = $studyGroupFilterView->getCurrentStudyGroup()->getMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudent())->toArray();
            $this->sorter->sort($students, StudentStrategy::class);
            $missingStudents = $this->missingUsersReportHelper->getMissingStudents($students);
            $missingParents = $this->missingUsersReportHelper->getMissingParents($students);
        }

        return $this->render('admin/tools/missing_users.html.twig', [
            'studyGroupFilter' => $studyGroupFilterView,
            'missingStudents' => $missingStudents,
            'missingParents' => $missingParents
        ]);
    }
}