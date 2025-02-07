<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageFile;
use App\Entity\User;
use App\Entity\UserType;
use App\Entity\UserTypeEntity;
use App\Event\MessageUpdatedEvent;
use App\Export\PollResultCsvExporter;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Filesystem\FileNotFoundException;
use App\Filesystem\MessageFilesystem;
use App\Form\MessageType;
use App\Grouping\Grouper;
use App\Grouping\MessageExpirationGroup;
use App\Grouping\MessageExpirationStrategy;
use App\Grouping\StudentGradeStrategy;
use App\Grouping\UserUserTypeStrategy;
use App\Message\MessageConfirmationViewHelper;
use App\Message\MessageDownloadView;
use App\Message\MessageDownloadViewHelper;
use App\Message\MessageFileUploadViewHelper;
use App\Message\PollResultViewHelper;
use App\Repository\MessageFileUploadRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Request\Message\RemoveMessageRequest;
use App\Section\SectionResolverInterface;
use App\Security\Voter\MessageVoter;
use App\Sorting\MessageExpirationGroupStrategy;
use App\Sorting\MessageExpiryDateStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Sorting\StudentGradeGroupStrategy;
use App\Sorting\StudentStrategy;
use App\Sorting\TeacherStrategy;
use App\Sorting\UserLastnameFirstnameStrategy;
use App\Sorting\UserUserTypeGroupStrategy;
use App\View\Filter\GradeFilter;
use App\View\Filter\UserTypeFilter;
use Doctrine\Common\Collections\ArrayCollection;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(path: '/admin/messages')]
#[IsFeatureEnabled(Feature::Messages)]
class MessageAdminController extends AbstractController {

    private const CsrfTokenName = '_csrf_token';
    private const CsrfTokenId = 'message_files';

    public function __construct(private MessageRepositoryInterface $repository, private Grouper $grouper, private Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    #[Route(path: '', name: 'admin_messages')]
    public function index(UserTypeFilter $userTypeFilter, GradeFilter $gradeFilter, Request $request): Response {
        $this->denyAccessUnlessGranted('ROLE_MESSAGE_CREATOR');

        /** @var User $user */
        $user = $this->getUser();

        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null));
        $userTypeFilterView->setHandleNull(true);
        $onlyOwn = $request->query->get('all') !== '✓';

        $gradeFilterView = $gradeFilter->handle($request->query->get('grade', null), null, $user);

        if($userTypeFilterView->getCurrentType() !== null) {
            $messages = $this->repository->findAllByUserType($userTypeFilterView->getCurrentType(), $onlyOwn ? $user : null);
        } else if($gradeFilterView->getCurrentGrade() !== null) {
            $messages = $this->repository->findAllByGrade($gradeFilterView->getCurrentGrade(), $onlyOwn ? $user : null);
        } else if($onlyOwn === true) {
            $messages = $this->repository->findAllByAuthor($user);
        } else {
            $messages = $this->repository->findAll();
        }

        /** @var MessageExpirationGroup[] $groups */
        $groups = $this->grouper->group($messages, MessageExpirationStrategy::class);
        $this->sorter->sort($groups, MessageExpirationGroupStrategy::class);
        $this->sorter->sortGroupItems($groups, MessageExpiryDateStrategy::class, SortDirection::Descending);

