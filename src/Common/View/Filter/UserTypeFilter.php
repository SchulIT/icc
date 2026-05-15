<?php

namespace App\Common\View\Filter;

use App\Common\Entity\User;
use App\Common\Entity\UserType;
use App\Document\Voter\DocumentVoter;
use App\Framework\Sorting\Sorter;
use App\Common\Sorting\UserTypeStrategy;
use App\Framework\Utils\ArrayUtils;
use App\Framework\Utils\EnumArrayUtils;
use App\Common\View\Filter\UserTypeFilterView;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserTypeFilter {
    private array $types;

    public function __construct(private Sorter $sorter) {
        $this->types = UserType::cases();
    }

    /**
     * @param string|null $userType
     * @param User|null $user
     * @param bool $isRestrictedToOwnType
     * @param UserType|null $defaultType
     * @param UserType[] $onlyTypes Restrict to the given user types
     * @return UserTypeFilterView
     */
    public function handle(?string $userType, ?User $user = null, bool $isRestrictedToOwnType = false, ?UserType $defaultType = null, array $onlyTypes = [ ]): UserTypeFilterView {
        if($isRestrictedToOwnType === true) {
            return new UserTypeFilterView([ ], $user !== null ? $user->getUserType() : $defaultType);
        }

        if(empty($onlyTypes)) {
            $enums = $this->types;
        } else {
            $enums = $onlyTypes;
        }

        $types = ArrayUtils::createArrayWithKeys($enums, fn(UserType $type) => $type->value);

        if($user === null) {
            $fallbackUserType = null;
        } else {
            $fallbackUserType = $types[$user->getUserType()->value] ?? null;
        }

        if($user !== null) {
            $type = $userType != null ?
                $types[$userType] ?? $fallbackUserType : $fallbackUserType;
        } else {
            $type = $types[$userType] ?? $defaultType;
        }

        $this->sorter->sort($types, UserTypeStrategy::class);

        return new UserTypeFilterView($types, $type);
    }
}