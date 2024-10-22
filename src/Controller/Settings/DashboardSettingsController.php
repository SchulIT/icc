<?php

namespace App\Controller\Settings;

use App\Form\TextCollectionEntryType;
use App\Settings\DashboardSettings;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/admin/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class DashboardSettingsController extends AbstractController {
    #[Route(path: '/dashboard', name: 'admin_settings_dashboard')]
    public function dashboard(Request $request, DashboardSettings $dashboardSettings): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('removable_types', CollectionType::class, [
                'label' => 'admin.settings.dashboard.removable_substitutions.label',
                'help' => 'admin.settings.dashboard.removable_substitutions.help',
                'data' => $dashboardSettings->getRemovableSubstitutionTypes(),
                'required' => false,
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => new NotBlank()
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('additional_types', CollectionType::class, [
                'label' => 'admin.settings.dashboard.additional_substitutions.label',
                'help' => 'admin.settings.dashboard.additional_substitutions.help',
                'data' => $dashboardSettings->getAdditionalSubstitutionTypes(),
                'required' => false,
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => new NotBlank()
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('free_lesson_types', CollectionType::class, [
                'label' => 'admin.settings.dashboard.free_lesson_types.label',
                'help' => 'admin.settings.dashboard.free_lesson_types.help',
                'data' => $dashboardSettings->getFreeLessonSubstitutionTypes(),
                'required' => false,
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => new NotBlank()
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('next_day_threshold', TimeType::class, [
                'label' => 'admin.settings.dashboard.next_day_threshold.label',
                'help' => 'admin.settings.dashboard.next_day_threshold.help',
                'data' => $dashboardSettings->getNextDayThresholdTime(),
                'required' => false,
                'input' => 'string',
                'input_format' => 'H:i',
                'widget' => 'single_text'
            ])
            ->add('skip_weekends', CheckboxType::class, [
                'label' => 'admin.settings.dashboard.skip_weekends.label',
                'help' => 'admin.settings.dashboard.skip_weekends.help',
                'required' => false,
                'data' => $dashboardSettings->skipWeekends(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('past_days', IntegerType::class, [
                'label' => 'admin.settings.dashboard.past_days.label',
                'help' => 'admin.settings.dashboard.past_days.label',
                'required' => true,
                'data' => $dashboardSettings->getNumberPastDays(),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ]
            ])
            ->add('future_days', IntegerType::class, [
                'label' => 'admin.settings.dashboard.future_days.label',
                'help' => 'admin.settings.dashboard.future_days.label',
                'required' => true,
                'data' => $dashboardSettings->getNumberFutureDays(),
                'constraints' => [
                    new GreaterThanOrEqual(0)
                ]
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'removable_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setRemovableSubstitutionTypes($types);
                },
                'additional_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setAdditionalSubstitutionTypes($types);
                },
                'free_lesson_types' => function($types) use ($dashboardSettings) {
                    $dashboardSettings->setFreeLessonSubstitutionTypes($types);
                },
                'next_day_threshold' => function($threshold) use ($dashboardSettings) {
                    $dashboardSettings->setNextDayThresholdTime($threshold);
                },
                'skip_weekends' => function($skipWeekends) use ($dashboardSettings) {
                    $dashboardSettings->setSkipWeekends($skipWeekends);
                },
                'past_days' => function($days) use ($dashboardSettings) {
                    $dashboardSettings->setNumberPastDays($days);
                },
                'future_days' => function($days) use ($dashboardSettings) {
                    $dashboardSettings->setNumberFutureDays($days);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_dashboard');
        }

        return $this->render('admin/settings/dashboard.html.twig', [
            'form' => $form->createView()
        ]);
    }
}