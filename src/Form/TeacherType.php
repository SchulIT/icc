<?php

namespace App\Form;

use App\Entity\TeacherTag;
use Doctrine\ORM\EntityRepository;
use FervoEnumBundle\Generated\Form\GenderType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class TeacherType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('externalId', TextType::class, [
                'label' => 'label.external_id'
            ])
            ->add('acronym', TextType::class, [
                'label' => 'label.acronym'
            ])
            ->add('title', TextType::class, [
                'label' => 'label.title',
                'required' => false
            ])
            ->add('firstname', TextType::class, [
                'label' => 'label.firstname'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'label.lastname'
            ])
            ->add('gender', GenderType::class, [
                'label' => 'label.gender',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email'
            ])
            ->add('tags', EntityType::class, [
                'class' => TeacherTag::class,
                'label' => 'label.tags',
                'choice_label' => function(TeacherTag $tag) {
                    return $tag->getName();
                },
                'query_builder' => function(EntityRepository $repository) {
                    return $repository->createQueryBuilder('t')
                        ->orderBy('t.name', 'asc');
                },
                'multiple' => true,
                'expanded' => true
            ]);
    }
}