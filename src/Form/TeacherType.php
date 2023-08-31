<?php

namespace App\Form;

use App\Entity\Gender;
use App\Entity\TeacherTag;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataMapper\CheckboxListMapper;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class TeacherType extends AbstractType {

    public function __construct(private readonly TranslatorInterface $translator) { }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
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
            ->add('gender', EnumType::class, [
                'class' => Gender::class,
                'label' => 'label.gender',
                'expanded' => true,
                'label_attr' => [
                    'class' => 'radio-custom'
                ],
                'choice_label' => function(Gender $gender) {
                    return $this->translator->trans('gender.' . $gender->value, [], 'enums');
                }
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email'
            ])
            ->add('birthday', DateType::class, [
                'label' => 'label.birthday',
                'required' => false,
                'widget' => 'single_text'
            ])
            ->add('showBirthday', CheckboxType::class, [
                'label' => 'label.show_birthday.label',
                'help' => 'label.show_birthday.help',
                'required' => false,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ])
            ->add('tags', EntityType::class, [
                'class' => TeacherTag::class,
                'label' => 'label.tags',
                'choice_label' => fn(TeacherTag $tag) => $tag->getName(),
                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('t')
                    ->orderBy('t.name', 'asc'),
                'multiple' => true,
                'expanded' => true,
                'label_attr' => [
                    'class' => 'checkbox-custom'
                ]
            ]);
    }
}