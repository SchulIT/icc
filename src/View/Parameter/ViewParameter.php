<?php

namespace App\View\Parameter;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ViewParameter {
    public const TableView = 'table';
    public const CardView = 'card';

    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getViewType(?string $viewType, User $user, string $sectionKey): string {
        if(!in_array($viewType, [ self::TableView, self::CardView ])) {
            $viewType = null;
        }

        $key = sprintf('view.%s', $sectionKey);

        if($viewType === null) {
            $viewType = $user->getData($key, null) ?? self::CardView;
        } else {
            $user->setData($key, $viewType);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $viewType;
    }
}