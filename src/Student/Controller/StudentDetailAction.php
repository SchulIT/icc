<?php

declare(strict_types=1);

namespace App\Student\Controller;

use App\Common\Entity\Student;
use App\Common\Voter\StudentVoter;
use App\Common\View\Filter\SectionFilter;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class StudentDetailAction extends AbstractController {
    #[Route('/student/{uuid}', name: 'show_student')]
    public function __invoke(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] Student $student,
        SectionFilter $sectionFilter,
        #[MapQueryParameter(filter: FILTER_DEFAULT, flags: FILTER_FLAG_EMPTY_STRING_NULL | FILTER_NULL_ON_FAILURE)] string|null $section = null,
    ): Response {
        $this->denyAccessUnlessGranted(StudentVoter::Show, $student);
        $sectionFilterView = $sectionFilter->handle($section);

        return $this->render('student/detail.html.twig', [
            'student' => $student,
            'sectionFilter' => $sectionFilterView
        ]);
    }
}
