<?php

namespace App\Controller;

use App\Grouping\Grouper;
use App\Grouping\UserTypeAndGradeStrategy;
use App\Repository\UserRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\StringStrategy;
use App\Sorting\UserUsernameStrategy;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class ProfileController extends AbstractController {

    /**
     * @Route("/switch", name="switch_user")
     * @Security("has_role('ROLE_ALLOWED_TO_SWITCH')")
     */
    public function switchUser(Grouper $grouper, Sorter $sorter, UserRepositoryInterface $userRepository) {
        $users = $userRepository->findAll();
        $groups = $grouper->group($users, UserTypeAndGradeStrategy::class);
        $sorter->sort($groups, StringStrategy::class);
        $sorter->sortGroupItems($groups, UserUsernameStrategy::class);

        return $this->render('profile/switch.html.twig', [
            'groups' => $groups
        ]);
    }
}