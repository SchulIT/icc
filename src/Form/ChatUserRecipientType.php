<?php

namespace App\Form;

use App\Converter\FancyUserStringConverter;
use App\Entity\User;
use App\Entity\UserType;
use App\Settings\ChatSettings;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChatUserRecipientType extends EntityType {

    public function __construct(ManagerRegistry $managerRegistry, private readonly TokenStorageInterface $tokenStorage, private readonly FancyUserStringConverter $userStringConverter, private readonly ChatSettings $chatSettings, private readonly TranslatorInterface $translator) {
        parent::__construct($managerRegistry);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'attr' => [
                'size' => 15,
                'data-choice' => 'true'
            ],
            'class' => User::class,
            'multiple' => true,
            'choice_label' => function(User $user) {
                return $this->userStringConverter->convert($user);
            },
            'query_builder' => function(EntityRepository $repository) {
                $user = $this->tokenStorage->getToken()?->getUser();
                $allowedUserTypes = [ ];

                if($user instanceof User) {
                    $allowedUserTypes = $this->chatSettings->getAllowedRecipients($user->getUserType());
                }

                return $repository->createQueryBuilder('u')
                    ->where('u.userType IN (:types)')
                    ->setParameter('types', $allowedUserTypes);
            },
            'placeholder' => $this->translator->trans('label.select.user')
        ]);

    }


}