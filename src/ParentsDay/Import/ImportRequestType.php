<?php

namespace App\ParentsDay\Import;

use App\ParentsDay\Entity\ParentsDay;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ImportRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('parentsDay', EntityType::class, [
                'label' => 'label.parents_day',
                'class' => ParentsDay::class,
                'choice_label' => fn(ParentsDay $parentsDay): string => $parentsDay->getTitle(),
                'disabled' => true
            ])
            ->add('csv', FileType::class, [
                'label' => 'admin.parents_day.rooms.import.csv.label',
                'help' => 'admin.parents_day.rooms.import.csv.help',
            ])
            ->add('delimiter', TextType::class, [
                'label' => 'label.delimiter'
            ])
            ->add('teacherHeader', TextType::class, [
                'label' => 'admin.parents_day.rooms.import.teacher_header'
            ])
            ->add('roomHeader', TextType::class, [
                'label' => 'admin.parents_day.rooms.import.room_header'
            ]);
    }
}