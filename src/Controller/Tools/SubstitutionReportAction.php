<?php

namespace App\Controller\Tools;

use App\Tools\SubstitutionEvaluation\ReportInput;
use App\Tools\SubstitutionEvaluation\ReportInputType;
use App\Tools\SubstitutionEvaluation\ReportManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools/substitution_report', name: 'substitution_report_tool')]
class SubstitutionReportAction extends AbstractController {

    public function __construct(private readonly ReportManager $manager) { }

    public function __invoke(Request $request): Response {
        $input = new ReportInput();
        $form = $this->createForm(ReportInputType::class, $input);
        $form->handleRequest($request);

        $result = [ ];

        if($form->isSubmitted() && $form->isValid()) {
            $result = $this->manager->evaluate($input);
        }

        return $this->render('admin/tools/substitution_report.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);

    }
}