<?php

namespace App\ParentsDay\Room;

use App\Form\ParentsDayTeacherRoomType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParentsDayRoomsRequestType extends AbstractType {
    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefault('parents_day', null);
        $resolver->setDefault('data_class', ParentsDayRoomsRequest::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('teacherRooms', CollectionType::class, [
                'entry_type' => ParentsDayTeacherRoomType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'entry_options' => [
                    'parents_day' => $options['parents_day'],
                ],
                'by_reference' => false,
                'error_bubbling' => true,
            ]);
    }
}