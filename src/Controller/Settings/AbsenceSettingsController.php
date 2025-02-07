<?php

namespace App\Controller\Settings;

use App\Feature\Feature;
use App\Feature\FeatureManager;
use App\Form\EmailCollectionEntryType;
use App\Form\MarkdownType;
use App\Settings\StudentAbsenceSettings;
use App\Settings\TeacherAbsenceSettings;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class AbsenceSettingsController extends AbstractController {
    #[Route(path: '/absences', name: 'admin_settings_absences')]
    public function absences(Request $request, StudentAbsenceSettings $studentAbsenceSettings, TeacherAbsenceSettings $teacherAbsenceSettings, FeatureManager $featureManager): Response {
        $builder = $this->createFormBuilder();

        if($featureManager->isFeatureEnabled(Feature::StudentAbsence)) {
            $builder
                ->add('introduction_text', MarkdownType::class, [
                    'required' => false,
                    'data' => $studentAbsenceSettings->getIntroductionText(),
                    'label' => 'admin.settings.student_absences.introduction_text.label',
                    'help' => 'admin.settings.student_absences.introduction_text.help'
                ])
                ->add('privacy_url', TextType::class, [
                    'required' => true,
                    'data' => $studentAbsenceSettings->getPrivacyUrl(),
                    'label' => 'admin.settings.student_absences.privacy_url.label',
                    'help' => 'admin.settings.student_absences.privacy_url.help'
                ])
                ->add('retention_days', IntegerType::class, [
                    'required' => true,
                    'data' => $studentAbsenceSettings->getRetentionDays(),
                    'label' => 'admin.settings.student_absences.retention_days.label',
                    'help' => 'admin.settings.student_absences.retention_days.help',
                    'constraints' => [
                        new GreaterThanOrEqual(0)
                    ]
                ])
                ->add('next_day_threshold', TimeType::class, [
                    'label' => 'admin.settings.dashboard.next_day_threshold.label',
                    'help' => 'admin.settings.dashboard.next_day_threshold.help',
                    'data' => $studentAbsenceSettings->getNextDayThresholdTime(),
                    'required' => false,
                    'input' => 'string',
                    'input_format' => 'H:i',
                    'widget' => 'single_text'
                ]);
        }

        if($featureManager->isFeatureEnabled(Feature::TeacherAbsence)) {
            $builder
                ->add('teacher_create_recipients', CollectionType::class, [
                    'entry_type' => EmailCollectionEntryType::class,
                    'entry_options' => [
                        'constraints' => [new Email()]
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'admin.settings.teacher_absences.recipients.create.label',
                    'help' => 'admin.settings.teacher_absences.recipients.create.help',
                    'required' => false,
                    'data' => $teacherAbsenceSettings->getOnCreateRecipients(),
                    'by_reference' => false
                ])
                ->add('teacher_update_recipients', CollectionType::class, [
                    'entry_type' => EmailCollectionEntryType::class,
                    'entry_options' => [
                        'constraints' => [new Email()]
                    ],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'label' => 'admin.settings.teacher_absences.recipients.update.label',
                    'help' => 'admin.settings.teacher_absences.recipients.update.help',
                    'required' => false,
                    'data' => $teacherAbsenceSettings->getOnUpdateRecipients(),
                    'by_reference' => false
                ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'privacy_url' => function($url) use ($studentAbsenceSettings) {
                    $studentAbsenceSettings->setPrivacyUrl($url);
                },
                'retention_days' => function($days) use ($studentAbsenceSettings) {
                    $studentAbsenceSettings->setRetentionDays($days);
                },
                'introduction_text' => function($text) use ($studentAbsenceSettings) {
                    $studentAbsenceSettings->setIntroductionText($text);
                },
                'next_day_threshold' => function($threshold) use ($studentAbsenceSettings) {
                    $studentAbsenceSettings->setNextDayThresholdTime($threshold);
                },
                'teacher_create_recipients' => function(array $recipients) use ($teacherAbsenceSettings) {
                    $teacherAbsenceSettings->setOnCreateRecipients($recipients);
                },
                'teacher_update_recipients' => function(array $recipients) use ($teacherAbsenceSettings) {
                    $teacherAbsenceSettings->setOnUpdateRecipients($recipients);
                }
            ];

            foreach($map as $formKey => $callable) {
                if($form->has($formKey)) {
                    $value = $form->get($formKey)->getData();
                    $callable($value);
                }
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_absences');
        }

        return $this->render('admin/settings/absences.html.twig', [
            'form' => $form->createView()
        ]);
    }
}