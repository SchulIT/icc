<?php

namespace App\Controller;

use App\Entity\DeviceToken;
use App\Entity\User;
use App\Grouping\Grouper;
use App\Grouping\UserTypeAndGradeStrategy;
use App\Repository\DeviceTokenRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Voter\DeviceTokenVoter;
use App\Sorting\Sorter;
use App\Sorting\StringGroupStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\UserUsernameStrategy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

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
    public function notifications() {

    }

    /**
     * @Route("/apps", name="profile_apps")
     */
    public function apps(DeviceTokenRepositoryInterface $deviceTokenRepository) {
        /** @var User $user */
        $user = $this->getUser();

        $devices = $deviceTokenRepository->findAllBy($user);

        return $this->render('profile/apps.html.twig', [
            'apps' => $devices,
            'csrf_key' => static::RemoveAppCrsfTokenKey
        ]);
    }

    /**
     * @Route("/apps/{id}/remove", name="profile_remove_app", methods={"POST"})
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