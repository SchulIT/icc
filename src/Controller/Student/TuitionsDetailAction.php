<?php

declare(strict_types=1);

namespace App\Controller\Student;

use App\Entity\Student;
use App\Repository\TuitionRepositoryInterface;
use App\Security\Voter\ListsVoter;
use App\Security\Voter\StudentVoter;
use App\Sorting\Sorter;
use App\Sorting\TuitionStrategy;
use App\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class TuitionsDetailAction extends AbstractController {
    #[Route('/student/{uuid}/tuitions', name: 'student_tuitions_details')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        TuitionRepositoryInterface $tuitionRepository,
        Sorter $sorter,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $this->denyAccessUnlessGranted(ListsVoter::Tuitions);

        $sectionFilterView = $sectionFilter->handle($section);

        $tuitions = $tuitionRepository->findAllByStudents([$student], $sectionFilterView->getCurrentSection());
        $sorter->sort($tuitions, TuitionStrategy::class);

        return $this->render('student/tuitions.html.twig', [
            'student' => $student,
            'tuitions' => $tuitions,
            'sectionFilter' => $sectionFilterView
        ]);
    }
}
