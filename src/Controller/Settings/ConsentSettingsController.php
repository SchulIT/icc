<?php

namespace App\Controller\Settings;

use App\Controller\AbstractController;
use App\Form\MarkdownType;
use App\Settings\ConsentsSettings;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/settings/consents')]
class ConsentSettingsController extends AbstractController {
    #[Route('', name: 'admin_consents')]
    public function consents(Request $request, ConsentsSettings $consentsSettings): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('show_privacy', CheckboxType::class, [
                'required' => false,
                'data' => $consentsSettings->showPrivacyConsents(),
                'label' => 'admin.settings.consents.show_privacy.label',
                'help' => 'admin.settings.consents.show_privacy.help',
            ])
            ->add('show_lms', CheckboxType::class, [
                'required' => false,
                'data' => $consentsSettings->showLmsConsents(),
                'label' => 'admin.settings.consents.show_lms.label',
                'help' => 'admin.settings.consents.show_lms.help'
            ])
            ->add('text', MarkdownType::class, [
                'required' => false,
                'label' => 'admin.settings.consents.text.label',
                'help' => 'admin.settings.consents.text.help',
                'data' => $consentsSettings->getInfoText()
            ]);
        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'show_privacy' => function(bool $showPrivacy) use ($consentsSettings) {
                    $consentsSettings->setShowPrivacyConsents($showPrivacy);
                },
                'show_lms' => function(bool $showLms) use ($consentsSettings) {
                    $consentsSettings->setShowLmsConsents($showLms);
                },
                'text' => function(string|null $text) use ($consentsSettings) {
                    $consentsSettings->setInfoText($text);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');
            return $this->redirectToRoute('admin_consents');
        }

        return $this->render('admin/settings/consents.html.twig', [
            'form' => $form->createView()
        ]);
    }
}