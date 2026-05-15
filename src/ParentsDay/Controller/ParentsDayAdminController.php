<?php

namespace App\ParentsDay\Controller;

use App\Framework\Controller\AbstractController;
use App\ParentsDay\Entity\ParentsDay;
use App\Framework\Feature\Feature;
use App\Framework\Feature\IsFeatureEnabled;
use App\ParentsDay\Form\ParentsDayType;
use App\ParentsDay\Import\ImportRequest;
use App\ParentsDay\Import\ImportRequestType;
use App\ParentsDay\Import\ParentsDayTeacherRoomImporter;
use App\ParentsDay\Room\ParentsDayRoomsRequest;
use App\ParentsDay\Room\ParentsDayRoomsRequestType;
use App\ParentsDay\Repository\ParentsDayRepositoryInterface;
use App\ParentsDay\Repository\ParentsDayTeacherRoomRepositoryInterface;
use App\ParentsDay\Sorting\ParentsDayTeacherRoomTeacherStrategy;
use App\Framework\Sorting\Sorter;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/admin/parents_day')]
#[IsFeatureEnabled(Feature::ParentsDay)]
class ParentsDayAdminController extends AbstractController {
    public function __construct(RefererHelper $redirectHelper, private readonly ParentsDayRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('', name: 'admin_parents_days')]
    public function index(): Response {
        return $this->render('admin/parents_days/index.html.twig', [
            'parents_days' => $this->repository->findAll()
        ]);
    }

    #[Route('/add', name: 'add_parents_day')]
    public function add(Request $request): RedirectResponse|Response {
        $parentsDay = new ParentsDay();

        $form = $this->createForm(ParentsDayType::class, $parentsDay);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($parentsDay);
            $this->addFlash('success', 'admin.parents_day.add.success');

            return $this->redirectToRoute('admin_parents_days');
        }

        return $this->render('admin/parents_days/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_parents_day')]
    public function edit(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, Request $request): RedirectResponse|Response {
        $form = $this->createForm(ParentsDayType::class, $parentsDay);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->persist($parentsDay);
            $this->addFlash('success', 'admin.parents_day.edit.success');

            return $this->redirectToRoute('admin_parents_days');
        }

        return $this->render('admin/parents_days/edit.html.twig', [
            'parents_day' => $parentsDay,
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_parents_day')]
    public function remove(#[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay, Request $request): Response {
        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'admin.parents_day.remove.confirm',
            'message_parameters' => [
                '%title%' => $parentsDay->getTitle()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($parentsDay);

            $this->addFlash('success', 'admin.parents_day.remove.success');

            return $this->redirectToRoute('admin_appointments');
        }

        return $this->render('admin/parents_days/remove.html.twig', [
            'form' => $form->createView(),
            'parents_day' => $parentsDay
        ]);
    }

    #[Route('/{uuid}/rooms', name: 'parents_day_teacher_rooms')]
    public function rooms(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay,
        Request $request,
        ParentsDayTeacherRoomRepositoryInterface $roomRepository,
        Sorter $sorter,
        ValidatorInterface $validator
    ): Response {
        $rooms = $roomRepository->findAllByParentsDay($parentsDay);

        $sorter->sort($rooms, ParentsDayTeacherRoomTeacherStrategy::class);

        $roomsRequest = new ParentsDayRoomsRequest();
        $roomsRequest->teacherRooms = $rooms;

        $form = $this->createForm(ParentsDayRoomsRequestType::class, $roomsRequest, [
            'parents_day' => $parentsDay
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $validator->validate($roomsRequest)->count() === 0) {
            $roomRepository->beginTransaction();
            foreach($roomsRequest->teacherRooms as $teacherRoom) {
                $roomRepository->persist($teacherRoom);
            }
            $roomRepository->commit();

            $this->addFlash('success', 'admin.parents_day.rooms.success');
            return $this->redirectToRoute('parents_day_teacher_rooms', [
                'uuid' => $parentsDay->getUuid()
            ]);
        }

        return $this->render('admin/parents_days/rooms.html.twig', [
            'parents_day' => $parentsDay,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{uuid}/import/rooms', name: 'import_parents_day_teacher_rooms')]
    public function importTeacherRooms(
        #[MapEntity(mapping: ['uuid' => 'uuid'])] ParentsDay $parentsDay,
        Request $request,
        ParentsDayTeacherRoomImporter $importer,
        TranslatorInterface $translator
    ): Response {
        $importRequest = new ImportRequest();
        $importRequest->parentsDay = $parentsDay;
        $form = $this->createForm(ImportRequestType::class, $importRequest);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $result = $importer->import($importRequest);
            $this->addFlash('success',
                $translator->trans(
                    'admin.parents_day.rooms.import.success',
                    [
                        '%count%' => $result->importCount
                    ]
                )
            );
            return $this->redirectToRoute('admin_parents_days');
        }

        return $this->render('admin/parents_days/import_teacher_rooms.html.twig', [
            'form' => $form->createView(),
            'parentsDay' => $parentsDay
        ]);
    }
}