        return $this->render('admin/messages/index.html.twig', [
            'groups' => $groups,
            'userTypeFilter' => $userTypeFilterView,
            'gradeFilter' => $gradeFilterView,
            'onlyOwn' => $onlyOwn
        ]);
    }

    #[Route(path: '/add', name: 'add_message')]
    public function add(Request $request, DateHelper $dateHelper): Response {
        $this->denyAccessUnlessGranted(MessageVoter::New);

        $message = (new Message())
            ->setStartDate($dateHelper->getToday())
            ->setExpireDate($dateHelper->getToday()->modify('+7 days'));
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

    #[Route(path: '/{uuid}/edit', name: 'edit_message')]
    public function edit(Request $request, Message $message, EventDispatcherInterface $eventDispatcher): Response {
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

            $this->addFlash('success', 'admin.messages.edit.success');
            return $this->redirectToReferer(['view' => 'show_message'], 'admin_messages', [ 'uuid' => $message->getUuid() ]);
        }

        return $this->render('admin/messages/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message
        ]);
    }

    #[Route(path: '/{uuid}/remove', name: 'remove_message')]
    public function remove(Message $message, Request $request, TranslatorInterface $translator): Response {
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

    #[Route(path: '/{uuid}/confirmations', name: 'message_confirmations')]
    public function confirmations(Message $message, MessageConfirmationViewHelper $confirmationViewHelper, Grouper $grouper, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);
        $view = $confirmationViewHelper->createView($message);

        $section = $sectionResolver->getCurrentSection();

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $grouper->group($students, StudentGradeStrategy::class, [ 'section' => $section ]);
        $this->sorter->sort($gradeGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($gradeGroups, StudentStrategy::class);

        $userGroups = $grouper->group($view->getUsers(), UserUserTypeStrategy::class);
        $this->sorter->sort($userGroups, UserUserTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($userGroups, UserLastnameFirstnameStrategy::class);

        $studentsRequired = false;
        $parentsRequired = false;

        foreach($message->getConfirmationRequiredUserTypes() as $userTypeEntity) {
            if($userTypeEntity->getUserType() === UserType::Student) {
                $studentsRequired = true;
            }

            if($userTypeEntity->getUserType() === UserType::Parent) {
                $parentsRequired = true;
            }
        }

        return $this->render('admin/messages/confirmations.html.twig', [
            'message' => $message,
            'studentsRequired' => $studentsRequired,
            'parentsRequired' => $parentsRequired,
            'teachers' => $teachers,
            'userGroups' => $userGroups,
            'grades' => $gradeGroups,
            'view' => $view
        ]);
    }

    #[Route(path: '/{uuid}/downloads', name: 'message_downloads_admin')]
    public function downloads(Message $message, MessageDownloadViewHelper $messageDownloadViewHelper, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        /** @var MessageDownloadView $view */
        $view = $messageDownloadViewHelper->createView($message);

        $section = $sectionResolver->getCurrentSection();

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $this->grouper->group($students, StudentGradeStrategy::class, [ 'section' => $section ]);
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
            'csrf_token_name' => self::CsrfTokenName,
            'csrf_token_id' => self::CsrfTokenId
        ]);
    }

    #[Route(path: '/{message}/downloads/{user}/{filename}/download', name: 'download_message_download')]
    #[ParamConverter('message', class: Message::class, options: ['mapping' => ['message' => 'uuid']])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['user' => 'uuid']])]
    public function downloadDownload(Message $message, User $user, string $filename, MessageFilesystem $messageFilesystem): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);
        return $messageFilesystem->getMessageUserFileDownloadResponse($message, $user, $filename);
    }

    #[Route(path: '/{message}/downloads/{user}/{filename}/remove', name: 'remove_message_download')]
    #[ParamConverter('message', class: Message::class, options: ['mapping' => ['message' => 'uuid']])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['user' => 'uuid']])]
    public function removeDownload(Message $message, User $user, string $filename, MessageFilesystem $messageFilesystem, Request $request): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

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

    #[Route(path: '/{message}/downloads/upload/{user}', name: 'upload_message_download')]
    #[ParamConverter('message', class: Message::class, options: ['mapping' => ['message' => 'uuid']])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['user' => 'uuid']])]
    public function uploadDownload(Message $message, User $user, Request $request, MessageFilesystem $filesystem, UserRepositoryInterface $userRepository): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        if($this->isCsrfTokenValid(self::CsrfTokenId, $request->request->get(self::CsrfTokenName)) !== true) {
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

    #[Route(path: '/{uuid}/downloads/upload', name: 'upload_message_downloads')]
    public function uploadDownloads(Message $message, Request $request, MessageFilesystem $filesystem, UserRepositoryInterface $userRepository): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        if($this->isCsrfTokenValid(self::CsrfTokenId, $request->request->get(self::CsrfTokenName)) !== true) {
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

    #[Route(path: '/{uuid}/uploads', name: 'message_uploads_admin')]
    public function uploads(Message $message, MessageFileUploadViewHelper $messageFileUploadViewHelper, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        $view = $messageFileUploadViewHelper->createView($message);

        $section = $sectionResolver->getCurrentSection();

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $this->grouper->group($students, StudentGradeStrategy::class, [ 'section' => $section ]);
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

    #[Route(path: '/{message}/uploads/download/{file}/{user}', name: 'download_message_upload')]
    #[ParamConverter('message', class: Message::class, options: ['mapping' => ['message' => 'uuid']])]
    #[ParamConverter('file', class: MessageFile::class, options: ['mapping' => ['file' => 'uuid']])]
    #[ParamConverter('user', class: User::class, options: ['mapping' => ['user' => 'uuid']])]
    public function downloadUploads(Message $message, MessageFile $file, User $user,
                                    MessageFilesystem $filesystem, MessageFileUploadRepositoryInterface $fileUploadRepository): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        $fileUpload = $fileUploadRepository->findOneByFileAndUser($file, $user);

        if($fileUpload === null) {
            throw new NotFoundHttpException();
        }

        try {
            return $filesystem->getMessageUploadedUserFileDownloadResponse($fileUpload, $user);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/{uuid}/poll', name: 'poll_result')]
    public function pollResult(Message $message, PollResultViewHelper $resultViewHelper, SectionResolverInterface $sectionResolver): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);
        $view = $resultViewHelper->createView($message);

        $section = $sectionResolver->getCurrentSection();

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $gradeGroups = $this->grouper->group($students, StudentGradeStrategy::class, [ 'section' => $section ]);
        $this->sorter->sort($gradeGroups, StudentGradeGroupStrategy::class);
        $this->sorter->sortGroupItems($gradeGroups, StudentStrategy::class);

        $isPollEnabledForStudents = $message->getPollUserTypes()->filter(fn(UserTypeEntity $entity) => $entity->getUserType() === UserType::Student)->count() === 1;
        $isPollEnabledForParents = $message->getPollUserTypes()->filter(fn(UserTypeEntity $entity) => $entity->getUserType() === UserType::Student)->count() === 1;

        return $this->render('admin/messages/poll_result.html.twig', [
            'view' => $view,
            'grades' => $gradeGroups,
            'teachers' => $teachers,
            'message' => $message,
            'isPollEnabledForStudents' => $isPollEnabledForStudents,
            'isPollEnabledForParents' => $isPollEnabledForParents
        ]);
    }

    #[Route(path: '/{uuid}/poll/export', name: 'export_poll_result')]
    public function exportPollResult(Message $message, PollResultCsvExporter $exporter): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Edit, $message);

        return $exporter->getCsvResponse($message);
    }


    #[Route(path: '/{uuid}/remove/xhr', name: 'xhr_remove_message')]
    public function removeXhr(Message $message, RemoveMessageRequest $request): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Remove, $message);
        $this->repository->remove($message);

        return new JsonResponse();
    }
}