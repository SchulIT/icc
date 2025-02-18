<?php

namespace App\Controller;

use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Zenstruck\Messenger\Monitor\Controller\MessengerMonitorController as BaseMessengerMonitorController;

#[Route('/admin/messenger')]
#[IsGranted('ROLE_SUPER_ADMIN')]
final class MessengerMonitorController extends BaseMessengerMonitorController
{
}