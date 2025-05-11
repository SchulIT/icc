<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\MessageAttachment;
use App\Entity\MessageConfirmation;
use App\Entity\MessageFile;
use App\Entity\MessageFileUpload;
use App\Entity\MessageScope;
use App\Entity\StudyGroupMembership;
use App\Entity\User;
use App\Entity\UserType;
use App\Feature\Feature;
use App\Feature\IsFeatureEnabled;
use App\Filesystem\FileNotFoundException;
use App\Filesystem\MessageFilesystem;
use App\Form\MessagePollVoteType;
use App\Form\MessageUploadType;
use App\Grouping\Grouper;
use App\Grouping\MessageWeekStrategy;
use App\Message\PollVoteHelper;
use App\Repository\MessageFileUploadRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\MessageVoter;
use App\Sorting\MessageStrategy;
use App\Sorting\MessageWeekGroupStrategy;
use App\Sorting\SortDirection;
use App\Sorting\Sorter;
use App\Utils\ArrayUtils;
use App\View\Filter\StudentFilter;
use App\View\Filter\UserTypeFilter;
use Doctrine\ORM\EntityManagerInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/messages')]
#[IsFeatureEnabled(Feature::Messages)]
class MessageController extends AbstractController {

    private const MessagesPerPage = 25;

    public function __construct(private Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Messages;
    }

    #[Route(path: '', name: 'messages')]
    public function index(MessageRepositoryInterface $messageRepository, StudentFilter $studentFilter, UserTypeFilter $userTypeFilter,
                          SectionResolverInterface $sectionResolver, Request $request, Grouper $grouper): Response {
        /** @var User $user */
        $user = $this->getUser();

        $query = $request->query->get('q');
        $studentFilterView = $studentFilter->handle($request->query->get('student', null), $sectionResolver->getCurrentSection(), $user);
        $userTypeFilterView = $userTypeFilter->handle($request->query->get('user_type', null), $user, $user->isStudentOrParent());

        if($studentFilterView->getCurrentStudent() !== null && ArrayUtils::inArray($userTypeFilterView->getCurrentType(), [ UserType::Student, UserType::Parent ]) === false) {
            $userTypeFilterView->setCurrentType(UserType::Student);
        }

        $studyGroups = [ ];
        if($userTypeFilterView->getCurrentType() === UserType::Student || $userTypeFilterView->getCurrentType() === UserType::Parent) {
            if($studentFilterView->getCurrentStudent() !== null) {
                $studyGroups = $studentFilterView->getCurrentStudent()->getStudyGroupMemberships()->map(fn(StudyGroupMembership $membership) => $membership->getStudyGroup())->toArray();
            }
        }

        $page = $request->query->getInt('page', 1);

        $paginator = $messageRepository->getPaginator(
            self::MessagesPerPage,
            $page,
            MessageScope::Messages,
            $userTypeFilterView->getCurrentType(),
            null,
            $studyGroups,
            $query
        );

        $messages = [ ];

        foreach($paginator as $message) {
            $messages[] = $message;
        }

        $messages = array_filter($messages, fn(Message $message) => $this->isGranted(MessageVoter::View, $message));

        $pages = ceil((double)$paginator->count() / self::MessagesPerPage);

        $groups = $grouper->group($messages, MessageWeekStrategy::class);
        $this->sorter->sort($groups, MessageWeekGroupStrategy::class, SortDirection::Descending);
        $this->sorter->sortGroupItems($groups, MessageStrategy::class);

        return $this->render('messages/index.html.twig', [
            'studentFilter' => $studentFilterView,
            'userTypeFilter' => $userTypeFilterView,
            'groups' => $groups,
            'page' => $page,
            'pages' => $pages,
            'query' => $query
        ]);
    }

