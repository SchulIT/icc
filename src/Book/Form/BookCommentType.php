<?php

namespace App\Book\Form;

use App\Book\Entity\BookComment;
use App\Common\Form\Choice\StudentsType;
use App\Common\Form\Choice\TeacherChoiceType;
use App\Book\Settings\BookSettings;
use App\Common\Form\Type\MarkdownType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BookCommentType extends AbstractType {

    public function __construct(private readonly BookSettings $bookSettings) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('date', DateType::class, [
                'label' => 'label.date',
                'widget' => 'single_text'
            ])
            ->add('teacher', TeacherChoiceType::class, [
                'label' => 'label.teacher',
                'placeholder' => 'label.select.teacher'
            ])
            ->add('text', MarkdownType::class, [
                'label' => 'label.comment'
            ])
            ->add('students', StudentsType::class, [
                'label' => 'label.students_simple',
                'multiple' => true,
            ])
            ->add('canStudentAndParentsView', CheckboxType::class, [
                'label' => 'comments.can_student_and_parents_view.label',
                'help' => 'comments.can_student_and_parents_view.help',
                'required' => false,
                'disabled' => $this->bookSettings->getAlwaysMakeCommentsVisibleForStudentAndParents()
            ]);

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $comment = $event->getData();

                if($comment->getId() !== null && $form->has('canStudentAndParentsView')) {
                    $form->add('canStudentAndParentsView', CheckboxType::class, [
                        'label' => 'comments.can_student_and_parents_view.label',
                        'help' => 'comments.can_student_and_parents_view.help',
                        'required' => false,
                        'disabled' => true
                    ]);
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $form = $event->getForm();

                if($this->bookSettings->getStudentsAndParentsCanViewBookCommentsEnabled() === false) {
                    $form->remove('canStudentAndParentsView');
                }
            })
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                /** @var BookComment $comment */
                $comment = $event->getData();

                if($this->bookSettings->getAlwaysMakeCommentsVisibleForStudentAndParents() === true) {
                    $comment->setCanStudentAndParentsView(true);
                }
            })
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var BookComment $comment */
                $comment = $event->getData();

                if($this->bookSettings->getAlwaysMakeCommentsVisibleForStudentAndParents() === true) {
                    $comment->setCanStudentAndParentsView(true);
                }
            });
    }
}