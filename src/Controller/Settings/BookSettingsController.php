<?php

namespace App\Controller\Settings;

use App\Entity\Grade;
use App\Form\TextCollectionEntryType;
use App\Repository\GradeRepositoryInterface;
use App\Repository\StudentAbsenceTypeRepositoryInterface;
use App\Settings\BookSettings;
use App\Utils\ArrayUtils;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Route(path: '/admin/settings')]
#[IsGranted('ROLE_ADMIN')]
class BookSettingsController extends AbstractController {
    #[Route(path: '/book', name: 'admin_settings_book')]
    public function book(Request $request, BookSettings $settings, GradeRepositoryInterface $gradeRepository, StudentAbsenceTypeRepositoryInterface $typeRepository): Response {
        $builder = $this->createFormBuilder();
        $builder->add('grades_grade_teacher_excuses', ChoiceType::class, [
            'label' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.label',
            'help' => 'admin.settings.book.excuses.grades_grade_teacher_excuses.help',
            'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
            'multiple' => true,
            'attr' => [
                'data-choice' => 'true'
            ],
            'data' => $settings->getGradesGradeTeacherExcuses()
        ])
            ->add('grades_tuition_teacher_excuses', ChoiceType::class, [
                'label' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.label',
                'help' => 'admin.settings.book.excuses.grades_tuition_teacher_excuses.help',
                'choices' => ArrayUtils::createArrayWithKeysAndValues($gradeRepository->findAll(), fn(Grade $grade) => $grade->getName(), fn(Grade $grade) => $grade->getId()),
                'multiple' => true,
                'attr' => [
                    'data-choice' => 'true'
                ],
                'data' => $settings->getGradesTuitionTeacherExcuses()
            ])
            ->add('exclude_student_status', CollectionType::class, [
                'label' => 'admin.settings.book.exclude_student_status.label',
                'help' => 'admin.settings.book.exclude_student_status.help',
                'required' => false,
                'data' => $settings->getExcludeStudentsStatus(),
                'entry_type' => TextCollectionEntryType::class,
                'entry_options' => [
                    'constraints' => new NotBlank()
                ],
                'allow_add' => true,
                'allow_delete' => true
            ])
            ->add('regular_font', FileType::class, [
                'required' => false,
                'label' => 'admin.settings.book.font.regular.label',
                'help' => 'admin.settings.book.font.regular.help'
            ])
            ->add('bold_font', FileType::class, [
                'required' => false,
                'label' => 'admin.settings.book.font.bold.label',
                'help' => 'admin.settings.book.font.bold.help'
            ])
            ->add('attendances_visible_for_students_and_parents', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.attendances_visible_for_students_and_parents.label',
                'help' => 'admin.settings.book.attendances_visible_for_students_and_parents.help',
                'data' => $settings->isAttendanceVisibleForStudentsAndParentsEnabled()
            ])
            ->add('lesson_topics_visible_for_students_and_parents', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.lesson_topics_visible_for_students_and_parents.label',
                'help' => 'admin.settings.book.lesson_topics_visible_for_students_and_parents.help',
                'data' => $settings->isLessonTopicsVisibleForStudentsAndParentsEnabled()
            ])
            ->add('suggestion_priority_exam', IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.exam',
                'data' => $settings->getSuggestionPriorityForExams()
            ])
            ->add('suggestion_priority_previously_absent', IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.previously_absent',
                'data' => $settings->getSuggestionPriorityForPreviouslyAbsent()
            ])
            ->add('suggestion_priority_excuse_note', IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.excuse_note',
                'data' => $settings->getSuggestionPriorityForExcuseNote()
            ])
            ->add('suggestion_priority_book_event', IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.book_event',
                'data' => $settings->getSuggestionPriorityForBookEvent()
            ])
            ->add('suggestion_priority_absent_study_group', IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.absent_study_group',
                'data' => $settings->getSuggestionPriorityForAbsentStudyGroup()
            ])
            ->add('notify_parents_on_absent_student_without_note', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.notify_parents_on_absent_student_without_note.label',
                'help' => 'admin.settings.book.notify_parents_on_absent_student_without_note.help',
                'data' => $settings->getNotifyParentsOnStudentAbsenceWithoutSuggestion()
            ])
            ->add('notify_grade_teachers_on_absent_student_without_note', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.notify_grade_teachers_on_absent_student_without_note.label',
                'help' => 'admin.settings.book.notify_grade_teachers_on_absent_student_without_note.help',
                'data' => $settings->getNotifyGradeTeachersOnStudentAbsenceWithoutSuggestion()
            ])
            ->add('students_and_parents_can_view_book_comments', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.students_and_parents_can_view_book_comments.label',
                'help' =>  'admin.settings.book.students_and_parents_can_view_book_comments.help',
                'data' => $settings->getStudentsAndParentsCanViewBookCommentsEnabled()
            ])
            ->add('always_make_comments_visible_for_student_and_parents', CheckboxType::class, [
                'required' => false,
                'label' => 'admin.settings.book.always_make_comments_visible_for_student_and_parents.label',
                'help' =>  'admin.settings.book.always_make_comments_visible_for_student_and_parents.help',
                'data' => $settings->getAlwaysMakeCommentsVisibleForStudentAndParents()
            ]);

        $types = $typeRepository->findAll();

        foreach($types as $type) {
            $builder->add('suggestion_priority_' . $type->getUuid(), IntegerType::class, [
                'required' => true,
                'label' => 'admin.settings.book.attendance_suggestion.priority.absence_note',
                'label_translation_parameters' => [
                    '%type%' => $type->getName()
                ],
                'data' => $settings->getSuggestionPriorityForAbsenceType($type->getUuid()->toString())
            ]);
        }

        $form = $builder->getForm();
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $map = [
                'grades_grade_teacher_excuses' => function(array $ids) use($settings) {
                    $settings->setGradesGradeTeacherExcuses($ids);
                },
                'grades_tuition_teacher_excuses' => function(array $ids) use($settings) {
                    $settings->setGradesTuitionTeacherExcuses($ids);
                },
                'exclude_student_status' => function(array $status) use($settings) {
                    $settings->setExcludeStudentsStatus($status);
                },
                'attendances_visible_for_students_and_parents' => function(bool $isEnabled) use($settings) {
                    $settings->setAttendanceVisibleForStudentsAndParentsEnabled($isEnabled);
                },
                'lesson_topics_visible_for_students_and_parents' => function(bool $isEnabled) use ($settings) {
                    $settings->setLessonTopicsVisibleForStudentsAndParentsEnabled($isEnabled);
                },
                'suggestion_priority_exam' => function(int $priority) use ($settings) {
                    $settings->setSuggestionPriorityForExams($priority);
                },
                'suggestion_priority_previously_absent' => function(int $priority) use ($settings) {
                    $settings->setSuggestionPriorityForPreviouslyAbsent($priority);
                },
                'suggestion_priority_excuse_note' => function(int $priority) use ($settings) {
                    $settings->setSuggestionPriorityForExcuseNote($priority);
                },
                'suggestion_priority_book_event' => function(int $priority) use ($settings) {
                    $settings->setSuggestionPriorityForBookEvent($priority);
                },
                'suggestion_priority_absent_study_group' => function(int $priority) use ($settings) {
                    $settings->setSuggestionPriorityForAbsentStudyGroup($priority);
                },
                'notify_parents_on_absent_student_without_note' => function(bool $notify) use($settings) {
                    $settings->setNotifyParentsOnStudentAbsenceWithoutSuggestion($notify);
                },
                'notify_grade_teachers_on_absent_student_without_note' => function(bool $notify) use($settings) {
                    $settings->setNotifyGradeTeachersOnStudentAbsenceWithoutSuggestion($notify);
                },
                'students_and_parents_can_view_book_comments' => function(bool $enabled) use ($settings) {
                    $settings->setStudentsAndParentsCanViewBookCommentsEnabled($enabled);
                },
                'always_make_comments_visible_for_student_and_parents' => function(bool $enabled) use ($settings) {
                    $settings->setAlwaysMakeCommentsVisibleForStudentAndParents($enabled);
                }
            ];

            foreach($map as $formKey => $callable) {
                $value = $form->get($formKey)->getData();
                $callable($value);
            }

            foreach($types as $type) {
                $settings->setSuggestionPriorityForAbsenceType($type->getUuid()->toString(), $form->get('suggestion_priority_' . $type->getUuid()->toString())->getData());
            }

            $map = [
                'regular_font' => function(?string $font) use ($settings) {
                    $settings->setRegularFont($font);
                },
                'bold_font' => function(?string $font) use ($settings) {
                    $settings->setBoldFont($font);
                }
            ];

            foreacH($map as $formKey => $callable) {
                /** @var UploadedFile|null $file */
                $file = $form->get($formKey)->getData();

                if($file === null) {
                    continue;
                }

                $callable(base64_encode($file->getContent()));
            }

            $this->addFlash('success', 'admin.settings.success');

            return $this->redirectToRoute('admin_settings_book');
        }

        return $this->render('admin/settings/book.html.twig', [
            'form' => $form->createView(),
            'types' => $types
        ]);
    }
}