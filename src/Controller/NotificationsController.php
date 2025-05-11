<?php

namespace App\Controller;

use App\Entity\Notification;
use App\Entity\User;
use App\Repository\NotificationRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/notifications')]
class NotificationsController extends AbstractController {

    private const NotificationsPerPage = 5;

    public function __construct(RefererHelper $redirectHelper, private readonly NotificationRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'notifications')]
    public function index(Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $page = $request->query->getInt('page', 1);

        $paginator = $this->repository->getUserPaginator(
            $user,
            self::NotificationsPerPage,
            $page
        );

        $pages = ceil((double)$paginator->count() / self::NotificationsPerPage);

        return $this->render('notifications/index.html.twig', [
            'notifications' => $paginator->getIterator(),
            'pages' => $pages,
            'page' => $page
        ]);
    }

    #[Route('/{uuid}/redirect', name: 'notification_redirect')]
    public function redirectToLink(#[MapEntity(mapping: ['uuid' => 'uuid'])] Notification $notification): Response {
        $notification->setIsRead(true);
        $this->repository->persist($notification);

        if(empty($notification->getLink())) {
            return $this->redirectToRoute('notifications');
        }

        return $this->redirect($notification->getLink());
    }

    #[Route('/markread', name: 'mark_notifications_read')]
    public function markNotificationsRead(): Response {
        $user = $this->getUser();

        if($user instanceof User) {
            $this->repository->markAllReadForUser($user);
            $this->addFlash('success', 'notifications.mark_read.success');
        }

        return $this->redirectToRoute('notifications');
    }
}