<?php

namespace App\Controller;

use App\Entity\Appointment as AppointmentEntity;
use App\Entity\Exam as ExamEntity;
use App\Entity\Message as MessageEntity;
use App\Entity\MessageAttachment;
use App\Entity\MessageConfirmation;
use App\Entity\MessageScope;
use App\Entity\Student;
use App\Entity\Student as StudentEntity;
use App\Entity\StudyGroup as StudyGroupEntity;
use App\Entity\StudyGroupMembership as StudyGroupMembershipEntity;
use App\Entity\Substitution as SubstitutionEntity;
use App\Entity\TimetableLesson as TimetableLessonEntity;
use App\Entity\TimetablePeriod as TimetablePeriodEntity;
use App\Entity\TimetableSupervision as TimetableSupervisionEntity;
use App\Entity\User as UserEntity;
use App\Entity\UserType;
use App\Filesystem\FileNotFoundException;
use App\Filesystem\MessageFilesystem;
use App\Repository\AppointmentRepositoryInterface;
use App\Repository\ExamRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\SubstitutionRepositoryInterface;
use App\Repository\TimetableLessonRepositoryInterface;
use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableSupervisionRepositoryInterface;
use App\Response\Api\V1\Appointment;
use App\Response\Api\V1\AppointmentList;
use App\Response\Api\V1\Exam;
use App\Response\Api\V1\ExamList;
use App\Response\Api\V1\Message;
use App\Response\Api\V1\MessageList;
use App\Response\Api\V1\Substitution;
use App\Response\Api\V1\SubstitutionList;
use App\Response\Api\V1\Timetable;
use App\Response\Api\V1\TimetableLesson;
use App\Response\Api\V1\TimetablePeriod;
use App\Response\Api\V1\TimetablePeriodList;
use App\Response\Api\V1\TimetableSupervision;
use App\Response\Api\V1\User;
use App\Security\Voter\AppointmentVoter;
use App\Security\Voter\ExamVoter;
use App\Security\Voter\MessageVoter;
use App\Security\Voter\SubstitutionVoter;
use App\Security\Voter\TimetablePeriodVoter;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/v1")
 */
class ApiV1Controller extends AbstractController {

    private $serializer;

    public function __construct(SerializerInterface $serializer) {
        $this->serializer = $serializer;
    }

