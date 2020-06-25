<?php

namespace App\Controller;

use App\Menu\Builder;
use Symfony\Component\Routing\Annotation\Route;

class SystemController extends AbstractController {

    /**
     * @Route("/admin/system", name="system")
     */
    public function index(Builder $menuBuilder) {
        $adminMenu = $menuBuilder->systemMenu([]);

        return $this->render('admin/system.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}