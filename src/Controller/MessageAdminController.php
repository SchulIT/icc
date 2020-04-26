<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\User;
use App\Event\MessageUpdatedEvent;
use App\Filesystem\MessageFilesystem;
use App\Form\MessageType;
use App\Grouping\Grouper;
use App\Grouping\MessageExpirationGroup;
use App\Grouping\MessageExpirationStrategy;
use App\Grouping\StudentGradeStrategy;
use App\Grouping\StudentStudyGroupStrategy;
use App\Grouping\UserUserTypeStrategy;
use App\Message\MessageDownloadView;
use App\Message\MessageDownloadViewHelper;
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
use App\View\Filter\UserTypeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use SchoolIT\CommonBundle\Form\ConfirmType;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
    public function index(UserTypeFilter $userTypeFilter, Request $request) {
        $this->denyAccessUnlessGranted('ROLE_MESSAGE_CREATOR');

        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null));
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
     * @Route("/{uuid}/edit", name="edit_message")
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
            return $this->redirectToReferer(['view' => 'show_message'], 'admin_messages', [ 'uuid' => $message->getUuid() ]);
        }

        return $this->render('admin/messages/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_message")
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
     * @Route("/{uuid}/downloads", name="message_downloads_admin")
     */
    public function downloads(Message $message, MessageDownloadViewHelper $messageDownloadViewHelper) {
        /** @var MessageDownloadView $view */
        $view = $messageDownloadViewHelper->createView($message);

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $this->grouper->group($students, StudentGradeStrategy::class);
        $this->sorter->sort($gradeGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($gradeGroups, StudentStrategy::class);

        $userGroups = $this->grouper->group($view->getUsers(), UserUserTypeStrategy::class);
        $this->sorter->sort($userGroups, UserUserTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($userGroups, UserLastnameFirstnameStrategy::class);

        $statistics = [ ];
        $users = $view->getUsers();

        foreach($users as $user) {
            $files = $view->getUserDownloads($user);
            $count = count($files);

            if(!isset($statistics[$count])) {
                $statistics[$count] = 0;
            }

            $statistics[$count]++;
        }

        return $this->render('admin/messages/downloads.html.twig', [
            'message' => $message,
            'teachers' => $teachers,
            'userGroups' => $userGroups,
            'grades' => $gradeGroups,
            'view' => $view,
            'statistics' => $statistics,
            'csrf_token_name' => static::CsrfTokenName,
            'csrf_token_id' => static::CsrfTokenId
        ]);
    }

    /**
     * @Route("/{message}/downloads/{user}/{filename}/download", name="download_message_download")
     * @ParamConverter("message", class="App\Entity\Message", options={"uuid" = "message"})
     * @ParamConverter("user", class="App\Entity\User", options={"uuid" = "user"})
     */
    public function downloadDownload(Message $message, User $user, string $filename, MessageFilesystem $messageFilesystem) {
        return $messageFilesystem->getMessageUserFileDownloadResponse($message, $user, $filename);
    }

    /**
     * @Route("/{message}/downloads/{user}/{filename}/remove", name="remove_message_download")
     * @ParamConverter("message", class="App\Entity\Message", options={"uuid" = "message"})
     * @ParamConverter("user", class="App\Entity\User", options={"uuid" = "user"})
     */
    public function removeDownload(Message $message, User $user, string $filename, MessageFilesystem $messageFilesystem, Request $request) {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'messages.downloads.remove.confirm',
            'message_parameters' => [
                '%user%' => $user->getUsername(),
                '%filename%' => $filename
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $messageFilesystem->removeUserFileDownload($message, $user, $filename);

            $this->addFlash('success', 'messages.downloads.remove.success');
            return $this->redirectToRoute('message_downloads_admin', [
                'uuid' => $message->getUuid()
            ]);
        }

        return $this->render('admin/messages/remove_download.html.twig', [
            'form' => $form->createView(),
            'message' => $message,
            'user' => $user,
            'filename' => $filename
        ]);
    }

    /**
     * @Route("/{message}/downloads/upload/{user}", name="upload_message_download")
     * @ParamConverter("message", class="App\Entity\Message", options={"uuid" = "message"})
     * @ParamConverter("user", class="App\Entity\User", options={"uuid" = "user"})
     */
    public function uploadDownload(Message $message, User $user, Request $request, MessageFilesystem $filesystem, UserRepositoryInterface $userRepository) {
        if($this->isCsrfTokenValid(static::CsrfTokenId, $request->request->get(static::CsrfTokenName)) !== true) {
            return new JsonResponse(
                [
                    'error' => 'CSRF token invalid.'
                ],
                Response::HTTP_BAD_REQUEST);
        }

        /** @var UploadedFile $file */
        $file = $request->files->get('file');
        $filesystem->uploadUserDownload($message, $user, $file);
        $fileInfo = $filesystem->getUserDownload($message, $user, $file->getClientOriginalName());
        $fileInfo['basename'] = basename($fileInfo['path']);

        $response = $this->renderView('admin/messages/file_explorer_file.html.twig', [
            'user' => $user,
            'file' => $fileInfo,
            'message' => $message
        ]);

        return new JsonResponse([
            'success' => true,
            'file' => $response
        ]);
    }

    /**
     * @Route("/{uuid}/downloads/upload", name="upload_message_downloads")
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
                'success' => true
            ]
        );
    }

    /**
     * @Route("/{uuid}/uploads", name="message_uploads_admin")
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
     * @Route("/{uuid}/uploads/download", name="download_message_upload")
     */
    public function downloadUploads(Message $message, MessageFilesystem $filesystem, Request $request) {
        $path = $request->query->get('path', null);

        if($path === null) {
            throw new NotFoundHttpException();
        }

        return $filesystem->getMessageUploadedFileDownloadResponse($message, $path);
    }
}