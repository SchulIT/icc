<?php

namespace App\Controller;

use App\Book\AttendanceSuggestion\RemoveSuggestionResolver;
use App\Book\AttendanceSuggestion\SuggestionResolver;
use App\Entity\Attendance;
use App\Entity\BookEvent;
use App\Entity\Student;
use App\Entity\StudyGroup;
use App\Entity\StudyGroupMembership;
use App\Entity\StudyGroupType;
use App\Entity\Tuition;
use App\Entity\User;
use App\Form\BookEventCreateType;
use App\Form\BookEventType;
use App\Repository\BookEventRepositoryInterface;
use App\Section\SectionResolverInterface;
use App\Security\Voter\BookEventVoter;
use JMS\Serializer\SerializerInterface;
use SchulIT\CommonBundle\Form\ConfirmType;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/book/entry/event')]
class BookEventController extends AbstractController {
    public function __construct(RefererHelper $redirectHelper, private readonly BookEventRepositoryInterface $repository) {
        parent::__construct($redirectHelper);
    }

    #[Route('/create', name: 'create_book_event_entry')]
    public function create(Request $request): Response {
        $this->denyAccessUnlessGranted(BookEventVoter::New);

        /** @var User $user */
        $user = $this->getUser();
        $event = new BookEvent();
        $event->setTeacher($user->getTeacher());
        $form = $this->createForm(BookEventCreateType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            /** @var Student $student */
            foreach($form->get('students')->getData() as $student) {
                for($lessonNumber = $event->getLessonStart(); $lessonNumber <= $event->getLessonEnd(); $lessonNumber++) {
                    $attendance = (new Attendance())
                        ->setEvent($event)
                        ->setStudent($student)
                        ->setLesson($lessonNumber);

                    $event->addAttendance($attendance);
                }
            }

            $this->repository->persist($event);
            $this->addFlash('success', 'book.events.add.success');

            return $this->redirectToRoute('show_or_edit_book_event_entry', [
                'uuid' => $event->getUuid()
            ]);
        }

        return $this->render('books/events/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/{uuid}', name: 'show_or_edit_book_event_entry')]
    public function showOrEdit(BookEvent $event, Request $request, SuggestionResolver $suggestionResolver, RemoveSuggestionResolver $removeSuggestionResolver, SectionResolverInterface $sectionResolver, SerializerInterface $serializer): Response {
        $this->denyAccessUnlessGranted(BookEventVoter::Show, $event);

        $form = $this->createForm(BookEventType::class, $event);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() && $this->isGranted(BookEventVoter::Edit, $event)) {
            $this->addFlash('success', 'book.events.edit.success');
            $this->repository->persist($event);

            return $this->redirectToRoute('show_or_edit_book_event_entry', [
                'uuid' => $event->getUuid()
            ]);
        }

        $fakeTuition = (new Tuition())
            ->setName('fake')
            ->setSection($sectionResolver->getSectionForDate($event->getDate()));
        $fakeTuition->addTeacher($event->getTeacher());
        $fakeStudyGroup = (new StudyGroup())
            ->setName('fake')
            ->setSection($sectionResolver->getSectionForDate($event->getDate()))
            ->setType(StudyGroupType::Course);
        $alreadyAddedStudentIds = [ ];

        foreach($event->getAttendances() as $attendance) {
            if(!in_array($attendance->getStudent()->getId(), $alreadyAddedStudentIds)) {
                $alreadyAddedStudentIds[] = $attendance->getStudent()->getId();
                $fakeStudyGroup->getMemberships()->add((new StudyGroupMembership())->setStudent($attendance->getStudent()));
            }
        }

        $fakeTuition->setStudyGroup($fakeStudyGroup);

        $possibleAbsences = $serializer->serialize($suggestionResolver->resolve($fakeTuition, $event->getDate(), $event->getLessonStart(), $event->getLessonEnd()), 'json');

        return $this->render('books/events/edit.html.twig', [
            'form' => $form->createView(),
            'event' => $event,
            'possibleAbsences' => $possibleAbsences
        ]);
    }

    #[Route('/{uuid}/remove', name: 'remove_book_event_entry')]
    public function remove(BookEvent $event, Request $request): Response {
        $this->denyAccessUnlessGranted(BookEventVoter::Remove, $event);

        $form = $this->createForm(ConfirmType::class, null, [
            'message' => 'book.events.remove.confirm',
            'message_parameters' => [
                '%title%' => $event->getTitle()
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $this->repository->remove($event);
            $this->addFlash('success', 'book.events.remove.success');

            return $this->redirectToRoute('book');
        }

        return $this->render('books/events/remove.html.twig', [
            'form' => $form->createView(),
            'event' => $event
        ]);
    }

}