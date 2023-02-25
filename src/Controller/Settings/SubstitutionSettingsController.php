<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\UserType;
use App\Settings\SubstitutionSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\Type;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class SubstitutionSettingsController extends AbstractController {
    #[Route(path: '/substitutions', name: 'admin_settings_substitutions')]
    public function substitutions(Request $request, SubstitutionSettings $substitutionSettings, EnumStringConverter $enumStringConverter): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('ahead_days', IntegerType::class, [
                'label' => 'admin.settings.substitutions.number_of_ahead_substitutions.label',
                'help' => 'admin.settings.substitutions.number_of_ahead_substitutions.help',
                'constraints' => [
                    new Type(['type' => 'integer']),
                    new GreaterThanOrEqual(['value' => 0])
                ],
                'data' => $substitutionSettings->getNumberOfAheadDaysForSubstitutions()
            ])
            ->add('skip_weekends', CheckboxType::class, [
                'label' => 'admin.settings.substitutions.skip_weekends.label',
                'help' => 'admin.settings.substitutions.skip_weekends.help',
                'required' => false,
                'data' => $substitutionSettings->skipWeekends(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('absence_visibility', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'label.absence_visibility',
                'data' => $substitutionSettings->getAbsenceVisibility(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('notifications_enabled', CheckboxType::class, [
                'label' => 'admin.settings.substitutions.notifications.enabled.label',
                'help' => 'admin.settings.substitutions.notifications.enabled.help',
                'required' => false,
                'data' => $substitutionSettings->isNotificationsEnabled(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('notifications_sender', TextType::class, [
                'label' => 'admin.settings.substitutions.notifications.sender.label',
                'help' => 'admin.settings.substitutions.notifications.sender.help',
                'required' => false,
                'data' => $substitutionSettings->getNotificationSender()
            ])
            ->add('notifications_replyaddress', EmailType::class, [
                'label' => 'admin.settings.substitutions.notifications.reply_address.label',
                'help' => 'admin.settings.substitutions.notifications.reply_address.help',
                'required' => false,
                'data' => $substitutionSettings->getNotificationReplyToAddress()
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'ahead_days' => function(int $days) use ($substitutionSettings) {
                    $substitutionSettings->setNumberOfAheadDaysForSubstitutions($days);
                },
                'skip_weekends' => function(bool $skipWeekends) use ($substitutionSettings) {
                    $substitutionSettings->setSkipWeekends($skipWeekends);
                },
                'absence_visibility' => function(array $visibility) use ($substitutionSettings) {
                    $substitutionSettings->setAbsenceVisibility($visibility);
                },
                'notifications_enabled' => function(bool $enabled) use ($substitutionSettings) {
                    $substitutionSettings->setNotificationsEnabled($enabled);
                },
                'notifications_sender' => function(?string $sender) use($substitutionSettings) {
                    $substitutionSettings->setNotificationSender($sender);
                },
                'notifications_replyaddress' => function(?string $address) use($substitutionSettings) {
                    $substitutionSettings->setNotificationReplyToAddress($address);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_substitutions');
        }

        return $this->render('admin/settings/substitutions.html.twig', [
            'form' => $form->createView()
        ]);
    }
}