<?php

namespace App\Controller;

use App\Entity\Message;
use App\Event\MessageUpdatedEvent;
use App\Filesystem\MessageFilesystem;
use App\Form\MessageType;
use App\Grouping\Grouper;
use App\Grouping\MessageExpirationGroup;
use App\Grouping\MessageExpirationStrategy;
use App\Grouping\StudentGradeStrategy;
use App\Grouping\StudentStudyGroupStrategy;
use App\Grouping\UserUserTypeStrategy;
use App\Message\MessageFileUploadViewHelper;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Voter\MessageVoter;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\StudentStudyGroupGroupStrategy;
use App\Sorting\TeacherStrategy;
use App\Sorting\UserLastnameFirstnameStrategy;
use App\Sorting\UserUserTypeGroupStrategy;
use App\Utils\RefererHelper;
use App\View\Filter\UserTypeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use SchoolIT\CommonBundle\Form\ConfirmType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/admin/messages")
 */
class MessageAdminController extends AbstractController {

    private const CsrfTokenName = '_csrf_token';
    private const CsrfTokenId = 'message_files';

    private $repository;
    private $grouper;
    private $sorter;

    public function __construct(MessageRepositoryInterface $repository, Grouper $grouper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->repository = $repository;
        $this->grouper = $grouper;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="admin_messages")
     */
    public function index(UserTypeFilter $userTypeFilter, ?string $userType = null) {
        $this->denyAccessUnlessGranted('ROLE_MESSAGE_CREATOR');

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
        $this->denyAccessUnlessGranted(MessageVoter::New);

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($message);

            $this->addFlash('success', 'admin.messages.add.success');
            return $this->redirectToRoute('admin_messages');
        }

        return $this->render('admin/messages/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/edit", name="edit_message")
     */
    public function edit(Request $request, Message $message, EventDispatcherInterface $eventDispatcher) {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        $originalFiles = new ArrayCollection();
        foreach($message->getFiles() as $file) {
            $originalFiles->add($file);
        }

        $update = (bool)$request->request->get('update', false);
        $groups = ['Default'];

        if($update === true) {
            $groups[] = 'update';
        }

        $form = $this->createForm(MessageType::class, $message, [
            'validation_groups' => $groups
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($originalFiles as $file) {
                if($message->getFiles()->contains($file) === false) {
                    $this->repository->removeMessageFile($file);
                }
            }

            $this->repository->persist($message);

            if($update === true) {
                $eventDispatcher->dispatch(new MessageUpdatedEvent($message));
            }

            $this->addFlash('success', 'admin.messages.edit.succes');
            return $this->redirectToReferer(['view' => 'show_message'], 'admin_messages', [ 'id' => $message->getId() ]);
        }

        return $this->render('admin/messages/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/{id}/remove", name="remove_message")
     */
    public function remove(Message $message, Request $request, TranslatorInterface $translator) {
        $this->denyAccessUnlessGranted(MessageVoter::Remove, $message);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => $translator->trans('admin.messages.remove.confirm', [
                '%name%' => $message->getTitle()
            ])
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($message);

            $this->addFlash('success', 'admin.messages.remove.success');

            return $this->redirectToRoute('admin_messages');
        }

        return $this->render('admin/messages/remove.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/{id}/downloads", name="message_downloads_admin")
     */
    public function downloads(Message $message, MessageFilesystem $filesystem) {
        return $this->render('admin/messages/downloads.html.twig', [
            'message' => $message,
            'csrf_token_name' => static::CsrfTokenName,
            'csrf_token_id' => static::CsrfTokenId,
            'files' => $filesystem->getAllUserDownloads($message)
        ]);
    }

    /**
     * @Route("/{id}/downloads/upload", name="upload_message_downloads")
     */
    public function uploadDownloads(Message $message, Request $request, MessageFilesystem $filesystem, UserRepositoryInterface $userRepository) {
        if($this->isCsrfTokenValid(static::CsrfTokenId, $request->request->get(static::CsrfTokenName)) !== true) {
            return new JsonResponse(
                [
                    'error' => 'CSRF token invalid.'
                ],
                Response::HTTP_BAD_REQUEST);
        }

        $fullPath = $request->request->get('path');

        if($fullPath === null) {
            return new JsonResponse(
                [
                    'error' => 'Drag and drop folders which names corresponds to users.'
                ],
                Response::HTTP_BAD_REQUEST);
        }

        $parts = explode('/', $fullPath);

        if(count($parts) < 2 ) {
            return new JsonResponse(
                [
                    'error' => 'Drag and drop folders which names corresponds to users.'
                ],
                Response::HTTP_BAD_REQUEST);
        }

        $username = $parts[0];
        $user = $userRepository->findOneByUsername($username);

        if($user === null) {
            return new JsonResponse(
                [
                    'error' => sprintf('User %s does not exist.', $username)
                ],
                Response::HTTP_BAD_REQUEST);
        }

        $filesystem->uploadUserDownload($message, $user, $request->files->get('file'));

        return new JsonResponse(
            [
                'files' => $this->renderView('admin/messages/files_explorer.html.twig', [
                    'files' => $filesystem->getAllUserDownloads($message),
                    'message' => $message
                ])
            ]
        );
    }

    /**
     * @Route("/{id}/uploads", name="message_uploads_admin")
     */
    public function uploads(Message $message, MessageFileUploadViewHelper $messageFileUploadViewHelper) {
        $view = $messageFileUploadViewHelper->createView($message);

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $this->grouper->group($students, StudentGradeStrategy::class);
        $this->sorter->sort($gradeGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($gradeGroups, StudentStrategy::class);

        $userGroups = $this->grouper->group($view->getUsers(), UserUserTypeStrategy::class);
        $this->sorter->sort($userGroups, UserUserTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($userGroups, UserLastnameFirstnameStrategy::class);

        return $this->render('admin/messages/uploads.html.twig', [
            'message' => $message,
            'teachers' => $teachers,
            'userGroups' => $userGroups,
            'grades' => $gradeGroups,
            'view' => $view
        ]);
    }

    /**
     * @Route("/{id}/uploads/download", name="download_message_upload")
     */
    public function downloadUploads(Message $message, string $path, MessageFilesystem $filesystem) {
        return $filesystem->getMessageUploadedFileDownloadResponse($message, $path);
    }
}