<?php

namespace App\Controller\Settings;

use App\Converter\EnumStringConverter;
use App\Entity\UserType;
use App\Form\DeliveryOptionCompoundType;
use App\Notification\EventSubscriber\NotifierManager;
use App\Notification\NotificationDeliveryTarget;
use App\Settings\NotificationSettings;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class NotificationSettingsController extends AbstractController {

    public function __construct(private readonly ?string $pushoverToken) {

    }

    #[Route(path: '/notifications', name: 'admin_settings_notifications')]
    public function notifications(Request $request, NotificationSettings $notificationSettings, NotifierManager $notifierManager,
                                  EnumStringConverter $enumStringConverter): Response {
        $builder = $this->createFormBuilder();

        $builder
            ->add('enabled', CheckboxType::class, [
                'label' => 'notifications.enabled.label',
                'help' => 'notifications.enabled.help',
                'required' => false,
                'data' => $notificationSettings->isNotificationsEnabled()
            ])
            ->add('emailEnabled', CheckboxType::class, [
                'label' => 'notifications.email_enabled.label',
                'help' => 'notifications.email_enabled.help',
                'required' => false,
                'data' => $notificationSettings->isEmailEnabled()
            ])
            ->add('pushoverEnabled', CheckboxType::class, [
                'label' => 'notifications.pushover_enabled.label',
                'help' => 'notifications.pushover_enabled.help',
                'required' => false,
                'disabled' => empty($this->pushoverToken),
                'data' => $notificationSettings->isPushoverEnabled()
            ]);

        foreach(UserType::cases() as $userType) {
            $builder->add(sprintf('%s', $userType->value), FieldsetType::class, [
                'legend' => $enumStringConverter->convert($userType),
                'fields' => function(FormBuilderInterface $builder) use ($userType, $notifierManager, $notificationSettings) {
                    foreach($notifierManager->getNotifiersForUserType($userType) as $notifier) {
                        $key = sprintf('%s_%s', $userType->value, $notifier->getKey());
                        $data = [ ];

                        foreach(NotificationDeliveryTarget::cases() as $target) {
                            $data[$target->value] = $notificationSettings->getDeliveryStrategy($userType, $notifier->getKey(), $target);
                        }

                        $builder
                            ->add($key, DeliveryOptionCompoundType::class, [
                                'label' => $notifier::getLabelKey(),
                                'help' => $notifier::getHelpKey(),
                                'required' => false,
                                'data' => $data
                            ]);
                    }
                }
            ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $notificationSettings->setNotificationsEnabled($form->get('enabled')->getData());
            $notificationSettings->setEmailEnabled($form->get('emailEnabled')->getData());
            $notificationSettings->setPushoverEnabled($form->get('pushoverEnabled')->getData());

            foreach(UserType::cases() as $userType) {
                foreach($notifierManager->getNotifiersForUserType($userType) as $notifier) {
                    $key = sprintf('%s_%s', $userType->value, $notifier->getKey());

                    foreach(NotificationDeliveryTarget::cases() as $target) {
                        $notificationSettings->setDeliveryStrategy($userType, $notifier->getKey(), $target, $form->getData()[$key][$target->value]);
                    }
                }
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_notifications');
        }

        return $this->render('admin/settings/notifications.html.twig', [
            'form' => $form->createView(),
            'isPushoverTokenMissing' => empty($this->pushoverToken)
        ]);
    }
}