<?php

namespace App\Form;

use App\Entity\LessonEntry;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LessonEntryType extends LessonEntryCreateType {

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        parent::buildForm($builder, $options);

        $builder
            ->remove('teacher');

        $builder
            ->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
                $form = $event->getForm();
                $entry = $event->getData();

                if($entry instanceof LessonEntry) {
                    if($entry->isCancelled()) {
                        $form->remove('replacementSubject')
                            ->remove('replacementTeacher')
                            ->remove('topic')
                            ->remove('comment')
                            ->remove('attendances');

                        $form->add('cancelReason', TextType::class, [
                            'label' => 'book.entry.cancel.reason',
                            'required' => true
                        ]);
                    } else if($entry->getId() !== null) {
                        $form
                            ->remove('lessonStart')
                            ->remove('lessonEnd')
                            ->remove('subject');
                    }
                }
            });
    }
}