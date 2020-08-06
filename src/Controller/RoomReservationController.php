<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\RoomReservation;
use App\Entity\User;
use App\Form\RoomReservationType;
use App\Grouping\Grouper;
use App\Grouping\RoomReservationWeekStrategy;
use App\Repository\RoomRepositoryInterface;
use App\Repository\RoomReservationRepositoryInterface;
use App\Rooms\Reservation\RoomAvailabilityHelper;
use App\Security\Voter\RoomReservationVoter;
use App\Settings\TimetableSettings;
use App\Sorting\RoomNameStrategy;
use App\Sorting\RoomReservationDateStrategy;
use App\Sorting\RoomReservationWeekGroupStrategy;
use App\Sorting\Sorter;
use App\View\Filter\RoomFilter;
use App\View\Filter\RoomFilterView;
use App\View\Filter\RoomsFilter;
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
class RoomReservationController extends AbstractController {

    private $repository;

    public function __construct(RoomReservationRepositoryInterface $repository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);

        $this->repository = $repository;
    }

    /**
     * @Route("", name="room_reservations")
     */
    public function index(DateHelper $dateHelper, RoomRepositoryInterface $roomRepository, RoomsFilter $roomsFilter,
                          RoomAvailabilityHelper $availabilityHelper, Sorter $sorter, Request $request) {
        $date = $this->getDateFromRequest($request, $dateHelper);
        $roomsFilterView = $roomsFilter->handle($request->query->get('rooms', []));
        $rooms = $roomsFilterView->getCurrentRooms();

        if(count($rooms) === 0) {
            $rooms = $roomRepository->findAll();
            $sorter->sort($rooms, RoomNameStrategy::class);
        }

        $overview = $availabilityHelper->getAvailabilities($date, $rooms);

        return $this->render('rooms/reservations/index.html.twig', [
            'roomsFilter' => $roomsFilterView,
            'rooms' => $rooms,
            'date' => $date,
            'overview' => $overview,
            'days' => $dateHelper->getListOfNextDays(7, $date)
        ]);
    }

    /**
     * @Route("/reservation", name="reservation_xhr")
     */
    public function reservation(DateHelper $dateHelper, RoomRepositoryInterface $roomRepository,
                                RoomAvailabilityHelper $availabilityHelper, TimetableSettings $timetableSettings, Request $request) {
        $date = $this->getDateFromRequest($request, $dateHelper);
        $room = $this->getRoomFromRequest($request, $roomRepository);
        $lessonNumber = $request->query->getInt('lessonNumber', 0);

        $availability = $availabilityHelper->getAvailability($room, $date, $lessonNumber);

        return $this->render('rooms/reservations/reservation.html.twig', [
            'availability' => $availability,
            'date' => $date,
            'room' => $room,
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

        return $this->render('rooms/reservations/list.html.twig', [
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

    private function getRoomFromRequest(Request $request, RoomRepositoryInterface $roomRepository): ?Room {
        $uuid = $request->query->get('room', null);

        if(empty($uuid)) {
            return null;
        }

        return $roomRepository->findOneByUuid($uuid);
    }

    /**
     * @Route("/add", name="add_room_reservation")
     */
    public function add(DateHelper $dateHelper, RoomRepositoryInterface $roomRepository, Request $request) {
        $this->denyAccessUnlessGranted(RoomReservationVoter::New);

        $date = $this->getDateFromRequest($request, $dateHelper);
        $room = $this->getRoomFromRequest($request, $roomRepository);

        $reservation = (new RoomReservation())
            ->setLessonStart($request->query->getInt('lessonStart', 0))
            ->setLessonEnd($request->query->getInt('lessonStart', 0));

        if($date !== null) {
            $reservation->setDate($date);
        }
        if($room !== null) {
            $reservation->setRoom($room);
        }

        if($this->getUser()->getTeacher() !== null) {
            $reservation->setTeacher($this->getUser()->getTeacher());
        }

        $form = $this->createForm(RoomReservationType::class, $reservation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($reservation);
            $this->addFlash('success', 'plans.rooms.reservations.add.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('rooms/reservations/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{uuid}/edit", name="edit_room_reservation")
     */
    public function edit(RoomReservation $reservation, Request $request) {
        $this->denyAccessUnlessGranted(RoomReservationVoter::Edit, $reservation);

        $form = $this->createForm(RoomReservationType::class, $reservation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($reservation);
            $this->addFlash('success', 'plans.rooms.reservations.edit.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('rooms/reservations/edit.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

    /**
     * @Route("/{uuid}/remove", name="remove_room_reservation")
     */
    public function remove(RoomReservation $reservation, TranslatorInterface $translator, Request $request) {
        $this->denyAccessUnlessGranted(RoomReservationVoter::Remove, $reservation);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'plans.rooms.reservations.remove.confirm',
            'message_parameters' => [
                '%room%' => $reservation->getRoom()->getName(),
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
            $this->addFlash('success', 'plans.rooms.reservations.remove.success');

            return $this->redirectToRoute('list_reservations');
        }

        return $this->render('rooms/reservations/remove.html.twig', [
            'form' => $form->createView(),
            'reservation' => $reservation
        ]);
    }

}