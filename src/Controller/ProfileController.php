<?php

namespace App\Controller;

use App\Entity\IcsAccessToken;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Grouping\UserTypeAndGradeStrategy;
use App\Messenger\SendPushoverNotificationMessage;
use App\Notification\Delivery\DeliverStrategyType;
use App\Notification\Delivery\DeliveryDecider;
use App\Notification\EventSubscriber\NotifierManager;
use App\Notification\NotificationDeliveryTarget;
use App\Notification\UserNotificationSettingSaver;
use App\Repository\DeviceTokenRepositoryInterface;
use App\Repository\UserNotificationSettingRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\DeviceTokenVoter;
use App\Settings\NotificationSettings;
use App\Sorting\Sorter;
use App\Sorting\StringGroupStrategy;
use App\Sorting\UserUsernameStrategy;
use SchulIT\CommonBundle\Form\FieldsetType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/profile')]
class ProfileController extends AbstractController {

    private const RemoveAppCrsfTokenKey = '_remove_app_csrf';

    public function __construct(private readonly ?string $pushoverToken, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route(path: '', name: 'profile')]
    public function index(): Response {
        return $this->render('profile/index.html.twig');
    }

    #[Route(path: '/notifications', name: 'profile_notifications')]
    public function notifications(Request $request, NotificationSettings $notificationSettings, UserNotificationSettingSaver $notificationSettingSaver,
                                  UserNotificationSettingRepositoryInterface $notificationSettingRepository, DeliveryDecider $deliveryDecider,
                                  UserRepositoryInterface $userRepository,
                                  NotifierManager $notifierManager, MessageBusInterface $messageBus, TranslatorInterface $translator): Response {
        /** @var User $user */
        $user = $this->getUser();

        $isEnabled = $notificationSettings->isNotificationsEnabled();
        $isEmailEnabled = $notificationSettings->isEmailEnabled();
        $isPushoverEnabled = $notificationSettings->isPushoverEnabled() && !empty($this->pushoverToken);

        $formBuilder = $this->createFormBuilder();

        if($isPushoverEnabled) {
            $formBuilder->add('pushoverToken', TextType::class, [
                'required' => false,
                'label' => 'profile.notifications.pushover.label',
                'help' => 'profile.notifications.pushover.help',
                'constraints' => [
                    new Regex('/^[a-zA-Z0-9]{30}$/')
                ],
                'data' => $user->getPushoverToken(),
            ]);
        }

        $formBuilder
            ->add('isSubstitutionNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.substitutions.label',
                'help' => 'profile.notifications.substitutions.help',
                'required' => false,
                'data' => $user->isSubstitutionNotificationsEnabled()
            ])
            ->add('isExamNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.exams.label',
                'help' => 'profile.notifications.exams.help',
                'required' => false,
                'data' => $user->isExamNotificationsEnabled()
            ])
            ->add('isMessageNotificationsEnabled', CheckboxType::class, [
                'label' => 'profile.notifications.messages.label',
                'help' => 'profile.notifications.messages.help',
                'required' => false,
                'data' => $user->isMessageNotificationsEnabled()
            ]);

        foreach(NotificationDeliveryTarget::cases() as $target) {
            if(($target === NotificationDeliveryTarget::Email && $isEmailEnabled) || ($target === NotificationDeliveryTarget::Pushover && $isPushoverEnabled)) {
                $formBuilder->add($target->value, FieldsetType::class, [
                    'legend' => $target->trans($translator),
                    'fields' => function(FormBuilderInterface $builder) use ($notifierManager, $user, $notificationSettings, $notificationSettingRepository, $deliveryDecider, $target) {
                        foreach ($notifierManager->getNotifiersForUserType($user->getUserType()) as $notifier) {
                            $strategy = $notificationSettings->getDeliveryStrategy($user->getUserType(), $notifier::getKey(), $target);
                            $isEnabled = in_array($strategy, [DeliverStrategyType::OptIn, DeliverStrategyType::OptOut]);
                            $data = $deliveryDecider->decide($user, $notifier::getKey(), $target);

                            if($strategy !== DeliverStrategyType::Never) {
                                $builder->add($notifier::getKey(), CheckboxType::class, [
                                    'required' => false,
                                    'label' => $notifier::getLabelKey(),
                                    'help' => $notifier::getHelpKey(),
                                    'disabled' => $isEnabled === false,
                                    'data' => $data
                                ]);
                            }
                        }
                    }
                ]);
            }
        }

        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $request->request->get('test', null) === 'pushover' && !empty($user->getPushoverToken())) {
            $messageBus->dispatch(
                new SendPushoverNotificationMessage(
                    $user->getId(),
                    $user->getUserIdentifier(),
                    $translator->trans('test.content', [], 'push'),
                    $translator->trans('test.title', [], 'push')
                )
            );

            $this->addFlash('success', 'profile.notifications.pushover.test.success');
            return $this->redirectToRoute('profile_notifications');
        }

        if($form->isSubmitted() && $form->isValid()) {
            if($isPushoverEnabled) {
                $user->setPushoverToken($form->get('pushoverToken')->getData());
            }

            $user->setIsSubstitutionNotificationsEnabled($form->get('isSubstitutionNotificationsEnabled')->getData());
            $user->setIsExamNotificationsEnabled($form->get('isExamNotificationsEnabled')->getData());
            $user->setIsMessageNotificationsEnabled($form->get('isMessageNotificationsEnabled')->getData());
            $userRepository->persist($user);

            foreach($notifierManager->getNotifiersForUserType($user->getUserType()) as $notifier) {
                foreach(NotificationDeliveryTarget::cases() as $target) {
                    if(($target === NotificationDeliveryTarget::Email && $isEmailEnabled) || ($target === NotificationDeliveryTarget::Pushover && $isPushoverEnabled)) {
                        if($form->get($target->value)->has($notifier::getKey())) {
                            $isEnabled = $form->get($target->value)->get($notifier::getKey())->getData();
                            $notificationSettingSaver->persist($user, $notifier::getKey(), $target, $isEnabled);
                        }
                    }
                }
            }

            $this->addFlash('success', 'profile.notifications.success');
            return $this->redirectToRoute('profile_notifications');
        }

        return $this->render('profile/notifications.html.twig', [
            'form' => $form->createView(),
            'isEnabled' => $isEnabled,
            'isEmailEnabled' => $isEmailEnabled,
            'isPushoverEnabled' => $isPushoverEnabled
        ]);
    }

    #[Route(path: '/apps', name: 'profile_apps')]
    public function apps(DeviceTokenRepositoryInterface $deviceTokenRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        $devices = $deviceTokenRepository->findAllBy($user);

        return $this->render('profile/apps.html.twig', [
            'apps' => $devices,
            'csrf_key' => self::RemoveAppCrsfTokenKey
        ]);
    }

    #[Route(path: '/apps/{uuid}/remove', name: 'profile_remove_app', methods: ['POST'])]
    public function removeApp(IcsAccessToken $token, Request $request, DeviceTokenRepositoryInterface $deviceTokenRepository): Response {
        $this->denyAccessUnlessGranted(DeviceTokenVoter::Remove, $token);

        $csrfToken = $request->request->get('_csrf_token');
        if($this->isCsrfTokenValid(self::RemoveAppCrsfTokenKey, $csrfToken)) {
            $deviceTokenRepository->remove($token);

            $this->addFlash('success', 'profile.apps.remove.success');
        } else {
            $this->addFlash('success', 'profile.apps.remove.error.csrf');
        }

        return $this->redirectToRoute('profile_apps');
    }

    #[Route(path: '/switch', name: 'switch_user')]
    #[Security("is_granted('ROLE_ALLOWED_TO_SWITCH')")]
    public function switchUser(Grouper $grouper, Sorter $sorter, UserRepositoryInterface $userRepository, SectionResolverInterface $sectionResolver): Response {
        $users = $userRepository->findAll();
        $groups = $grouper->group($users, UserTypeAndGradeStrategy::class, ['section' => $sectionResolver->getCurrentSection()]);
        $sorter->sort($groups, StringGroupStrategy::class);
        $sorter->sortGroupItems($groups, UserUsernameStrategy::class);

        return $this->render('profile/switch.html.twig', [
            'groups' => $groups
        ]);
    }
}