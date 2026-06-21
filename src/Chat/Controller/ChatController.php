<?php

namespace App\Chat\Controller;

use App\Chat\ChatSeenByHelper;
use App\Chat\ChatTagHelper;
use App\Chat\Entity\Chat;
use App\Chat\Entity\ChatMessage;
use App\Chat\Entity\ChatMessageAttachment;
use App\Chat\Filesystem\ChatFilesystem;
use App\Chat\Form\ChatMessageType;
use App\Chat\Form\ChatType;
use App\Chat\Form\Field\ChatUserAutocompleteField;
use App\Chat\Repository\ChatMessageAttachmentRepositoryInterface;
use App\Chat\Repository\ChatMessageRepositoryInterface;
use App\Chat\Repository\ChatRepositoryInterface;
use App\Chat\Settings\ChatSettings;
use App\Chat\View\Filter\ChatTagFilter;
use App\Chat\Voter\ChatMessageAttachmentVoter;
use App\Chat\Voter\ChatMessageVoter;
use App\Chat\Voter\ChatVoter;
use App\Common\Entity\User;
use App\Common\Repository\UserRepositoryInterface;
use App\Framework\Controller\AbstractController;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\Framework\Filesystem\FileNotFoundException;
use App\Framework\Repository\PaginatedResult;
use App\Framework\Repository\PaginationQuery;
use App\Framework\Security\Firewall\Attribute\IsGrantedIfNotImpersonated;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/chat')]
#[IsFeatureEnabled(Feature::Chat)]
#[IsGranted(ChatVoter::ChatEnabled)]
#[IsGrantedIfNotImpersonated]
class ChatController extends AbstractController {

