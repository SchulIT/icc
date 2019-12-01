<?php

namespace App\View\Parameter;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ViewParameter {
    public const TableView = 'table';
    public const CardView = 'card';

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    public function getViewType(?string $viewType, User $user, string $sectionKey): string {
        if(!in_array($viewType, [ static::TableView, static::CardView ])) {
            $viewType = null;
        }

        $key = sprintf('view.%s', $sectionKey);

        if($viewType === null) {
            $viewType = $user->getData($key, null) ?? static::CardView;
        } else {
            $user->setData($key, $viewType);
            $this->em->persist($user);
            $this->em->flush();
        }

        return $viewType;
    }
}