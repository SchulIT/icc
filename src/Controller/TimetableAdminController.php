<?php

namespace App\Controller;

use App\Repository\TimetablePeriodRepositoryInterface;
use App\Repository\TimetableWeekRepositoryInterface;
use App\Sorting\Sorter;
use App\Sorting\TimetablePeriodStrategy;
use SchoolIT\CommonBundle\Utils\RefererHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/timetable")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class TimetableAdminController extends AbstractController {

    private $weekRepository;
    private $periodRepository;

    private $sorter;

    public function __construct(TimetableWeekRepositoryInterface $weekRepository, TimetablePeriodRepositoryInterface $periodRepository, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($refererHelper);

        $this->weekRepository = $weekRepository;
        $this->periodRepository = $periodRepository;
        $this->sorter = $sorter;
    }

    /**
     * @Route("", name="admin_timetable")
     */
    public function index() {
        $periods = $this->periodRepository->findAll();
        $this->sorter->sort($periods, TimetablePeriodStrategy::class);

        return $this->render('admin/timetable/index.html.twig', [
            'weeks' => $this->weekRepository->findAll(),
            'periods' => $periods
        ]);
    }

}