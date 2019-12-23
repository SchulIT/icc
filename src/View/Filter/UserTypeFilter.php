<?php

namespace App\View\Filter;

use App\Entity\User;
use App\Entity\UserType;
use App\Security\Voter\DocumentVoter;
use App\Sorting\Sorter;
use App\Sorting\UserTypeStrategy;
use App\Utils\ArrayUtils;
use App\Utils\EnumArrayUtils;
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

    /**
     * @param string|null $userType
     * @param User|null $user
     * @param bool $isRestrictedToOwnType
     * @param UserType|null $defaultType
     * @param UserType[] $onlyTypes Restrict to the given user types
     * @return UserTypeFilterView
     */
    public function handle(?string $userType, User $user = null, bool $isRestrictedToOwnType = false, ?UserType $defaultType = null, array $onlyTypes = [ ]) {
        if($isRestrictedToOwnType === true) {
            return new UserTypeFilterView([ ], $user !== null ? $user->getUserType() : $defaultType);
        }

        if(empty($onlyTypes)) {
            $enums = $this->types;
        } else {
            $enums = $onlyTypes;
        }

        $types = ArrayUtils::createArrayWithKeys($enums, function(UserType $type) {
            return $type->getValue();
        });

        if($user !== null) {
            $type = $userType == null ?
                $types[$userType] ?? $user->getUserType() : $user->getUserType();
        } else {
            $type = $types[$userType] ?? $defaultType;
        }

        $this->sorter->sort($types, UserTypeStrategy::class);

        return new UserTypeFilterView($types, $type);
    }
}