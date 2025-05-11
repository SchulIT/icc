<?php

namespace App\Controller\Tools;

use App\Form\TuitionReportInputType;
use App\Tools\TuitionReport;
use App\Tools\TuitionReportInput;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/tools/tuition_report', name: 'tuition_report_tool')]
class TuitionReportAction extends AbstractController {
    public function __construct(private readonly TuitionReport $tuitionReport) { }

    public function __invoke(Request $request): Response {
        $input = new TuitionReportInput();
        $form = $this->createForm(TuitionReportInputType::class, $input);
        $form->handleRequest($request);

        $result = null;

        if($form->isSubmitted() && $form->isValid()) {
            if($request->request->get('export') === 'csv') {
                return $this->tuitionReport->generateReportAsCsvResponse($input->section, $input->types);
            }

            $result = $this->tuitionReport->generateReport($input->section, $input->types);
        }

        return $this->render('admin/tools/tuition_report.html.twig', [
            'form' => $form->createView(),
            'result' => $result
        ]);
    }
}