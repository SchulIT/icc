<?php

namespace App\Controller\Admin;

use SchulIT\CommonBundle\Controller\LogController as BaseLogController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/logs')]
#[IsGranted('ROLE_ADMIN')]
class LogsController extends BaseLogController {

}
