<?php

namespace App\Controller;

use App\Entity\ResourceEntity;
use App\Entity\ResourceReservation;
use App\Entity\Room;
use App\Entity\User;
use App\Form\ResourceReservationType;
use App\Grouping\Grouper;
use App\Grouping\RoomReservationWeekStrategy;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\ResourceRepositoryInterface;
use App\Repository\ResourceReservationRepositoryInterface;
use App\Repository\RoomTagRepositoryInterface;
use App\Rooms\Reservation\ResourceAvailabilityHelper;
use App\Rooms\RoomQueryBuilder;
use App\Rooms\Status\StatusHelperInterface;
use App\Security\Voter\ResourceReservationVoter;
use App\Settings\TimetableSettings;
use App\Sorting\ResourceStrategy;
use App\Sorting\RoomReservationDateStrategy;
use App\Sorting\RoomReservationWeekGroupStrategy;
use App\Sorting\Sorter;
use App\View\Filter\RoomFilter;
use App\View\Filter\TeacherFilter;
use DateTime;
use Exception;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Route("/reservations")
 * @Security("is_granted('view-reservations')")
 */
class ResourceReservationController extends AbstractController {

    private $repository;

    public function __construct(ResourceReservationRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="resource_reservations")
     */
    public function index(DateHelper $dateHelper, ResourceRepositoryInterface $resourceRepository, RoomQueryBuilder $queryBuilder, RoomTagRepositoryInterface  $roomTagRepository,
                          ResourceAvailabilityHelper $availabilityHelper, Sorter $sorter, Request $request, StatusHelperInterface $statusHelper, ImportDateTypeRepositoryInterface  $importDateTypeRepository) {
        $date = $this->getDateFromRequest($request, $dateHelper);
        $query = $queryBuilder->buildFromRequest($request);

        /** @var int|null $lessonStart */
        $lessonStart = $request->query->get('start', null);

        /** @var int|null $lessonEnd */
        $lessonEnd = $request->query->get('end', null);

        if($query->hasConditions()) {
            $resources = $resourceRepository->findAllByQuery($query);
        } else {
            $resources = $resourceRepository->findAll();
        }

        $sorter->sort($resources, ResourceStrategy::class);

        $resources = array_filter($resources, function(ResourceEntity $room) {
            return $room->isReservationEnabled();
        });

        $overview = $availabilityHelper->getAvailabilities($date, $resources);

        if($lessonStart !== null && $lessonEnd !== null) {
            if($lessonStart < $lessonEnd) {
                $resources = array_filter($resources, function (ResourceEntity $resource) use ($overview, $lessonStart, $lessonEnd) {
                    for ($lesson = $lessonStart; $lesson <= $lessonEnd; $lesson++) {
                        $availability = $overview->getAvailability($resource, $lessonEnd);

                        if ($availability->isAvailable() !== true) {
                            return false;
                        }
                    }

                    return true;
                });
            } else {
                $lessonEnd = null;
            }
        }

        $status = [ ];

        foreach($resources as $resource) {
            if($resource instanceof Room) {
                $status[$resource->getId()] = $statusHelper->getStatus($resource->getExternalId());
            }
        }

        return $this->render('reservations/index.html.twig', [
            'resources' => $resources,
            'date' => $date,
            'overview' => $overview,
            'status' => $status,
            'days' => $dateHelper->getListOfNextDays(7, $date),
            'query' => $query,
            'tags' => $roomTagRepository->findAll(),
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Room::class),
            'lesson_start' => $lessonStart,
            'lesson_end' => $lessonEnd,
            'serialized_query' => $queryBuilder->serialize($query)
        ]);
    }

    /**
     * @Route("/reservation", name="reservation_xhr")
     */
    public function reservation(DateHelper $dateHelper, ResourceRepositoryInterface $resourceRepository,
                                ResourceAvailabilityHelper $availabilityHelper, TimetableSettings $timetableSettings, Request $request) {
        $date = $this->getDateFromRequest($request, $dateHelper);
        $resource = $this->getResourceFromRequest($request, $resourceRepository);
        $lessonNumber = $request->query->getInt('lessonNumber', 0);

        $availability = $availabilityHelper->getAvailability($resource, $date, $lessonNumber);

        return $this->render('reservations/reservation.html.twig', [
            'availability' => $availability,
            'date' => $date,
            'resource' => $resource,
            'gradesWithCourseNames' => $timetableSettings->getGradeIdsWithCourseNames(),
        ]);
    }

    /**
     * @Route("/list", name="list_reservations")
     */
    public function list(RoomFilter $roomFilter, TeacherFilter $teacherFilter, DateHelper $dateHelper,
                          Sorter $sorter, Grouper $grouper, Request $request) {
        /** @var User $user */
        $user = $this->getUser();

        $roomsFilterView = $roomFilter->handle($request->query->get('room', null), $user);
        $teacherFilterView = $teacherFilter->handle($request->query->get('teacher', null), $user, !$request->query->has('teacher'));
        $room = $roomsFilterView->getCurrentRoom();
        $all = $request->query->get('all', null) === '✓';

        $reservations = $this->repository->findAllByRoomAndTeacher(
            $roomsFilterView->getCurrentRoom(),
            $teacherFilterView->getCurrentTeacher(),
            $all ? null : $dateHelper->getToday()
        );

        $groups = $grouper->group($reservations, RoomReservationWeekStrategy::class);
        $sorter->sort($groups, RoomReservationWeekGroupStrategy::class);
        $sorter->sortGroupItems($groups, RoomReservationDateStrategy::class);

        return $this->render('reservations/list.html.twig', [
            'groups' => $groups,
            'all' => $all,
            'teacherFilter' => $teacherFilterView,
            'roomFilter' => $roomsFilterView
        ]);
    }

    private function getDateFromRequest(Request $request, DateHelper $dateHelper): DateTime {
        $date = null;

        try {
            $date = new DateTime($request->query->get('date', null));
            $date->setTime(0, 0, 0);
        } catch (Exception $exception) { }

        if($date === null) {
            return $dateHelper->getToday();
        }

        return $date;
    }

    private function getResourceFromRequest(Request $request, ResourceRepositoryInterface $repository): ?ResourceEntity {
        $uuid = $request->query->get('resource', null);

        if(empty($uuid)) {
            return null;
        }

        return $repository->findOneByUuid($uuid);
    }

    /**
     * @Route("/add", name="add_room_reservation")
     */
    public function add(DateHelper $dateHelper, ResourceRepositoryInterface $resourceRepository, Request $request) {
        $this->denyAccessUnlessGranted(ResourceReservationVoter::New);

        $date = $this->getDateFromRequest($request, $dateHelper);
        $room = $this->getResourceFromRequest($request, $resourceRepository);

        $reservation = (new ResourceReservation())
            ->setLessonStart($request->query->getInt('lessonStart', 0))
            ->setLessonEnd($request->query->getInt('lessonStart', 0));

        if($date !== null) {
            $reservation->setDate($date);
        }
        if($room !== null) {
            $reservation->setResource($room);
        }

        if($this->getUser()->getTeacher() !== null) {
            $reservation->setTeacher($this->getUser()->getTeacher());
        }

        $form = $this->createForm(ResourceReservationType::class, $reservation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($reservation);
            $this->addFlash('success', 'resources.reservations.add.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('reservations/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_room_reservation")
     */
    public function edit(ResourceReservation $reservation, Request $request) {
        $this->denyAccessUnlessGranted(ResourceReservationVoter::Edit, $reservation);

        $form = $this->createForm(ResourceReservationType::class, $reservation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($reservation);
            $this->addFlash('success', 'resources.reservations.edit.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('reservations/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_room_reservation")
     */
    public function remove(ResourceReservation $reservation, TranslatorInterface $translator, Request $request) {
        $this->denyAccessUnlessGranted(ResourceReservationVoter::Remove, $reservation);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'resources.reservations.remove.confirm',
            'message_parameters' => [
                '%room%' => $reservation->getResource()->getName(),
                '%date%' => $reservation->getDate()->format($translator->trans('date.format')),
                '%lessons%' => $translator->trans('label.substitution_lessons', [
                    '%start%' => $reservation->getLessonStart(),
                    '%end%' => $reservation->getLessonEnd(),
                    '%count%' => ($reservation->getLessonEnd() - $reservation->getLessonStart())
                ])
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($reservation);
            $this->addFlash('success', 'resources.reservations.remove.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('reservations/remove.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

}