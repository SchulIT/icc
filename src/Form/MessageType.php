<?php

namespace App\Form;

use App\Entity\MessageScope;
use App\Security\Voter\MessageScopeVoter;
use App\Utils\ArrayUtils;
use FervoEnumBundle\Generated\Form\MessageScopeType;
use SchoolIT\CommonBundle\Form\FieldsetType;
use SchoolIT\CommonBundle\Helper\DateHelper;
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

                    $scopes = array_filter(ArrayUtils::createArray(MessageScope::keys(), MessageScope::values()),
                        function(MessageScope $scope) {
                            return $this->authorizationChecker->isGranted(MessageScopeVoter::USE, $scope);
                        }
                    );

                    $builder
                        ->add('title', TextType::class, [
                            'label' => 'label.title'
                        ])
                        ->add('scope', MessageScopeType::class, [
                            'choices' => $scopes,
                            'label' => 'label.scope'
                        ])
                        ->add('visibilities', UserTypeEntityType::class, [
                            'label' => 'label.visibility',
                            'multiple' => true,
                            'expanded' => true,
                        ])
                        ->add('studyGroups', StudyGroupType::class, [
                            'label' => 'label.study_groups_simple',
                            'multiple' => true,
                            'attr' => [
                                'size' => 10
                            ]
                        ])
                        ->add('mustConfirm', CheckboxType::class, [
                            'label' => 'label.must_confirm',
                            'required' => false
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