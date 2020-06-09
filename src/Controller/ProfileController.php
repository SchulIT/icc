<?php

namespace App\Controller;

use App\Entity\DeviceToken;
use App\Entity\OAuthClientInfo;
use App\Entity\User;
use App\Form\NotificationsType;
use App\Grouping\Grouper;
use App\Grouping\UserTypeAndGradeStrategy;
use App\Notification\Email\EmailNotificationService;
use App\Repository\DeviceTokenRepositoryInterface;
use App\Repository\OAuthClientInfoRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\OAuth2\AppManager;
use App\Security\Voter\AccessTokenVoter;
use App\Security\Voter\DeviceTokenVoter;
use App\Settings\NotificationSettings;
use App\Sorting\Sorter;
use App\Sorting\StringGroupStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\UserUsernameStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Trikoder\Bundle\OAuth2Bundle\Manager\ClientManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Manager\RefreshTokenManagerInterface;
use Trikoder\Bundle\OAuth2Bundle\Model\AccessToken;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController {

    private const RemoveAppCrsfTokenKey = '_remove_app_csrf';

    /**
     * @Route("", name="profile")
     */
    public function index() {
        return $this->render('profile/index.html.twig');
    }

    /**
     * @Route("/notifications", name="profile_notifications")
     */
    public function notifications(Request $request, UserRepositoryInterface $userRepository, NotificationSettings $notificationSettings) {
        /** @var User $user */
        $user = $this->getUser();

        $allowedEmailUserTypes = $notificationSettings->getEmailEnabledUserTypes();
        $allowedPushUserTypes = $notificationSettings->getPushEnabledUserTypes();

        $isEmailAllowed = EnumArrayUtils::inArray($user->getUserType(), $allowedEmailUserTypes) !== false;
        $isPushAllowed = EnumArrayUtils::inArray($user->getUserType(), $allowedPushUserTypes);

        $isAllowed = $isEmailAllowed || $isPushAllowed;
        $form = null;

        if($isAllowed === true) {
            $form = $this->createForm(NotificationsType::class, $user, [
                'allow_email' => $isEmailAllowed
            ]);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()) {
                $userRepository->persist($user);
                $this->addFlash('success', 'profile.notifications.success');

                return $this->redirectToRoute('profile_notifications');
            }
        }

        return $this->render('profile/notifications.html.twig', [
            'form' => $form !== null ? $form->createView() : null,
            'is_allowed' => $isAllowed,
            'email_allowed' => $isEmailAllowed,
            'push_allowed' => $isPushAllowed
        ]);
    }

    /**
     * @Route("/apps", name="profile_apps")
     */
    public function apps(DeviceTokenRepositoryInterface $deviceTokenRepository, AppManager $appManager, OAuthClientInfoRepositoryInterface $clientInfoRepository) {
        /** @var User $user */
        $user = $this->getUser();

        $devices = $deviceTokenRepository->findAllBy($user);
        $tokens = $appManager->getAccessTokens($user);
        $clientInfo = ArrayUtils::createArrayWithKeys(
            $clientInfoRepository->findAll(),
            function(OAuthClientInfo $clientInfo) {
                return $clientInfo->getClient()->getIdentifier();
            }
        );

        return $this->render('profile/apps.html.twig', [
            'apps' => $devices,
            'tokens' => $tokens,
            'info' => $clientInfo,
            'csrf_key' => static::RemoveAppCrsfTokenKey
        ]);
    }

    /**
     * @Route("/apps/{uuid}/remove", name="profile_remove_app", methods={"POST"})
     */
    public function removeApp(DeviceToken $token, Request $request, DeviceTokenRepositoryInterface $deviceTokenRepository) {
        $this->denyAccessUnlessGranted(DeviceTokenVoter::Remove, $token);

        $csrfToken = $request->request->get('_csrf_token');
        if($this->isCsrfTokenValid(static::RemoveAppCrsfTokenKey, $csrfToken)) {
            $deviceTokenRepository->remove($token);

            $this->addFlash('success', 'profile.apps.remove.success');
        } else {
            $this->addFlash('success', 'profile.apps.remove.error.csrf');
        }

        return $this->redirectToRoute('profile_apps');
    }

    /**
     * @Route("/tokens/{identifier}/revoke", name="profile_revoke_token", methods={"POST"})
     */
    public function revokeToken(AccessToken $token, Request $request, AppManager $appManager) {
        $this->denyAccessUnlessGranted(AccessTokenVoter::Revoke, $token);

        $csrfToken = $request->request->get('_csrf_token');
        if($this->isCsrfTokenValid(static::RemoveAppCrsfTokenKey, $csrfToken)) {
            $appManager->revokeAccessToken($token);

            $this->addFlash('success', 'profile.apps.remove.success');
        } else {
            $this->addFlash('success', 'profile.apps.remove.error.csrf');
        }

        return $this->redirectToRoute('profile_apps');
    }

    /**
     * @Route("/switch", name="switch_user")
     * @Security("is_granted('ROLE_ALLOWED_TO_SWITCH')")
     */
    public function switchUser(Grouper $grouper, Sorter $sorter, UserRepositoryInterface $userRepository) {
        $users = $userRepository->findAll();
        $groups = $grouper->group($users, UserTypeAndGradeStrategy::class);
        $sorter->sort($groups, StringGroupStrategy::class);
        $sorter->sortGroupItems($groups, UserUsernameStrategy::class);

        return $this->render('profile/switch.html.twig', [
            'groups' => $groups
        ]);
    }
}