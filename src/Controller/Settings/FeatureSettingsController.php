<?php

namespace App\Controller\Settings;

use App\Feature\Feature;
use App\Settings\FeatureSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/settings')]
class FeatureSettingsController extends AbstractController {

    #[Route('/features', name: 'admin_settings_features')]
    public function features(Request $request, FeatureSettings $featureSettings): RedirectResponse|Response {
        $builder = $this->createFormBuilder();

        foreach(Feature::cases() as $feature) {
            $builder
                ->add(sprintf('feature_%s', $feature->value), CheckboxType::class, [
                    'required' => false,
                    'label' => sprintf('admin.settings.features.%s.label', $feature->value),
                    'help' => sprintf('admin.settings.features.%s.help', $feature->value),
                    'data' => $featureSettings->isFeatureEnabled($feature)
                ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach(Feature::cases() as $feature) {
                $featureSettings->setFeatureEnabled($feature, $form->get(sprintf('feature_%s', $feature->value))->getData());
            }

            $this->addFlash('success', 'admin.settings.features.success');
            return $this->redirectToRoute('admin_settings_features');
        }

        return $this->render('admin/settings/features.html.twig', [
            'form' => $form->createView()
        ]);
    }
}