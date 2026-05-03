<?php

namespace App\Form;

use App\Entity\ParentsDay;
use App\Entity\ParentsDayTeacherRoom;
use App\Entity\Room;
use App\Entity\Teacher;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentsDayTeacherRoomType extends AbstractType {

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefault('parents_day', null);
        $resolver->setDefault('data_class', ParentsDayTeacherRoom::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('teacher', SortableEntityType::class, [
                'label' => 'label.teacher',
                'class' => Teacher::class,
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->add('room', SortableEntityType::class, [
                'label' => 'label.room',
                'class' => Room::class,
                'attr' => [
                    'data-choice' => 'true'
                ]
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use($options) {
                $data = $event->getData();

                if($options['parents_day'] instanceof ParentsDay && $data instanceof ParentsDayTeacherRoom) {
                    $data->setParentsDay($options['parents_day']);
                }
            });
    }
}