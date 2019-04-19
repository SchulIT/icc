<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Grouping\Grouper;
use App\Grouping\MessageVisibilityGroup;
use App\Grouping\MessageVisibilityStrategy;
use App\Repository\MessageRepositoryInterface;
use App\Security\CurrentUserResolver;
use App\Sorting\Sorter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/messages")
 */
class MessageAdminController extends AbstractController {

    private $repository;
    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $repository, Grouper $grouper, Sorter $sorter) {
        $this->repository = $repository;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("/", name="admin_messages")
     */
    public function index() {
        $messages = $this->repository->findAll();
        /** @var MessageVisibilityGroup[] $groups */
        $groups = $this->grouper->group($messages, MessageVisibilityStrategy::class);

        return $this->render('admin/messages/index.html.twig', [
            'groups' => $groups
        ]);
    }

    /**
     * @Route("/add", name="add_message")
     */
    public function add(Request $request, CurrentUserResolver $currentUserResolver) {
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($message);

            $this->addFlash('success', 'message.add.success');
            return $this->redirectToRoute('admin_messages');
        }

        return $this->render('admin/messages/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
}