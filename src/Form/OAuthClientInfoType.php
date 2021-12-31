<?php

namespace App\Form;

use App\Entity\OAuthClientInfo;
use App\Utils\ArrayUtils;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class OAuthClientInfoType extends AbstractType {

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefault('client', true);
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.name'
            ])
            ->add('description', TextareaType::class, [
                'label' => 'label.description'
            ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function(FormEvent $event) use($options) {
            $form = $event->getForm();
            /** @var OAuthClientInfo $info */
            $info = $event->getData();

            if($info->getClient() !== null && $options['client'] === true) {
                $client = $info->getClient();

                $form
                    ->add('active', CheckboxType::class, [
                        'required' => false,
                        'label' => 'label.active',
                        'data' => $client->isActive(),
                        'mapped' => false,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ]
                    ])
                    ->add('identifier', TextType::class, [
                        'disabled' => true,
                        'required' => false,
                        'label' => 'label.identifier',
                        'data' => $client->getIdentifier(),
                        'mapped' => false
                    ])
                    ->add('secret', TextType::class, [
                        'disabled' => true,
                        'required' => false,
                        'label' => 'label.secret',
                        'data' => $client->getSecret(),
                        'mapped' => false
                    ])
                    ->add('redirect_uris', CollectionType::class, [
                        'entry_type' => RedirectUriType::class,
                        'allow_add' => true,
                        'allow_delete' => true,
                        'delete_empty' => true,
                        'data' => ArrayUtils::toString($client->getRedirectUris()),
                        'mapped' => false
                    ])
                    ->add('grants', ChoiceType::class, [
                        'multiple' => true,
                        'expanded' => true,
                        'choices' => [
                            $this->translator->trans('grants.authorization_code', [], 'oauth') => 'authorization_code',
                            $this->translator->trans('grants.client_credentials', [], 'oauth') => 'client_credentials',
                            $this->translator->trans('grants.implicit', [], 'oauth') => 'implicit',
                            $this->translator->trans('grants.password', [], 'oauth') => 'password',
                            $this->translator->trans('grants.refresh_token', [], 'oauth') =>'refresh_token'
                        ],
                        'data' => ArrayUtils::toString($client->getGrants()),
                        'mapped' => false,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ],
                        'help' => $this->translator->trans('grants._help', [], 'oauth')
                    ])
                    ->add('scopes', ChoiceType::class, [
                        'multiple' => true,
                        'expanded' => true,
                        'choices' => [
                            $this->translator->trans('scopes.exams', [], 'oauth') => 'exams',
                            $this->translator->trans('scopes.messages', [], 'oauth') => 'messages',
                            $this->translator->trans('scopes.substitutions', [], 'oauth') => 'substitutions',
                            $this->translator->trans('scopes.timetable', [], 'oauth') => 'timetable',
                            $this->translator->trans('scopes.appointments', [], 'oauth') => 'appointments'
                        ],
                        'data' => ArrayUtils::toString($client->getScopes()),
                        'mapped' => false,
                        'label_attr' => [
                            'class' => 'checkbox-custom'
                        ],
                        'help' => $this->translator->trans('scopes._help', [], 'oauth')
                    ]);
            }
        });
    }
}