<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Grouping\Grouper;
use App\Grouping\MessageExpirationGroup;
use App\Grouping\MessageExpirationStrategy;
use App\Grouping\MessageVisibilityGroup;
use App\Grouping\MessageVisibilityStrategy;
use App\Repository\MessageRepositoryInterface;
use App\Sorting\MessageStrategy;
use App\Sorting\Sorter;
use App\View\Filter\UserTypeFilter;
use League\CommonMark\Util\ArrayCollection;
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
     * @Route("", name="admin_messages")
     */
    public function index(UserTypeFilter $userTypeFilter, ?string $userType = null) {
        $userTypeFilterView = $userTypeFilter->handle($userType);
        $userTypeFilterView->setHandleNull(true);

        if($userTypeFilterView->getCurrentType() === null) {
            $messages = $this->repository->findAll();
        } else {
            $messages = $this->repository->findAllByUserType($userTypeFilterView->getCurrentType());
        }

        /** @var MessageExpirationGroup[] $groups */
        $groups = $this->grouper->group($messages, MessageExpirationStrategy::class);

        return $this->render('admin/messages/index.html.twig', [
            'groups' => $groups,
            'userTypeFilter' => $userTypeFilterView
        ]);
    }

    /**
     * @Route("/add", name="add_message")
     */
    public function add(Request $request) {
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

    /**
     * @Route("/{id}/edit", name="edit_message")
     */
    public function edit(Request $request, Message $message) {
        $originalFiles = new ArrayCollection();
        foreach($message->getFiles() as $file) {
            $originalFiles->add($file);
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($originalFiles as $file) {
                if($message->getFiles()->contains($file) === false) {
                    $this->repository->removeMessageFile($file);
                }
            }

            $this->repository->persist($message);

            $this->addFlash('success', 'message.edit.succes');
            return $this->redirectToRoute('admin_messages');
        }

        return $this->render('admin/messages/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }
}