<?php

namespace App\Controller\Tools;

use App\Form\GradeTuitionTeachersIntersectionType;
use App\Tools\GradeTuitionTeachersIntersectionInput;
use App\Tools\GradeTuitionTeachersIntersectionTool;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools/teacher_intersection', name: 'grade_tuition_teachers_intersection')]
class GradeTeachersIntersectionAction extends AbstractController {
    public function __construct(private readonly GradeTuitionTeachersIntersectionTool $tool) { }

    public function __invoke(Request $request): Response {
        $input = new GradeTuitionTeachersIntersectionInput();
        $form = $this->createForm(GradeTuitionTeachersIntersectionType::class, $input);
        $form->handleRequest($request);

        $intersections = null;

        if($form->isSubmitted() && $form->isValid()) {
            $intersections = $this->tool->computeIntersections($input);
        }

        return $this->render('admin/tools/teacher_intersection.html.twig', [
            'form' => $form->createView(),
            'intersections' => $intersections
        ]);
    }
}