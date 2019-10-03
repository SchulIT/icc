<?php

namespace App\View\Filter;

use App\Entity\User;
use App\Entity\UserType;
use App\Security\Voter\DocumentVoter;
use App\Sorting\Sorter;
use App\Sorting\UserTypeStrategy;
use App\Utils\ArrayUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserTypeFilter {
    private $sorter;
    private $types;
    private $authorizationChecker;

    public function __construct(Sorter $sorter, AuthorizationCheckerInterface $authorizationChecker) {
        $this->sorter = $sorter;
        $this->types = UserType::values();
        $this->authorizationChecker = $authorizationChecker;
    }

    public function handle(?string $userType, User $user = null, bool $isRestrictedToOwnType = false) {
        if($isRestrictedToOwnType === true) {
            return new UserTypeFilterView([ ], $user !== null ? $user->getUserType() : null);
        }

        $types = ArrayUtils::createArrayWithKeys(UserType::values(), function(UserType $type) {
            return $type->getValue();
        });


        if($user !== null) {
            $type = $userType == null ?
                $types[$userType] ?? $user->getUserType() : $user->getUserType();
        } else {
            $type = $types[$userType] ?? null;
        }

        $this->sorter->sort($types, UserTypeStrategy::class);

        return new UserTypeFilterView($types, $type);
    }
}