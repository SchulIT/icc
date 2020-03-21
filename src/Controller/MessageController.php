<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageConfirmation;
use App\Entity\MessageFile;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Filesystem\FileNotFoundException;
use App\Filesystem\MessageFilesystem;
use App\Form\MessageUploadType;
use App\Grouping\Grouper;
use App\Grouping\StudentStudyGroupStrategy;
use App\Grouping\UserUserTypeStrategy;
use App\Message\MessageConfirmationViewHelper;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\Voter\MessageVoter;
use App\Sorting\MessageStrategy;
use App\Sorting\Sorter;
use App\Sorting\StudentStrategy;
use App\Sorting\StudentStudyGroupGroupStrategy;
use App\Sorting\TeacherStrategy;
use App\Sorting\UserLastnameFirstnameStrategy;
use App\Sorting\UserUserTypeGroupStrategy;
use App\Utils\RefererHelper;
use App\View\Filter\StudentFilter;
use App\View\Filter\UserTypeFilter;
use Doctrine\ORM\EntityManagerInterface;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/messages")
 */
class MessageController extends AbstractController {

    private $sorter;
    private $dateHelper;

    public function __construct(Sorter $sorter, DateHelper $dateHelper, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
        
        $this->sorter = $sorter;
        $this->dateHelper = $dateHelper;
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Messages();
    }

    /**
     * @Route("", name="messages")
     */
    public function index(MessageRepositoryInterface $messageRepository, StudentFilter $studentFilter, UserTypeFilter $userTypeFilter,
                          ?int $studentId = null, ?string $userType = null, ?bool $archive = false) {
        /** @var User $user */
        $user = $this->getUser();

        $studentFilterView = $studentFilter->handle($studentId, $user);
        $userTypeFilterView = $userTypeFilter->handle($userType, $user);

        $studyGroups = [ ];
        if($userTypeFilterView->getCurrentType()->equals(UserType::Student()) || $userTypeFilterView->getCurrentType()->equals(UserType::Parent())) {
            if($studentFilterView->getCurrentStudent() !== null) {
                $studyGroups = $studentFilterView->getCurrentStudent()->getStudyGroupMemberships()->map(function(StudyGroupMembership $membership) {
                    return $membership->getStudyGroup();
                })->toArray();
            }
        }

        $messages = $messageRepository->findBy(
            MessageScope::Messages(),
            $userTypeFilterView->getCurrentType(),
            $this->dateHelper->getToday(),
            $studyGroups,
            $archive
        );

        $this->sorter->sort($messages, MessageStrategy::class);

        return $this->render('messages/index.html.twig', [
            'studentFilter' => $studentFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'messages' => $messages,
            'archive' => $archive
        ]);
    }

    /**
     * @Route("/{id}", name="show_message")
     */
    public function show(Message $message, MessageFilesystem $messageFilesystem, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(MessageUploadType::class, null, [
            'message' => $message
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var MessageFile[] $files */
            $files = $message->getFiles();

            foreach($files as $file) {
                $id = sprintf('file_%d', $file->getId());

                if($form->has($id)) {
                    /** @var UploadedFile $upload */
                    $upload = $form->get($id)->getData();

                    if($upload !== null && $upload->isValid()) {
                        $messageFilesystem->uploadFile($message, $user, $upload);
                    }
                }
            }

            return $this->redirectToRoute('show_message', [
                'id' => $message->getId()
            ]);
        }

        return $this->render('messages/show.html.twig', [
            'message' => $message,
            'downloads' => $messageFilesystem->getUserDownloads($message, $user),
            'uploads' => $messageFilesystem->getUserUploads($message, $user),
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{message}/attachments/{id}", name="download_message_attachment")
     */
    public function downloadAttachment(MessageAttachment $attachment, MessageFilesystem $messageFilesystem) {
        $this->denyAccessUnlessGranted(MessageVoter::View, $attachment->getMessage());

        try {
            return $messageFilesystem->getMessageAttachmentDownloadResponse($attachment);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/{id}/downloads/{filename}", name="download_user_file")
     */
    public function downloadUserFile(Message $message, string $filename, MessageFilesystem $messageFilesystem) {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(MessageVoter::View, $message);

        try {
            return $messageFilesystem->getMessageUserFileDownloadResponse($message, $user, $filename);
        } catch (FileNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/{id}/uploads/{filename}", name="download_uploaded_user_file")
     */
    public function downloadUploadedUserFile(Message $message, string $filename, MessageFilesystem $messageFilesystem) {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(MessageVoter::View, $message);

        try {
            return $messageFilesystem->getMessageUploadedUserFileDownloadResponse($message, $user, $filename);
        } catch (FileNotFoundException $e) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * @Route("/{id}/confirm", name="confirm_message")
     */
    public function confirm(Message $message, EntityManagerInterface $entityManager) {
        $this->denyAccessUnlessGranted(MessageVoter::Confirm, $message);

        /** @var User $user */
        $user = $this->getUser();

        $confirmations = $message->getConfirmations()
            ->filter(function(MessageConfirmation $confirmation) use ($user) {
                return $confirmation->getUser()->getId() === $user->getId();
            });

        if($confirmations->count() === 0) {
            $confirmation = (new MessageConfirmation())
                ->setMessage($message)
                ->setUser($user);

            $entityManager->persist($confirmation);
            $entityManager->flush();
        }

        return $this->redirectToRequestReferer('show_message', [ 'id' => $message->getId() ]);
    }

    /**
     * @Route("/{id}/dismiss", name="dismiss_message")
     */
    public function dismiss(Message $message, UserRepositoryInterface $userRepository) {
        /** @var User $user */
        $user = $this->getUser();

        if($user->getDismissedMessages()->contains($message) !== true) {
            $user->addDismissedMessage($message);
            $userRepository->persist($user);
        }

        return $this->redirectToRequestReferer('messages');
    }

    /**
     * @Route("/{id}/reenable", name="reenable_message")
     */
    public function reenable(Message $message, UserRepositoryInterface $userRepository) {
        /** @var User $user */
        $user = $this->getUser();

        if($user->getDismissedMessages()->contains($message) === true) {
            $user->removeDismissedMessage($message);
            $userRepository->persist($user);
        }

        return $this->redirectToRequestReferer('messages');
    }

    /**
     * @Route("/{id}/confirmations", name="message_confirmations")
     */
    public function confirmations(Message $message, MessageConfirmationViewHelper $confirmationViewHelper, Grouper $grouper) {
        $view = $confirmationViewHelper->createView($message);

        $teachers = $view->getTeachers();
        $this->sorter->sort($teachers, TeacherStrategy::class);

        $students = $view->getStudents();
        $studyGroups = $grouper->group($students, StudentStudyGroupStrategy::class);
        $this->sorter->sort($studyGroups, StudentStudyGroupGroupStrategy::class);
        $this->sorter->sortGroupItems($studyGroups, StudentStrategy::class);

        $userGroups = $grouper->group($view->getUsers(), UserUserTypeStrategy::class);
        $this->sorter->sort($userGroups, UserUserTypeGroupStrategy::class);
        $this->sorter->sortGroupItems($userGroups, UserLastnameFirstnameStrategy::class);

        return $this->render('messages/confirmations.html.twig', [
            'message' => $message,
            'teachers' => $teachers,
            'userGroups' => $userGroups,
            'studyGroups' => $studyGroups,
            'view' => $view
        ]);
    }
}