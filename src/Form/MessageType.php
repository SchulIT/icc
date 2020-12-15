<?php

namespace App\Form;

use App\Entity\MessageScope;
use App\Security\Voter\MessageScopeVoter;
use App\Utils\ArrayUtils;
use FervoEnumBundle\Generated\Form\MessagePriorityType;
use FervoEnumBundle\Generated\Form\MessageScopeType;
use SchulIT\CommonBundle\Form\FieldsetType;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class MessageType extends AbstractType {

    private const YearsDelta = 1;

    private $dateHelper;

    private $authorizationChecker;

    public function __construct(DateHelper $dateHelper, AuthorizationCheckerInterface $authorizationChecker) {
        $this->dateHelper = $dateHelper;
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

                    $builder
                        ->add('title', TextType::class, [
                            'label' => 'label.title'
                        ])
                        ->add('scope', MessageScopeType::class, [
                            'label' => 'label.scope'
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
                            'widget' => 'single_text'
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
                        $builder->add('priority', MessagePriorityType::class, [
                            'label' => 'label.priority',
                            'attr' => [
                                'data-choice' => 'true'
                            ],
                            'help' => 'help.priority'
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
            ]);
    }
}