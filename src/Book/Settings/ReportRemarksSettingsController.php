<?php

namespace App\Book\Settings;

use App\Common\Form\Type\MarkdownType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class ReportRemarksSettingsController extends AbstractController {

    #[Route(path: '/report_remarks', name: 'admin_settings_report_remarks')]
    public function settings(
        Request $request,
        ReportRemarksSettings $settings
    ): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('info', MarkdownType::class, [
                'label' => 'admin.settings.report_remarks.info.label',
                'help' => 'admin.settings.report_remarks.info.help',
                'required' => false,
                'data' => $settings->getInformationText(),
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settings->setInformationText($form->get('info')->getData());

            $this->addFlash('success', 'admin.settings.success');
            return $this->redirectToRoute('admin_settings_report_remarks');
        }

        return $this->render('admin/settings/report_remarks.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
