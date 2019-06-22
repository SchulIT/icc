<?php

namespace App\Form;

use App\Converter\StudyGroupStringConverter;
use App\Converter\UserStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\DocumentCategory;
use App\Entity\DocumentVisibility;
use App\Entity\Grade;
use App\Entity\StudyGroup;
use App\Entity\User;
use App\Entity\UserType;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Sorting\UserUsernameStrategy;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DocumentType extends AbstractType {

    private $studyGroupConverter;

    private $stringStrategy;
    private $studyGroupStrategy;
    private $documentCategoryNameStrategy;
    private $userTypeConverter;
    private $userConverter;
    private $userStrategy;

    private $authorizationChecker;

    public function __construct(StudyGroupStringConverter $studyGroupConverter, StringStrategy $stringStrategy,
                                StudyGroupStrategy $studyGroupStrategy, UserTypeStringConverter $userTypeConverter,
                                DocumentCategoryNameStrategy $documentCategorySorter, UserStringConverter $userConverter,
                                UserUsernameStrategy $userStrategy, AuthorizationCheckerInterface $authorizationChecker) {
        $this->studyGroupConverter = $studyGroupConverter;
        $this->stringStrategy = $stringStrategy;
        $this->studyGroupStrategy = $studyGroupStrategy;
        $this->userTypeConverter = $userTypeConverter;
        $this->documentCategoryNameStrategy = $documentCategorySorter;
        $this->userConverter = $userConverter;
        $this->userStrategy = $userStrategy;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $isRestrictedView = $this->authorizationChecker->isGranted('ROLE_DOCUMENTS_ADMIN') !== true;

        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) use($isRestrictedView) {
                    $builder
                        ->add('name', TextType::class, [
                            'label' => 'label.name',
                            'disabled' => $isRestrictedView
                        ])
                        ->add('category', SortableEntityType::class, [
                            'label' => 'label.category',
                            'sort_by' => $this->documentCategoryNameStrategy,
                            'class' => DocumentCategory::class,
                            'choice_label' => function (DocumentCategory $category) {
                                return $category->getName();
                            },
                            'disabled' => $isRestrictedView
                        ]);

                    if($isRestrictedView !== true) {
                        $builder
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
                                ],
                                'required' => false
                            ]);
                    }

                    $builder
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
            ]);

        if($isRestrictedView !== true) {
            $builder
                ->add('group_authors', FieldsetType::class, [
                    'legend' => 'label.authors',
                    'fields' => function(FormBuilderInterface $builder) {
                        $builder
                            ->add('authors', SortableEntityType::class, [
                                'label' => 'label.authors',
                                'class' => User::class,
                                'query_builder' => function(EntityRepository $repository) {
                                    return $repository->createQueryBuilder('u')
                                        ->select('u')
                                        ->where('u.userType = :userType')
                                        ->setParameter('userType', UserType::Teacher());
                                },
                                'choice_label' => function(User $user) {
                                    return $this->userConverter->convert($user);
                                },
                                'sort_by' => $this->userStrategy,
                                'multiple' => true,
                                'required' => false
                            ]);
                    }
                ]);
        }
    }
}