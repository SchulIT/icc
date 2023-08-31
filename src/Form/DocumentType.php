<?php

namespace App\Form;

use App\Converter\UserStringConverter;
use App\Entity\DocumentCategory;
use App\Entity\Grade;
use App\Entity\User;
use App\Entity\UserType;
use App\Sorting\DocumentCategoryNameStrategy;
use App\Sorting\GradeNameStrategy;
use App\Sorting\UserUsernameStrategy;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DocumentType extends AbstractType {

    public function __construct(private readonly DocumentCategoryNameStrategy $documentCategoryNameStrategy, private readonly UserStringConverter $userConverter, private readonly UserUsernameStrategy $userStrategy, private readonly AuthorizationCheckerInterface $authorizationChecker)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
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
                            'choice_label' => fn(DocumentCategory $category) => $category->getName(),
                            'disabled' => $isRestrictedView,
                            'attr' => [
                                'data-choice' => 'true'
                            ]
                        ]);

                    if($isRestrictedView !== true) {
                        $builder
                            ->add('visibilities', UserTypeEntityType::class, [
                                'label' => 'label.visibility',
                                'multiple' => true,
                                'expanded' => true,
                                'label_attr' => [
                                    'class' => 'checkbox-custom'
                                ]
                            ])
                            ->add('grades', GradeChoiceType::class, [
                                'label' => 'label.grades',
                                'multiple' => true,
                                'attr' => [
                                    'size' => 10,
                                    'data-choice' => 'true'
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
                            'allow_delete' => false,
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
                                'query_builder' => fn(EntityRepository $repository) => $repository->createQueryBuilder('u')
                                    ->select('u')
                                    ->where('u.userType = :userType')
                                    ->setParameter('userType', UserType::Teacher),
                                'choice_label' => fn(User $user) => $this->userConverter->convert($user),
                                'sort_by' => $this->userStrategy,
                                'multiple' => true,
                                'required' => false
                            ]);
                    }
                ]);
        }
    }
}