    private function returnJson($data): Response {
        return new Response(
            $this->serializer->serialize($data, 'json'),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/json'
            ]
        );
    }

    /**
     * Get the profile of the current user.
     *
     * @Route("/profile", methods={"GET"})
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Returns the user object of the user which logged in.",
     *     @Model(type=User::class)
     * )
     * @Security(name="oauth")
     */
    public function profile() {
        /** @var UserEntity $user */
        $user = $this->getUser();

        return $this->returnJson(User::fromEntity($user));
    }

    /**
     * Get all exams for the current user.
     *
     * @Route("/exams", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_EXAMS")
     *
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of exams.",
     *     @Model(type=ExamList::class)
     * )
     * @SWG\Tag(name="exams")
     */
    public function exams(ExamRepositoryInterface $examRepository) {
        /** @var UserEntity $user */
        $user = $this->getUser();

        $exams = [];

        if ($user->getUserType()->equals(UserType::Teacher()) && $user->getTeacher() !== null) {
            $exams = $examRepository->findAllByTeacher($user->getTeacher());
        } else if ($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $exams = $examRepository->findAllByStudents($user->getStudents()->toArray());
        }

        $exams = array_filter($exams, function (\App\Entity\Exam $exam) {
            return $this->isGranted(ExamVoter::Show, $exam);
        });

        return $this->returnJson(
            (new ExamList())
                ->setExams(array_map(function (ExamEntity $exam) {
                    $supervisions = [ ];

                    if($this->isGranted(ExamVoter::Invigilators, $exam)) {
                        $supervisions = $exam->getInvigilators()->toArray();
                    }

                    return Exam::fromEntity($exam, $supervisions);
                }, $exams))
        );
    }

    /**
     * Get all messages for the current user.
     *
     * @Route("/messages", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_MESSAGES")
     *
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of messages.",
     *     @Model(type=MessageList::class)
     * )
     * @SWG\Tag(name="messages")
     */
    public function messages(MessageRepositoryInterface $messageRepository, DateHelper $dateHelper) {
        /** @var UserEntity $user */
        $user = $this->getUser();
        $studyGroups = $this->getStudyGroups($user);

        $messages = $messageRepository->findBy(MessageScope::Messages(), $user->getUserType(), $dateHelper->getToday(), $studyGroups, false);

        return $this->returnJson(
            (new MessageList())
            ->setMessages(array_map(function(MessageEntity $message) {
                return Message::fromEntity($message);
            }, $messages))
        );
    }

    /**
     * Confirm a message.
     *
     * @Route("/messages/{uuid}/confirm", methods={"POST"})
     * @IsGranted("ROLE_OAUTH2_MESSAGES")
     *
     * @SWG\Post()
     * @SWG\Response(
     *     response="204",
     *     description="Message was successfully confirmed."
     * )
     * @SWG\Response(
     *     response="403",
     *     description="User is not allowed to confirm the message."
     * )
     * @SWG\Response(
     *     response="404",
     *     description="The message was not found."
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="UUID of the message."
     * )
     * @SWG\Tag(name="messages")
     */
    public function confirmMessage(MessageEntity $message, EntityManagerInterface $entityManager) {
        $this->denyAccessUnlessGranted(MessageVoter::Confirm, $message);

        /** @var UserEntity $user */
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

        return new Response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * Download a message attachment.
     *
     * @Route("/messages/attachments/{uuid}", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_MESSAGES")
     *
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Attachment contents."
     * )
     * @SWG\Response(
     *     response="403",
     *     description="User is not allowed to download the attachment."
     * )
     * @SWG\Response(
     *     response="404",
     *     description="The attachment was not found."
     * )
     * @SWG\Parameter(
     *     name="uuid",
     *     in="path",
     *     type="string",
     *     description="UUID of the attachment."
     * )
     * @SWG\Tag(name="messages")
     */
    public function downloadAttachment(MessageAttachment $messageAttachment, MessageFilesystem $messageFilesystem) {
        $this->denyAccessUnlessGranted(MessageVoter::View, $messageAttachment->getMessage());

        try {
            return $messageFilesystem->getMessageAttachmentDownloadResponse($messageAttachment);
        } catch (FileNotFoundException $exception) {
            throw new NotFoundHttpException();
        }
    }

    /**
     * Get all substitutions for the current user.
     *
     * @Route("/substitutions", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_SUBSTITUTIONS")
     *
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of substitutions.",
     *     @Model(type=SubstitutionList::class)
     * )
     * @SWG\Tag(name="substitutions")
     */
    public function substitutions(SubstitutionRepositoryInterface $substitutionRepository, DateHelper $dateHelper, Request $request) {
        /** @var UserEntity $user */
        $user = $this->getUser();
        $studyGroups = $this->getStudyGroups($user);

        $substitutions = [ ];

        if ($user->getUserType()->equals(UserType::Student()) || $user->getUserType()->equals(UserType::Parent())) {
            $substitutions = $substitutionRepository->findAllForStudyGroups($this->getStudyGroups($user));
        } else {
            if($user->getTeacher() !== null && $request->query->getBoolean('all', false) === false) {
                $substitutions = $substitutionRepository->findAllForTeacher($user->getTeacher());
            } else {
                $substitutions = $substitutionRepository->findAll();
            }
        }

        $substitutions = array_filter($substitutions, function(SubstitutionEntity $substitution) {
            return $this->isGranted(SubstitutionVoter::View, $substitution);
        });

        return $this->returnJson(
            (new SubstitutionList())
            ->setSubstitutions(array_map(function(SubstitutionEntity $substitution) {
                return Substitution::fromEntity($substitution);
            }, $substitutions))
        );
    }

    /**
     * Gets all visible timetable periods for the current user.
     *
     * @Route("/timetable/periods", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_TIMETABLE")
     *
     * @SWG\Get()
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of timetable periods."
     * )
     * @SWG\Tag(name="timetable")
     */
    public function timetablePeriods(TimetablePeriodRepositoryInterface $periodRepository, Request $request) {
        $periods = array_filter(
            $periodRepository->findAll(),
            function(TimetablePeriodEntity $period) {
                return $this->isGranted(TimetablePeriodVoter::View, $period);
            });

        return $this->returnJson(
            (new TimetablePeriodList())
                ->setPeriods(array_map(function(TimetablePeriodEntity $period) {
                    return TimetablePeriod::fromEntity($period);
                }, $periods))
        );
    }

    /**
     * Get timetable lessons for the current user.
     *
     * Note about users with multiple students (e.g. parents):
     * By default, only the timetable of the first student is returned. You should specify the student
     * in the query string.
     *
     * @Route("/timetable/{uuid}", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_TIMETABLE")
     *
     * @SWG\Get()
     * @SWG\Parameter(
     *     in="path",
     *     name="uuid",
     *     type="string",
     *     description="UUID of the timetable period."
     * )
     * @SWG\Parameter(
     *     in="query",
     *     name="student",
     *     type="string",
     *     description="UUID of the student which the timetable should be returned for",
     *     required=false
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of timetable lessons and supervisions.",
     *     @Model(type=Timetable::class)
     * )
     * @SWG\Tag(name="timetable")
     */
    public function timetable(TimetablePeriodEntity $period, TimetableLessonRepositoryInterface $lessonRepository, TimetableSupervisionRepositoryInterface $supervisionRepository, Request $request) {
        /** @var UserEntity $user */
        $user = $this->getUser();
        $requestedStudentUuid = $request->query->get('student', null);

        $student = $user->getStudents()->count() > 0 ? $user->getStudents()->first() : null;

        if(!empty($requestedStudentUuid) && $user->getStudents()->count() > 1) {
            /** @var Student $s */
            foreach($user->getStudents() as $s) {
                if($s->getUuid()->toString() === $requestedStudentUuid) {
                    $student = $s;
                    break;
                }
            }
        }

        $lessons = [ ];
        $supervisions = [ ];

        if($student !== null) {
            $lessons = $lessonRepository->findAllByPeriodAndStudent($period, $student);
        } else if($user->getTeacher() !== null) {
            $lessons = $lessonRepository->findAllByPeriodAndTeacher($period, $user->getTeacher());
            $supervisions = $supervisionRepository->findAllByPeriodAndTeacher($period, $user->getTeacher());
        }

        return $this->returnJson(
            (new Timetable())
                ->setPeriod(TimetablePeriod::fromEntity($period))
                ->setLessons(array_map(function(TimetableLessonEntity $lesson) {
                    return TimetableLesson::fromEntity($lesson);
                }, $lessons))
                ->setSupervisions(array_map(function(TimetableSupervisionEntity $supervision) {
                    return TimetableSupervision::fromEntity($supervision);
                }, $supervisions))
        );
    }

    /**
     * Get appointments for the current user.
     *
     * Note about users with multiple students (e.g. parents):
     * By default, only the appointments of the first student is returned. You should specify the student
     * in the query string.
     *
     * @Route("/appointments", methods={"GET"})
     * @IsGranted("ROLE_OAUTH2_APPOINTMENTS")
     *
     * @SWG\Get()
     * @SWG\Parameter(
     *     in="query",
     *     name="student",
     *     type="string",
     *     description="UUID of the student which the appointments should be returned for",
     *     required=false
     * )
     * @SWG\Response(
     *     response="200",
     *     description="Returns a list of appointments.",
     *     @Model(type=AppointmentList::class)
     * )
     * @SWG\Tag(name="appointments")
     */
    public function appointments(AppointmentRepositoryInterface $appointmentRepository, Request $request) {
        /** @var UserEntity $user */
        $user = $this->getUser();
        $requestedStudentUuid = $request->query->get('student', null);

        $student = $user->getStudents()->count() > 0 ? $user->getStudents()->first() : null;

        if(!empty($requestedStudentUuid) && $user->getStudents()->count() > 1) {
            /** @var Student $s */
            foreach($user->getStudents() as $s) {
                if($s->getUuid()->toString() === $requestedStudentUuid) {
                    $student = $s;
                    break;
                }
            }
        }

        $appointments = [ ];

        if($student !== null) {
            $appointments = $appointmentRepository->findAllForStudents([$student]);
        } else if($user->getTeacher() !== null) {
            $appointments = $appointmentRepository->findAllForTeacher($user->getTeacher());
        } else {
            $appointments = $appointmentRepository->findAll();
        }

        $appointments = array_filter($appointments, function(AppointmentEntity $appointment) {
            return $this->isGranted(AppointmentVoter::View, $appointment);
        });

        return $this->returnJson(
            (new AppointmentList())
                ->setAppointments(array_map(function(AppointmentEntity $appointment) {
                    return Appointment::fromEntity($appointment);
                }, $appointments))
        );
    }

    /**
     * @param UserEntity $user
     * @return StudyGroupEntity[]
     */
    private function getStudyGroups(UserEntity $user): array {
        $studyGroups = [ ];

        /** @var StudentEntity $student */
        foreach($user->getStudents() as $student) {
            /** @var StudyGroupMembershipEntity $membership */
            foreach($student->getStudyGroupMemberships() as $membership) {
                if(!in_array($membership->getStudyGroup(), $studyGroups)) {
                    $studyGroups[] = $membership->getStudyGroup();
                }
            }
        }

        return $studyGroups;
    }
}