<?php

namespace App\Form;

use App\Entity\ResourceEntity;
use App\Entity\ResourceType as ResourceTypeEntity;
use App\Entity\Room;
use App\Exception\UnexpectedTypeException;
use App\Sorting\ResourceTypeStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ResourceType extends AbstractType {

    private $sortingStrategy;

    public function __construct(ResourceTypeStrategy $sortingStrategy) {
        $this->sortingStrategy = $sortingStrategy;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            if($data instanceof Room) {
                $form
                    ->add('group_general', FieldsetType::class, [
                        'legend' => 'label.general',
                        'fields' => function(FormBuilderInterface $builder) {
                            $builder
                                ->add('externalId', TextType::class, [
                                    'label' => 'label.external_id',
                                    'required' => false
                                ])
                                ->add('name', TextType::class, [
                                    'label' => 'label.name'
                                ])
                                ->add('description', TextType::class, [
                                    'label' => 'label.description',
                                    'required' => false
                                ])
                                ->add('capacity', IntegerType::class, [
                                    'label' => 'label.capacity',
                                    'required' => false
                                ])
                                ->add('isReservationEnabled', CheckboxType::class, [
                                    'label' => 'label.reservation_enabled.label',
                                    'help' => 'label.reservation_enabled.help',
                                    'required' => false,
                                    'label_attr' => [
                                        'class' => 'checkbox-custom'
                                    ]
                                ]);
                        }
                    ])
                    ->add('group_tags', FieldsetType::class, [
                        'legend' => 'label.tags',
                        'fields' => function(FormBuilderInterface $builder) {
                            $builder
                                ->add('tags', RoomTagChoiceType::class, [
                                    'label' => '',
                                    'required' => false
                                ]);
                        }
                    ]);
            } else if($data instanceof ResourceEntity) {
                $form
                    ->add('group_general', FieldsetType::class, [
                        'legend' => 'label.general',
                        'fields' => function(FormBuilderInterface $builder) {
                            $builder
                                ->add('name', TextType::class, [
                                    'label' => 'label.name'
                                ])
                                ->add('description', TextType::class, [
                                    'label' => 'label.description',
                                    'required' => false
                                ])
                                ->add('isReservationEnabled', CheckboxType::class, [
                                    'label' => 'label.reservation_enabled.label',
                                    'help' => 'label.reservation_enabled.help',
                                    'required' => false,
                                    'label_attr' => [
                                        'class' => 'checkbox-custom'
                                    ]
                                ])
                                ->add('type', SortableEntityType::class, [
                                    'label' => 'label.category',
                                    'required' => true,
                                    'class' => ResourceTypeEntity::class,
                                    'sort_by' => $this->sortingStrategy,
                                    'query_builder' => function(EntityRepository $repository) {
                                        return $repository->createQueryBuilder('t')
                                            ->where('t.id > 1');
                                    },
                                    'choice_label' => function(ResourceTypeEntity $entity) {
                                        return $entity->getName();
                                    },
                                    'attr' => [
                                        'data-choice' => 'true'
                                    ]
                                ]);
                        }
                    ]);
            } else {
                throw new UnexpectedTypeException($data, ResourceEntity::class);
            }
        });
    }
}