    #[Route(path: '/{uuid}', name: 'show_message')]
    public function show(#[MapEntity(mapping: ['uuid' => 'uuid'])] Message $message, MessageRepositoryInterface $messageRepository,
                         MessageFileUploadRepositoryInterface $fileUploadRepository, MessageFilesystem $messageFilesystem,
                         PollVoteHelper $voteHelper, Request $request, DateHelper $dateHelper): Response {
        // Requery message for better performance
        $message = $messageRepository->findOneById($message->getId());

        $this->denyAccessUnlessGranted(MessageVoter::View, $message);

        /** @var User $user */
        $user = $this->getUser();

        /** @var MessageFileUpload[] $uploads */
        $uploads = [ ];

        /** @var MessageFile $file */
        foreach($message->getFiles() as $file) {
            $fileUpload = $fileUploadRepository->findOneByFileAndUser($file, $user);

            if($fileUpload === null) {
                $fileUpload = (new MessageFileUpload())
                    ->setUser($user)
                    ->setMessageFile($file);
            }

            $uploads[] = $fileUpload;
        }

        $form = $this->createForm(MessageUploadType::class, [
            'uploads' => $uploads
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            foreach($uploads as $upload) {
                if($upload->getFile() !== null) {
                    $fileUploadRepository->persist($upload);
                }
            }

            return $this->redirectToRoute('show_message', [
                'uuid' => $message->getUuid()
            ]);
        }

        $missing = array_filter($uploads, fn(MessageFileUpload $upload) => $upload->isUploaded() === false);

        $votes = [ ];
        $voteForms = [ ];
        $students = [ ];
        $allowVote = $dateHelper->getToday() <= $message->getExpireDate();

        if($user->isStudentOrParent()) {
            foreach ($user->getStudents() as $student) {
                $vote = $voteHelper->getPollVote($message, $user, $student);
                $rankedChoices = $voteHelper->getRankedChoices($vote);
                $voteForm = $this->container->get('form.factory')->createNamedBuilder('poll_' . $student->getId(), MessagePollVoteType::class, $rankedChoices, [
                    'choices' => $message->getPollChoices(),
                    'num_choices' => $message->getPollNumChoices(),
                    'allow_vote' => $allowVote
                ])->getForm();
                $voteForm->handleRequest($request);

                if ($voteForm->isSubmitted() && $voteForm->isValid() && $allowVote) {
                    $voteHelper->persistVote($message, $user, $voteForm->getData(), $student);
                    return $this->redirectToRoute('show_message', [
                        'uuid' => $message->getUuid()
                    ]);
                }

                $votes[$student->getId()] = $vote;
                $voteForms[$student->getId()] = $voteForm->createView();
                $students[$student->getId()] = $student;
            }
        } else {
            $vote = $voteHelper->getPollVote($message, $user);
            $rankedChoices = $voteHelper->getRankedChoices($vote);

            $voteForm = $this->createForm(MessagePollVoteType::class, $rankedChoices, [
                'choices' => $message->getPollChoices(),
                'num_choices' => $message->getPollNumChoices(),
                'allow_vote' => $allowVote
            ]);
            $voteForm->handleRequest($request);

            if ($voteForm->isSubmitted() && $voteForm->isValid() && $allowVote) {
                $voteHelper->persistVote($message, $user, $voteForm->getData());
                return $this->redirectToRoute('show_message', [
                    'uuid' => $message->getUuid()
                ]);
            }

            $votes[] = $vote;
            $voteForms[] = $voteForm->createView();
        }

        return $this->render('messages/show.html.twig', [
            'message' => $message,
            'downloads' => $messageFilesystem->getUserDownloads($message, $user),
            'uploads' => $uploads,
            'missing' => $missing,
            'form' => $form->createView(),
            'voteForms' => $voteForms,
            'votes' => $votes,
            'students' => $students,
            'allow_vote' => $allowVote
        ]);
    }

    #[Route(path: '/attachments/{uuid}', name: 'download_message_attachment')]
    public function downloadAttachment(#[MapEntity(mapping: ['uuid' => 'uuid'])] MessageAttachment $attachment, MessageFilesystem $messageFilesystem): Response {
        $this->denyAccessUnlessGranted(MessageVoter::View, $attachment->getMessage());

        try {
            return $messageFilesystem->getMessageAttachmentDownloadResponse($attachment);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/{uuid}/downloads/{filename}', name: 'download_user_file')]
    public function downloadUserFile(#[MapEntity(mapping: ['uuid' => 'uuid'])] Message $message, string $filename, MessageFilesystem $messageFilesystem): Response {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(MessageVoter::View, $message);
        $this->denyAccessUnlessGranted(MessageVoter::Download, $message);

        try {
            return $messageFilesystem->getMessageUserFileDownloadResponse($message, $user, $filename);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/uploads/{uuid}/download', name: 'download_uploaded_user_file')]
    public function downloadUploadedUserFile(#[MapEntity(mapping: ['uuid' => 'uuid'])] MessageFile $file, MessageFileUploadRepositoryInterface $fileUploadRepository, MessageFilesystem $messageFilesystem): Response {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(MessageVoter::View, $file->getMessage());
        $this->denyAccessUnlessGranted(MessageVoter::Upload, $file->getMessage());

        $fileUpload = $fileUploadRepository->findOneByFileAndUser($file, $user);

        if($fileUpload === null) {
            throw new NotFoundHttpException();
        }

        try {
            return $messageFilesystem->getMessageUploadedUserFileDownloadResponse($fileUpload, $user);
        } catch (FileNotFoundException) {
            throw new NotFoundHttpException();
        }
    }

    #[Route(path: '/uploads/{uuid}/remove', name: 'remove_uploaded_user_file')]
    public function removeUploadedUserFile(#[MapEntity(mapping: ['uuid' => 'uuid'])] MessageFile $file, MessageFileUploadRepositoryInterface $fileUploadRepository, MessageFilesystem $filesystem, Request $request): Response {
        /** @var User $user */
        $user = $this->getUser();
        $this->denyAccessUnlessGranted(MessageVoter::View, $file->getMessage());
        $this->denyAccessUnlessGranted(MessageVoter::Upload, $file->getMessage());

        $fileUpload = $fileUploadRepository->findOneByFileAndUser($file, $user);

        if($fileUpload === null) {
            throw new NotFoundHttpException();
        }

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'messages.uploads.remove.confirm',
            'message_parameters' => [
                '%filename%' => $fileUpload->getFilename(),
                '%label%' => $file->getLabel()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            try {
                $fileUploadRepository->remove($fileUpload);
            } catch (FileNotFoundException) {
                throw new NotFoundHttpException();
            }

            $this->addFlash('success', 'messages.uploads.remove.success');
            return $this->redirectToRoute('show_message', [
                'uuid' => $file->getMessage()->getUuid()
            ]);
        }

        return $this->render('messages/remove_uploaded_file.html.twig', [
            'message' => $file->getMessage(),
            'file' => $file,
            'upload' => $fileUpload,
            'form' => $form->createView()
        ]);
    }

    #[Route(path: '/{uuid}/confirm', name: 'confirm_message')]
    public function confirm(#[MapEntity(mapping: ['uuid' => 'uuid'])] Message $message, EntityManagerInterface $entityManager): Response {
        $this->denyAccessUnlessGranted(MessageVoter::Confirm, $message);

        /** @var User $user */
        $user = $this->getUser();

        $confirmations = $message->getConfirmations()
            ->filter(fn(MessageConfirmation $confirmation) => $confirmation->getUser()->getId() === $user->getId());

        if($confirmations->count() === 0) {
            $confirmation = (new MessageConfirmation())
                ->setMessage($message)
                ->setUser($user);

            $entityManager->persist($confirmation);
            $entityManager->flush();
        }

        return $this->redirectToRequestReferer('show_message', [ 'uuid' => $message->getUuid() ]);
    }

    #[Route(path: '/{uuid}/dismiss', name: 'dismiss_message')]
    public function dismiss(#[MapEntity(mapping: ['uuid' => 'uuid'])] Message $message, UserRepositoryInterface $userRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        if($user->getDismissedMessages()->contains($message) !== true) {
            $user->addDismissedMessage($message);
            $userRepository->persist($user);
        }

        return $this->redirectToRequestReferer('messages');
    }

    #[Route(path: '/{uuid}/reenable', name: 'reenable_message')]
    public function reenable(#[MapEntity(mapping: ['uuid' => 'uuid'])] Message $message, UserRepositoryInterface $userRepository): Response {
        /** @var User $user */
        $user = $this->getUser();

        if($user->getDismissedMessages()->contains($message) === true) {
            $user->removeDismissedMessage($message);
            $userRepository->persist($user);
        }

        return $this->redirectToRequestReferer('messages');
    }
    
}