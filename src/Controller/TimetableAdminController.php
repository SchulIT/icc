<?php

namespace App\Controller;

use App\Repository\TimetableWeekRepositoryInterface;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/timetable")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TimetableAdminController extends AbstractController {

    private TimetableWeekRepositoryInterface $weekRepository;

    public function __construct(TimetableWeekRepositoryInterface $weekRepository, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->weekRepository = $weekRepository;
    }

    /**
     * @Route("", name="admin_timetable")
     */
    public function index() {
        return $this->render('admin/timetable/index.html.twig', [
            'weeks' => $this->weekRepository->findAll()
        ]);
    }

}