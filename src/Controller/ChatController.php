<?php

namespace App\Controller;

use App\Chat\ChatSeenByHelper;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\ChatMessageAttachment;
use App\Entity\User;
use App\Filesystem\ChatFilesystem;
use App\Filesystem\FileNotFoundException;
use App\Form\ChatMessageType;
use App\Form\Model\NewChat;
use App\Form\NewChatType;
use App\Repository\ChatMessageAttachmentRepositoryInterface;
use App\Repository\ChatMessageRepositoryInterface;
use App\Repository\ChatRepositoryInterface;
use App\Security\Voter\ChatMessageAttachmentVoter;
use App\Security\Voter\ChatVoter;
use App\Sorting\ChatStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/chat')]
class ChatController extends AbstractController {

    public function __construct(RefererHelper $redirectHelper,
                                private readonly ChatRepositoryInterface $chatRepository,
                                private readonly ChatMessageRepositoryInterface $chatMessageRepository,
                                private readonly ChatMessageAttachmentRepositoryInterface $attachmentRepository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'chats')]
    public function index(Sorter $sorter): Response {
        /** @var User $user */
        $user = $this->getUser();

        $chats = $this->chatRepository->findAllByUser($user);
        $sorter->sort($chats, ChatStrategy::class, SortDirection::Descending);

        return $this->render('chat/index.html.twig', [
            'chats' => $chats
        ]);
    }

    #[Route('/add', name: 'new_chat')]
    public function add(Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $chat = new NewChat();
        $form = $this->createForm(NewChatType::class, $chat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $chatEntity = (new Chat())->setTopic($chat->topic);
            $chatEntity->addParticipants($user);
            foreach($chat->recipients as $recipient) {
                $chatEntity->addParticipants($recipient);
            }

            $this->chatRepository->persist($chatEntity);
            $message = (new ChatMessage())
                ->setChat($chatEntity)
                ->setContent($chat->message);
            $this->chatMessageRepository->persist($message);

            $this->addFlash('success', 'chat.add.success');

            return $this->redirectToRoute('chats');
        }

        return $this->render('chat/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/attachment/{uuid}/remove', name: 'remove_chat_attachment')]
    public function removeAttachment(ChatMessageAttachment $attachment, Request $request): Response {
        $this->denyAccessUnlessGranted(ChatMessageAttachmentVoter::Remove, $attachment);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'chat.attachment.remove.confirm',
            'message_parameters' => [
                '%filename%' => $attachment->getFilename()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->attachmentRepository->remove($attachment);
            $this->addFlash('success', 'chat.attachment.remove.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $attachment->getMessage()->getChat()->getUuid()
            ]);
        }

        return $this->render('chat/remove_attachment.html.twig', [
            'form' => $form->createView(),
            'attachment' => $attachment
        ]);
    }

    #[Route('/attachment/{uuid}/download', name: 'download_chat_attachment')]
    public function downloadAttachment(ChatMessageAttachment $attachment, ChatFilesystem $chatFilesystem): Response {
        $this->denyAccessUnlessGranted(ChatMessageAttachmentVoter::Download, $attachment);

        try {
            return $chatFilesystem->getDownloadResponse($attachment);
        } catch (FileNotFoundException $e) {
            throw $this->createNotFoundException(null, $e);
        }
    }

    #[Route('/{uuid}', name: 'show_chat')]
    public function showChat(Chat $chat, Request $request, ChatSeenByHelper $chatSeenByHelper): Response {
        $this->denyAccessUnlessGranted(ChatVoter::View, $chat);

        // mark messages read
        $chatSeenByHelper->markAllChatMessagesSeen($chat);

        $attachments = $this->attachmentRepository->findByChat($chat);

        $message = new ChatMessage();
        $message->setChat($chat);

        $form = $this->createForm(ChatMessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->chatMessageRepository->persist($message);
            $this->addFlash('success', 'chat.message.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
            'attachments' => $attachments,
            'form' => $form->createView()
        ]);
    }
}