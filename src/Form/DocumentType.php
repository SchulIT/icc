<?php

namespace App\Form;

use App\Converter\StudyGroupStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\DocumentCategory;
use App\Entity\DocumentVisibility;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DocumentType extends AbstractType {

    private $studyGroupConverter;

    private $stringStrategy;
    private $studyGroupStrategy;
    private $documentCategoryNameStrategy;
    private $userTypeConverter;

    public function __construct(StudyGroupStringConverter $studyGroupConverter, StringStrategy $stringStrategy,
                                StudyGroupStrategy $studyGroupStrategy, UserTypeStringConverter $userTypeConverter,
                                DocumentCategoryNameStrategy $documentCategorySorter) {
        $this->studyGroupConverter = $studyGroupConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studyGroupStrategy = $studyGroupStrategy;
        $this->userTypeConverter = $userTypeConverter;
        $this->documentCategoryNameStrategy = $documentCategorySorter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name'
                        ])
                        ->add('category', SortableEntityType::class, [
                            'label' => 'label.category',
                            'sort_by' => $this->documentCategoryNameStrategy,
                            'class' => DocumentCategory::class,
                            'choice_label' => function (DocumentCategory $category) {
                                return $category->getName();
                            }
                        ])
                        ->add('visibilities', EntityType::class, [
                            'label' => 'label.visibility',
                            'class' => DocumentVisibility::class,
                            'query_builder' => function (EntityRepository $repository) {
                                return $repository->createQueryBuilder('v')
                                    ->orderBy('v.userType', 'asc');
                            },
                            'multiple' => true,
                            'expanded' => true,
                            'choice_label' => function (DocumentVisibility $visibility) {
                                return $this->userTypeConverter->convert($visibility->getUserType());
                            }
                        ])
                        ->add('studyGroups', SortableEntityType::class, [
                            'label' => 'label.study_groups',
                            'class' => StudyGroup::class,
                            'query_builder' => function (EntityRepository $repository) {
                                return $repository->createQueryBuilder('sg')
                                    ->select(['sg', 'g'])
                                    ->orderBy('sg.name', 'asc')
                                    ->leftJoin('sg.grades', 'g');
                            },
                            'group_by' => function (StudyGroup $group) {
                                $grades = array_map(function (Grade $grade) {
                                    return $grade->getName();
                                }, $group->getGrades()->toArray());

                                return join(', ', $grades);
                            },
                            'multiple' => true,
                            'choice_label' => function (StudyGroup $group) {
                                return $this->studyGroupConverter->convert($group);
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->studyGroupStrategy,
                            'attr' => [
                                'size' => 10
                            ]
                        ])
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.content',
                            'upload_enabled' => false
                        ]);
                }
            ])
            ->add('group_attachments', FieldsetType::class, [
                'legend' => 'label.attachments',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('attachments', CollectionType::class, [
                            'entry_type' => DocumentAttachmentType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'by_reference' => false
                        ]);
                }
            ])
        ;
    }
}