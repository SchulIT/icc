<?php

namespace App\Form;

use App\Converter\UserStringConverter;
use App\Entity\DocumentCategory;
use App\Entity\User;
use App\Entity\UserType;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\UserUsernameStrategy;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DocumentType extends AbstractType {

    private $documentCategoryNameStrategy;
    private $userConverter;
    private $userStrategy;

    private $authorizationChecker;

    public function __construct(DocumentCategoryNameStrategy $documentCategorySorter, UserStringConverter $userConverter,
                                UserUsernameStrategy $userStrategy, AuthorizationCheckerInterface $authorizationChecker) {
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
                        ->add('title', TextType::class, [
                            'label' => 'label.title',
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
                            ->add('visibilities', UserTypeEntityType::class, [
                                'label' => 'label.visibility',
                                'multiple' => true,
                                'expanded' => true
                            ])
                            ->add('studyGroups', StudyGroupType::class, [
                                'label' => 'label.study_groups_simple',
                                'multiple' => true,
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