    public function __construct(RefererHelper                                             $redirectHelper,
                                private readonly ChatRepositoryInterface                  $chatRepository,
                                private readonly ChatMessageRepositoryInterface           $chatMessageRepository,
                                private readonly ChatMessageAttachmentRepositoryInterface $attachmentRepository,
                                private readonly ChatSettings                             $chatSettings) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'chats')]
    public function index(
        ChatTagHelper $chatTagHelper,
        ChatTagFilter $chatTagFilter,
        #[MapQueryParameter] int $page = 1,
        #[MapQueryParameter] string|null $tag = null,
        #[MapQueryParameter(name: 'archive')] bool $isArchive = false
    ): Response {
        /** @var User $user */
        $user = $this->getUser();

        $chatTagFilterView = $chatTagFilter->handle($tag, $user);
        $chats = $this->chatRepository->findAllByUserPaginated(new PaginationQuery(page: $page), $user, $isArchive, $chatTagFilterView->getCurrentTag());

        $unreadCount = [ ];
        foreach($chats as $chat) {
            $unreadCount[$chat->getId()] = $this->chatMessageRepository->countUnreadMessages($user, $chat);
        }

        $userTags = [ ];
        $lastMessageDates = [ ];
        $messagesCount = [ ];
        foreach($chats as $chat) {
            $userTags[$chat->getId()] = $chatTagHelper->getTagsForUser($chat, $user);
            $messagesCount[$chat->getId()] = $this->chatMessageRepository->countByChat($chat);
            $lastMessageDates[$chat->getId()] = $this->chatMessageRepository->findLastMessageDate($chat);
        }

        $attachmentsCount = [ ];
        foreach($chats as $chat) {
            $attachmentsCount[$chat->getId()] = $this->attachmentRepository->countByChat($chat);
        }

        return $this->render('chat/index.html.twig', [
            'chats' => $chats,
            'messagesCount' => $messagesCount,
            'unreadCount' => $unreadCount,
            'lastMessageDates' => $lastMessageDates,
            'attachmentsCount' => $attachmentsCount,
            'userTags' => $userTags,
            'tagFilter' => $chatTagFilterView,
            'isArchive' => $isArchive
        ]);
    }

    #[Route('/add', name: 'new_chat')]
    public function add(Request $request, UserRepositoryInterface $userRepository): Response {
        $chat = new Chat();
        $chat->addMessage(new ChatMessage());

        if($request->query->all('recipients')) {
            foreach($request->query->all('recipients') as $uuid) {
                $user = $userRepository->findOneByUuid($uuid);

                if($user !== null) {
                    $chat->addParticipants($user);
                }
            }
        }

        /** @var User $user */
        $user = $this->getUser();

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
    public function removeAttachment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ChatMessageAttachment $attachment, Request $request): Response {
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
    public function downloadAttachment(#[MapEntity(mapping: ['uuid' => 'uuid'])] ChatMessageAttachment $attachment, ChatFilesystem $chatFilesystem): Response {
        $this->denyAccessUnlessGranted(ChatMessageAttachmentVoter::Download, $attachment);

        try {
            return $chatFilesystem->getDownloadResponse($attachment);
        } catch (FileNotFoundException $e) {
            throw $this->createNotFoundException('Datei wurde nicht gefunden.', $e);
        }
    }

    #[Route('/{uuid}', name: 'show_chat')]
    public function showChat(#[MapEntity(mapping: ['uuid' => 'uuid'])] Chat $chat, Request $request, ChatSeenByHelper $chatSeenByHelper, ChatTagHelper $chatTagHelper, EventDispatcherInterface $dispatcher, TranslatorInterface $translator): Response {
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
            $this->denyAccessUnlessGranted(ChatVoter::Reply, $chat);

            $this->chatMessageRepository->persist($message);
            $this->addFlash('success', 'chat.message.add.success');

            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        if($this->isGranted(ChatVoter::Participants, $chat) && $request->getMethod() === Request::METHOD_POST
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

        $participantsForm = $this->createForm(ChatUserAutocompleteField::class, null, ['multiple' => true, 'required' => false]);
        $participantsForm->handleRequest($request);

        if($this->isGranted(ChatVoter::Participants, $chat) && $participantsForm->isSubmitted() && $participantsForm->isValid()) {
            $newParticipants = $participantsForm->getData();

            foreach ($newParticipants as $newParticipant) {
                if (!$chat->getParticipants()->contains($newParticipant)) {
                    $chat->getParticipants()->add($newParticipant);
                }
            }

            $this->chatRepository->persist($chat);
            $this->addFlash('success', 'chat.participants.add.success');

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

        // User Tags
        $tags = $chatTagHelper->getAll($user->getUserType());
        $userTags = $chatTagHelper->getTagsForUser($chat, $user);

        if($request->isMethod(Request::METHOD_POST) && $this->isCsrfTokenValid('chat', $request->request->get('_csrf_token'))) {
            $enabledTags = $request->request->all('userTags');
            $chatTagHelper->synchronizeUserTags($chat, $user, $enabledTags);
            $this->chatRepository->persist($chat);

            $this->addFlash('success', 'chat.tags.saved');
            return $this->redirectToRoute('show_chat', [
                'uuid' => $chat->getUuid()
            ]);
        }

        return $this->render('chat/show.html.twig', [
            'chat' => $chat,
            'attachments' => $attachments,
            'form' => $form->createView(),
            'participantsForm' => $participantsForm->createView(),
            'editForms' => $editForms,
            'removeForms' => $removeForms,
            'renameForm' => $renameForm->createView(),
            'tags' => $tags,
            'userTags' => $userTags,
            'canEditOrRemove' => in_array($user->getUserType(), $this->chatSettings->getUserTypesAllowedToEditOrRemoveMessages(), strict: true)
        ]);
    }

    #[Route('/{uuid}/archive', name: 'archive_chat')]
    public function archive(#[MapEntity(mapping: ['uuid' => 'uuid'])] Chat $chat, Request $request): Response {
        $this->denyAccessUnlessGranted(ChatVoter::Archive, $chat);

        if($this->isCsrfTokenValid('archive_chat', $request->request->get('_csrf_token'))) {
            $chat->setIsArchived(true);
            $this->chatRepository->persist($chat);
            $this->addFlash('success', 'chat.archive.archive.success');
        }

        return $this->redirectToRoute('show_chat', [
            'uuid' => $chat->getUuid()
        ]);
    }

    #[Route('/{uuid}/unarchive', name: 'unarchive_chat')]
    public function unarchive(#[MapEntity(mapping: ['uuid' => 'uuid'])] Chat $chat, Request $request): Response {
        $this->denyAccessUnlessGranted(ChatVoter::Unarchive, $chat);

        if($this->isCsrfTokenValid('archive_chat', $request->request->get('_csrf_token'))) {
            $chat->setIsArchived(false);
            $this->chatRepository->persist($chat);
            $this->addFlash('success', 'chat.archive.unarchive.success');
        }

        return $this->redirectToRoute('show_chat', [
            'uuid' => $chat->getUuid()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_chat')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] Chat $chat, Request $request): Response {
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
