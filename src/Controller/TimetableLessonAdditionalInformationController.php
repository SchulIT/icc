<?php

namespace App\Controller;

use App\Entity\TimetableLessonAdditionalInformation;
use App\Entity\User;
use App\Form\TimetableLessonAdditionInformationType;
use App\Repository\StudyGroupRepositoryInterface;
use App\Repository\TimetableLessonAdditionalInformationRepositoryInterface;
use App\Security\Voter\TimetableLessonAdditionalInformationVoter;
use DateTime;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/dashboard/additional_information')]
class TimetableLessonAdditionalInformationController extends AbstractController {

    public function __construct(private readonly TimetableLessonAdditionalInformationRepositoryInterface $additionalInformationRepository, RefererHelper $redirectHelper) {
        parent::__construct($redirectHelper);
    }

    #[Route('/add', name: 'add_timetable_lesson_additional_information')]
    public function add(Request $request, StudyGroupRepositoryInterface $studyGroupRepository): Response {
        $this->denyAccessUnlessGranted(TimetableLessonAdditionalInformationVoter::New);
        /** @var User $user */
        $user = $this->getUser();

        $additionalInformation = new TimetableLessonAdditionalInformation();
        if($user->getTeacher() !== null) {
            $additionalInformation->setAuthor($user->getTeacher());
        }
        $additionalInformation
            ->setDate(new DateTime($request->query->get('date')))
            ->setLessonStart($request->query->getInt('start'))
            ->setLessonEnd($request->query->getInt('end'));

        if($request->query->has('studygroup')) {
            $studyGroup = $studyGroupRepository->findOneByUuid($request->query->get('studygroup'));

            if ($studyGroup === null) {
                throw new NotFoundHttpException();
            }

            $additionalInformation->setStudyGroup($studyGroup);
        }

        $form = $this->createForm(TimetableLessonAdditionInformationType::class, $additionalInformation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->additionalInformationRepository->persist($additionalInformation);
            $this->addFlash('success', 'dashboard.additional_information.add.success');

            return $this->redirectToRoute('dashboard', [
                'date' => $additionalInformation->getDate()->format('Y-m-d')
            ]);
        }

        return $this->render('dashboard/additional_information/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/edit', name: 'edit_timetable_lesson_additional_information')]
    public function edit(TimetableLessonAdditionalInformation $additionalInformation, Request $request): Response {
        $this->denyAccessUnlessGranted(TimetableLessonAdditionalInformationVoter::Edit, $additionalInformation);

        $form = $this->createForm(TimetableLessonAdditionInformationType::class, $additionalInformation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->additionalInformationRepository->persist($additionalInformation);
            $this->addFlash('success', 'dashboard.additional_information.edit.success');

            return $this->redirectToRoute('dashboard', [
                'date' => $additionalInformation->getDate()->format('Y-m-d')
            ]);
        }

        return $this->render('dashboard/additional_information/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_timetable_lesson_additional_information')]
    public function remove(TimetableLessonAdditionalInformation $additionalInformation, Request $request, TranslatorInterface $translator): Response {
        $this->denyAccessUnlessGranted(TimetableLessonAdditionalInformationVoter::Remove, $additionalInformation);

        $form = $this->createForm(ConfirmType::class, [], [
            'message' => 'dashboard.additional_information.remove.confirm',
            'message_parameters' => [
                '%lesson%' => $translator->trans('label.exam_lessons', ['%start%' => $additionalInformation->getLessonStart(), '%end%' => $additionalInformation->getLessonEnd(), '%count%' => ($additionalInformation->getLessonEnd() - $additionalInformation->getLessonStart())]),
                '%studygroup%' => $additionalInformation->getStudyGroup()->getName(),
                '%date%' => $additionalInformation->getDate()->format($translator->trans('date.format'))
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->additionalInformationRepository->remove($additionalInformation);
            $this->addFlash('success', 'dashboard.additional_information.remove.success');
            return $this->redirectToRoute('dashboard', [
                'date' => $additionalInformation->getDate()->format('Y-m-d')
            ]);
        }

        return $this->render('dashboard/additional_information/remove.html.twig', [
            'form' => $form->createView(),
            'additionalInformation' => $additionalInformation
        ]);
    }
}