<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Book\Student\StudentStatisticsCounterResolver;
use App\Common\Entity\Student;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Common\Repository\StudentInformationRepositoryInterface;
use App\Common\Voter\StudentVoter;
use App\Common\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[IsFeatureEnabled(Feature::Book)]
class ExtraDetailAction extends AbstractController {
    #[Route('/student/{uuid}/extra', name: 'student_extra_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        StudentInformationRepositoryInterface $studentInformationRepository,
        StudentStatisticsCounterResolver $studentStatisticsCounterResolver,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted('ROLE_BOOK_VIEWER');

        $sectionFilterView = $sectionFilter->handle($section);
        $extra = $studentInformationRepository->findByStudents([$student], null, $sectionFilterView->getCurrentSection()->getStart(), $sectionFilterView->getCurrentSection()->getEnd());


        return $this->render('student/extra.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView,
            'extra' => $extra
        ]);
    }
}
