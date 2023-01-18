<?php

namespace App\Controller;

use App\Form\GradeTuitionTeachersIntersectionType;
use App\Menu\Builder;
use App\Tools\GradeTuitionTeachersIntersectionInput;
use App\Tools\GradeTuitionTeachersIntersectionTool;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/tools')]
class ToolsController extends AbstractController {

    #[Route('', name: 'tools')]
    public function index(Builder $menuBuilder): Response {
        $toolsMenu = $menuBuilder->toolsMenu([]);

        $firstKey = array_key_first($toolsMenu->getChildren());
        $first = $toolsMenu->getChildren()[$firstKey];

        return $this->redirect($first->getUri());
    }

    #[Route('/teacher_intersection', name: 'grade_tuition_teachers_intersection')]
    public function gradeTuitionTeachersIntersection(Request $request, GradeTuitionTeachersIntersectionTool $tool): Response {
        $input = new GradeTuitionTeachersIntersectionInput();
        $form = $this->createForm(GradeTuitionTeachersIntersectionType::class, $input);
        $form->handleRequest($request);

        $intersections = null;

        if($form->isSubmitted() && $form->isValid()) {
            $intersections = $tool->computeIntersections($input);
        }

        return $this->render('admin/tools/teacher_intersection.html.twig', [
            'form' => $form->createView(),
            'intersections' => $intersections
        ]);
    }
}