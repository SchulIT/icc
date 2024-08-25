<?php

namespace App\Controller;

use App\Chat\ChatSeenByHelper;
use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\ChatMessageAttachment;
use App\Entity\User;
use App\Event\ChatMessageCreatedEvent;
use App\Filesystem\ChatFilesystem;
use App\Filesystem\FileNotFoundException;
use App\Form\ChatMessageType;
use App\Form\ChatUserRecipientType;
use App\Form\Model\NewChat;
use App\Form\ChatType;
use App\Repository\ChatMessageAttachmentRepositoryInterface;
use App\Repository\ChatMessageRepositoryInterface;
use App\Repository\ChatRepositoryInterface;
use App\Security\Voter\ChatMessageAttachmentVoter;
use App\Security\Voter\ChatMessageVoter;
use App\Security\Voter\ChatVoter;
use App\Settings\ChatSettings;
use App\Sorting\ChatStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/chat')]
#[IsGranted(ChatVoter::ChatEnabled)]
class ChatController extends AbstractController {

    public function __construct(RefererHelper                                             $redirectHelper,
                                private readonly ChatRepositoryInterface                  $chatRepository,
                                private readonly ChatMessageRepositoryInterface           $chatMessageRepository,
                                private readonly ChatMessageAttachmentRepositoryInterface $attachmentRepository,
                                private readonly ChatSettings $chatSettings) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'chats')]
    public function index(Sorter $sorter): Response {
        /** @var User $user */
        $user = $this->getUser();

        $chats = $this->chatRepository->findAllByUser($user);
        $sorter->sort($chats, ChatStrategy::class, SortDirection::Descending);

        $unreadCount = [ ];
        foreach($chats as $chat) {
            $unreadCount[$chat->getId()] = $this->chatMessageRepository->countUnreadMessages($user, $chat);
        }

        return $this->render('chat/index.html.twig', [
            'chats' => $chats,
            'unreadCount' => $unreadCount
        ]);
    }

    #[Route('/add', name: 'new_chat')]
    public function add(Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();

        $chat = new Chat();
        $chat->addMessage(new ChatMessage());
        $form = $this->createForm(ChatType::class, $chat);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $chat->addParticipants($user);
            $this->chatRepository->persist($chat);

            $this->addFlash('success', 'chat.add.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        return $this->render('chat/add.html.twig', [
            'form' => $form->createView(),
            'canEditOrRemove' => in_array($user->getUserType(), $this->chatSettings->getUserTypesAllowedToEditOrRemoveMessages(), strict: true)
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
            throw $this->createNotFoundException('Datei wurde nicht gefunden.', $e);
        }
    }

    #[Route('/{uuid}', name: 'show_chat')]
    public function showChat(Chat $chat, Request $request, ChatSeenByHelper $chatSeenByHelper, EventDispatcherInterface $dispatcher, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(ChatVoter::View, $chat);

        /** @var User $user */
        $user = $this->getUser();

        // mark messages read
        $chatSeenByHelper->markAllChatMessagesSeen($chat);

        $attachments = $this->attachmentRepository->findByChat($chat);

        $message = new ChatMessage();
        $message->setChat($chat);

        $form = $this->createForm(ChatMessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->chatMessageRepository->persist($message);
            $this->addFlash('success', 'chat.message.add.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        if($this->isGranted(ChatVoter::Edit, $chat) && $request->getMethod() === Request::METHOD_POST
            && $request->request->has('remove')
            && $this->isCsrfTokenValid('remove_participant', $request->request->get('_csrf_token'))) {

            /** @var User $user */
            $user = $this->getUser();
            $participants = $chat->getParticipants()->toArray();
            foreach($participants as $participant) {
                if($participant->getId() === $chat->getCreatedBy()->getId()) { // chat creator cannot remove himself
                    continue;
                }

                if($participant->getId() === $user->getId()) { // users cannot remove themselves
                    continue;
                }

                if($participant->getUuid()->toString() === $request->request->get('remove')) {
                    $chat->removeParticipants($participant);
                    $this->chatRepository->persist($chat);
                    $this->addFlash('success', 'chat.participants.remove.success');

                    return $this->redirectToRoute('show_chat', [
                        'uuid' => $chat->getUuid()
                    ]);
                }
            }
        }

        $renameForm = $this->createForm(ChatType::class, $chat);
        $renameForm->remove('participants')->remove('messages');
        $renameForm->handleRequest($request);

        if($this->isGranted(ChatVoter::Edit, $chat) && $renameForm->isSubmitted() && $renameForm->isValid()) {
            $this->chatRepository->persist($chat);
            $this->addFlash('success', 'chat.rename.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        $participantsForm = $this->createForm(ChatUserRecipientType::class, null, ['multiple' => false, 'required' => false]);
        $participantsForm->handleRequest($request);

        if($this->isGranted(ChatVoter::Edit, $chat) && $participantsForm->isSubmitted() && $participantsForm->isValid()) {
            $newParticipant = $participantsForm->getData();

            if(!$chat->getParticipants()->contains($newParticipant)) {
                $chat->getParticipants()->add($newParticipant);
                $this->chatRepository->persist($chat);

                $dispatcher->dispatch(new ChatMessageCreatedEvent($chat->getMessages()->first()));

                $this->addFlash('success', 'chat.participants.add.success');
            } else {
                $this->addFlash('error', 'chat.participants.add.error');
            }

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        $editForms = [ ];
        $removeForms = [ ];

        foreach($chat->getMessages() as $message) {
            if($this->isGranted(ChatMessageVoter::Edit, $message)) {
                $editForm = $this->container->get('form.factory')->createNamed('edit_' . $message->getId(), ChatMessageType::class, $message);
                $editForm->handleRequest($request);

                if($editForm->isSubmitted() && $editForm->isValid()) {
                    $this->chatMessageRepository->persist($message);
                    return $this->redirectToRoute('show_chat', [
                        'uuid' => $message->getChat()->getUuid()
                    ]);
                }

                $editForms[$message->getId()] = $editForm->createView();
            }

            if($this->isGranted(ChatMessageVoter::Remove, $message)) {
                $removeForm = $this->container->get('form.factory')->createNamed('remove_' . $message->getId(), ConfirmType::class, null, [
                    'message' => 'chat.message.remove.confirm'
                ]);
                $removeForm->handleRequest($request);

                if($removeForm->isSubmitted() && $removeForm->isValid()) {
                    $message->setContent($translator->trans('chat.message.remove.message_content'));
                    $this->chatMessageRepository->persist($message);
                    return $this->redirectToRoute('show_chat', [
                        'uuid' => $message->getChat()->getUuid()
                    ]);
                }

                $removeForms[$message->getId()] = $removeForm->createView();
            }
        }

        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
            'attachments' => $attachments,
            'form' => $form->createView(),
            'participantsForm' => $participantsForm->createView(),
            'editForms' => $editForms,
            'removeForms' => $removeForms,
            'renameForm' => $renameForm->createView(),
            'canEditOrRemove' => in_array($user->getUserType(), $this->chatSettings->getUserTypesAllowedToEditOrRemoveMessages(), strict: true)
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_chat')]
    public function remove(Chat $chat, Request $request): Response {
        $this->denyAccessUnlessGranted(ChatVoter::Remove, $chat);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'chat.remove.confirm',
            'message_parameters' => [
                '%topic%' => $chat->getTopic()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->chatRepository->remove($chat);
            $this->addFlash('success', 'chat.remove.success');

            return $this->redirectToRoute('chats');
        }

        return $this->render('chat/remove.html.twig', [
            'form' => $form->createView(),
            'chat' => $chat
        ]);
    }
}