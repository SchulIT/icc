<?php

namespace App\Form;

use App\Converter\EnumStringConverter;
use App\Entity\DisplayTargetUserType;
use App\Entity\MessagePriority;
use App\Entity\MessageScope;
use Doctrine\ORM\EntityRepository;
use SchulIT\CommonBundle\Form\FieldsetType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageType extends AbstractType {

    private const YearsDelta = 1;

    public function __construct(private readonly DateHelper $dateHelper, private readonly AuthorizationCheckerInterface $authorizationChecker, private readonly TranslatorInterface $translator, private readonly EnumStringConverter $enumStringConverter)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('group_general', FieldsetType::class, [
                'legend' => 'label.general',
                'fields' => function(FormBuilderInterface $builder) {

                    $years = [ ];
                    $currentYear = (int)$this->dateHelper->getToday()->format('Y');

                    for($year = $currentYear - self::YearsDelta; $year <= $currentYear + self::YearsDelta; $year++) {
                        $years[] = $year;
                    }

                    $builder
                        ->add('title', TextType::class, [
                            'label' => 'label.title'
                        ])
                        ->add('scope', EnumType::class, [
                            'class' => MessageScope::class,
                            'label' => 'label.scope',
                            'choice_label' => function(MessageScope $scope) {
                                return $this->translator->trans('message_scope.' . $scope->value, [], 'enums');
                            }
                        ])
                        ->add('visibilities', UserTypeEntityType::class, [
                            'label' => 'label.visibility',
                            'multiple' => true,
                            'expanded' => true,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('studyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10,
                            ],
                            'required' => false
                        ])
                        ->add('startDate', DateType::class, [
                            'label' => 'label.message.start',
                            'years' => $years,
                            'widget' => 'single_text'
                        ])
                        ->add('expireDate', DateType::class, [
                            'label' => 'label.message.expiry',
                            'years' => $years,
                            'widget' => 'single_text',
                            'help' => 'label.message_expiry_date_inclusive_help'
                        ])
                        ->add('content', MarkdownType::class, [
                            'label' => 'label.content',
                            'upload_enabled' => false
                        ]);
                }
            ]);

        if($this->authorizationChecker->isGranted('ROLE_MESSAGE_ADMIN') === false) {
            $builder
                ->get('group_general')
                ->remove('scope');
        }

        if($this->authorizationChecker->isGranted('ROLE_MESSAGE_PRIORITY')) {
            $builder
                ->add('group_priority', FieldsetType::class, [
                    'legend' => 'label.priority',
                    'fields' => function (FormBuilderInterface $builder) {
                        $builder->add('priority', EnumType::class, [
                            'class' => MessagePriority::class,
                            'label' => 'label.priority',
                            'attr' => [
                                'data-choice' => 'true'
                            ],
                            'help' => $this->translator->trans('help.priority', [], 'enums'),
                            'choice_label' => fn(MessagePriority $priority) => $this->enumStringConverter->convert($priority)
                        ]);
                    }
                ]);
        }

        $builder
            ->add('group_attachments', FieldsetType::class, [
                'legend' => 'label.attachments',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('attachments', CollectionType::class, [
                            'entry_type' => MessageAttachmentType::class,
                            'allow_add' => true,
                            'allow_delete' => false,
                            'by_reference' => false
                        ]);
                }
            ])
            ->add('group_confirmations', FieldsetType::class, [
                'legend' => 'messages.confirm.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('mustConfirm', CheckboxType::class, [
                            'label' => 'label.must_confirm',
                            'required' => false,
                            'help' => 'messages.confirm.info',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('confirmationRequiredUserTypes', UserTypeEntityType::class, [
                            'label' => 'label.usertypes',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('confirmationRequiredStudyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ],
                            'required' => false
                        ]);
                }
            ])
            ->add('group_download', FieldsetType::class, [
                'legend' => 'label.messages_files.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isDownloadsEnabled', CheckboxType::class, [
                            'label' => 'label.messages_files.enable_downloads',
                            'required' => false,
                            'help' => 'label.messages_files.info_downloads','label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('downloadEnabledUserTypes', UserTypeEntityType::class, [
                            'label' => 'label.usertypes',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('downloadEnabledStudyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ],
                            'required' => false
                        ]);
                }
            ])
            ->add('group_upload', FieldsetType::class, [
                'legend' => 'label.messages_files.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isUploadsEnabled', CheckboxType::class, [
                            'label' => 'label.messages_files.enable_uploads',
                            'required' => false,
                            'help' => 'label.messages_files.info_uploads',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('uploadDescription', MarkdownType::class, [
                            'label' => 'label.messages_files.upload_description',
                            'required' => false
                        ])
                        ->add('uploadEnabledUserTypes', UserTypeEntityType::class, [
                            'label' => 'label.usertypes',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('uploadEnabledStudyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ],
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
            ])
            ->add('group_poll', FieldsetType::class, [
                'legend' => 'label.messages_poll.label',
                'fields' => function(FormBuilderInterface $builder) {
                    $builder
                        ->add('isPollEnabled', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.messages_poll.enabled',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('allowPollRevote', CheckboxType::class, [
                            'required' => false,
                            'label' => 'label.messages_poll.allow_revote.label',
                            'help' => 'label.messages_poll.allow_revote.help',
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('pollUserTypes', UserTypeEntityType::class, [
                            'label' => 'label.usertypes',
                            'multiple' => true,
                            'expanded' => true,
                            'required' => false,
                            'label_attr' => [
                                'class' => 'checkbox-custom'
                            ]
                        ])
                        ->add('pollStudyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ],
                            'required' => false
                        ])
                        ->add('pollNumChoices', IntegerType::class, [
                            'label' => 'label.messages_poll.num_choices.label',
                            'help' => 'label.messages_poll.num_choices.help'
                        ])
                        ->add('pollChoices', CollectionType::class, [
                            'entry_options' => [
                                'label' => 'label.messages_poll.choices'
                            ],
                            'entry_type' => MessagePollChoiceType::class,
                            'allow_add' => true,
                            'allow_delete' => true,
                            'by_reference' => false
                        ]);
                }
            ]);
    }
}