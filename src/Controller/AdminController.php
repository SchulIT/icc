<?php

namespace App\Controller;

use App\Menu\Builder;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 */
class AdminController extends AbstractController {

    /**
     * @Route("", name="admin")
     */
    public function index(Builder $menuBuilder) {
        $adminMenu = $menuBuilder->adminMenu([]);

        return $this->render('admin/index.html.twig', [
            'menu' => $adminMenu->getChildren()
        ]);
    }
}