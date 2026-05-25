<?php

declare(strict_types=1);

namespace App\Chat\Form\Field;

use App\Chat\Settings\ChatSettings;
use App\Common\Converter\StudentStringConverter;
use App\Common\Converter\UserStringConverter;
use App\Common\Entity\User;
use App\Common\Form\Autocomplete\UserAutocompleteField;
use App\Common\Section\SectionResolverInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\Autocomplete\Form\AsEntityAutocompleteField;

#[AsEntityAutocompleteField]
class ChatUserAutocompleteField extends UserAutocompleteField {

    public function __construct(
        SectionResolverInterface $sectionResolver,
        StudentStringConverter $studentStringConverter,
        UserStringConverter $userStringConverter,
        TranslatorInterface $translator,
        private readonly TokenStorageInterface $tokenStorage,
        private readonly ChatSettings $chatSettings
    ) {
        parent::__construct($sectionResolver, $studentStringConverter, $userStringConverter, $translator);
    }

    public function configureOptions(OptionsResolver $resolver): void {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'security' => function(Security $security): bool {
                $user = $security->getUser();

                if(!$user instanceof User) {
                    return false;
                }

                $allowedUserTypes = $this->chatSettings->getEnabledUserTypes();

                return in_array($user->getUserType(), $allowedUserTypes);
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
            }
        ]);
    }
}
