<?php

namespace App\Form;

use App\Converter\MessageScopeStringConverter;
use App\Converter\StudyGroupStringConverter;
use App\Converter\UserTypeStringConverter;
use App\Entity\Grade;
use App\Entity\MessageScope;
use App\Entity\MessageVisibility;
use App\Entity\StudyGroup;
use App\Security\Voter\MessageScopeVoter;
use App\Sorting\StringStrategy;
use App\Sorting\StudyGroupStrategy;
use App\Utils\ArrayUtils;
use Doctrine\ORM\EntityRepository;
use SchoolIT\CommonBundle\Form\FieldsetType;
use SchoolIT\CommonBundle\Helper\DateHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MessageType extends AbstractType {

    private const YearsDelta = 1;

    private $studyGroupConverter;
    private $userTypeConverter;
    private $messageScopeConverter;
    private $dateHelper;

    private $stringStrategy;
    private $studyGroupStrategy;

    private $authorizationChecker;

    public function __construct(StudyGroupStringConverter $studyGroupConverter, UserTypeStringConverter $userTypeConverter,
                                MessageScopeStringConverter $messageScopeConverter, DateHelper $dateHelper,
                                StringStrategy $stringStrategy, StudyGroupStrategy $studyGroupStrategy,
                                AuthorizationCheckerInterface $authorizationChecker) {
        $this->studyGroupConverter = $studyGroupConverter;
        $this->userTypeConverter = $userTypeConverter;
        $this->messageScopeConverter = $messageScopeConverter;
        $this->dateHelper = $dateHelper;

        $this->stringStrategy = $stringStrategy;
        $this->studyGroupStrategy = $studyGroupStrategy;

        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {

                    $years = [ ];
                    $currentYear = (int)$this->dateHelper->getToday()->format('Y');

                    for($year = $currentYear - static::YearsDelta; $year <= $currentYear + static::YearsDelta; $year++) {
                        $years[] = $year;
                    }

                    $scopes = array_filter(ArrayUtils::createArray(MessageScope::keys(), MessageScope::values()),
                        function(MessageScope $scope) {
                            return $this->authorizationChecker->isGranted(MessageScopeVoter::USE, $scope);
                        }
                    );

                    $builder
                        ->add('title', TextType::class, [
                            'label' => 'label.title'
                        ])
                        ->add('scope', ChoiceType::class, [
                            'choices' => $scopes,
                            'label' => 'label.scope',
                            'choice_label' => function(MessageScope $scope) {
                                return $this->messageScopeConverter->convert($scope);
                            }
                        ])
                        ->add('visibilities', EntityType::class, [
                            'label' => 'label.visibility',
                            'class' => MessageVisibility::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('v')
                                    ->orderBy('v.userType', 'asc');
                            },
                            'multiple' => true,
                            'expanded' => true,
                            'choice_label' => function(MessageVisibility $visibility) {
                                return $this->userTypeConverter->convert($visibility->getUserType());
                            }
                        ])
                        ->add('studyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups',
                            'class' => StudyGroup::class,
                            'query_builder' => function(EntityRepository $repository) {
                                return $repository->createQueryBuilder('sg')
                                    ->select(['sg', 'g'])
                                    ->orderBy('sg.name', 'asc')
                                    ->leftJoin('sg.grades', 'g');
                            },
                            'group_by' => function(StudyGroup $group) {
                                $grades = array_map(function(Grade $grade) {
                                    return $grade->getName();
                                }, $group->getGrades()->toArray());

                                return join(', ', $grades);
                            },
                            'multiple' => true,
                            'choice_label' => function(StudyGroup $group) {
                                return $this->studyGroupConverter->convert($group);
                            },
                            'sort_by' => $this->stringStrategy,
                            'sort_items_by' => $this->studyGroupStrategy,
                            'attr' => [
                                'size' => 10
                            ]
                        ])
                        ->add('startDate', DateType::class, [
                            'label' => 'label.message.start',
                            'years' => $years
                        ])
                        ->add('expireDate', DateType::class, [
                            'label' => 'label.message.expiry',
                            'years' => $years
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
                            'entry_type' => MessageAttachmentType::class,
                            'allow_add' => true,
                            'allow_delete' => true
                        ]);
                }
            ])
            ->add('group_uploaddownload', FieldsetType::class, [
                'legend' => 'label.messages_files.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isDownloadsEnabled', CheckboxType::class, [
                            'label' => 'label.messages_files.enable_downloads',
                            'required' => false,
                            'help' => 'label.messages_files.info_downloads'
                        ])
                        ->add('isUploadsEnabled', CheckboxType::class, [
                            'label' => 'label.messages_files.enable_uploads',
                            'required' => false,
                            'help' => 'label.messages_files.info_uploads'
                        ])
                        ->add('uploadDescription', MarkdownType::class, [
                            'label' => 'label.messages_files.upload_description',
                            'required' => false
                        ])
                        ->add('files', CollectionType::class, [
                            'entry_options' => [
                                'label' => 'label.file'
                            ],
                            'entry_type' => MessageFileType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'by_reference' => false
                        ]);
                }
            ]);
    }
}