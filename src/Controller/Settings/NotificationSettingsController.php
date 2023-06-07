<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\UserType;
use App\Settings\NotificationSettings;
use App\Utils\ArrayUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/settings')]
#[Security("is_granted('ROLE_ADMIN')")]
class NotificationSettingsController extends AbstractController {
    #[Route(path: '/notifications', name: 'admin_settings_notifications')]
    public function notifications(Request $request, NotificationSettings $notificationSettings, EnumStringConverter $enumStringConverter): Response {
        $builder = $this->createFormBuilder();
        $builder
            ->add('email_enabled', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'admin.settings.notifications.email.label',
                'help' => 'admin.settings.notifications.email.help',
                'data' => $notificationSettings->getEmailEnabledUserTypes(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('pushover_enabled', ChoiceType::class, [
                'choices' => ArrayUtils::createArray(array_map(fn(UserType $case) => $case->name, UserType::cases()), UserType::cases()),
                'choice_label' => fn(UserType $userType) => $enumStringConverter->convert($userType),
                'choice_value' => fn(UserType $userType) => $userType->value,
                'expanded' => true,
                'multiple' => true,
                'label' => 'admin.settings.notifications.pushover.user_types.label',
                'help' => 'admin.settings.notifications.pushover.user_types.help',
                'data' => $notificationSettings->getPushoverEnabledUserTypes(),
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('pushover_token', TextType::class, [
                'label' => 'admin.settings.notifications.pushover.token.label',
                'help' => 'admin.settings.notifications.pushover.token.help',
                'required' => false,
                'mapped' => false,
                'data' => $notificationSettings->getPushoverApiToken()
            ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'email_enabled' => function($types) use ($notificationSettings) {
                    $notificationSettings->setEmailEnabledUserTypes($types);
                },
                'pushover_enabled' => function($types) use($notificationSettings) {
                    $notificationSettings->setPushoverEnabledUserTypes($types);
                },
                'pushover_token' => function($token) use($notificationSettings) {
                    $notificationSettings->setPushoverApiToken($token);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_notifications');
        }

        return $this->render('admin/settings/notifications.html.twig', [
            'form' => $form->createView()
        ]);
    }
}