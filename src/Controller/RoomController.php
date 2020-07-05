<?php

namespace App\Controller;

use App\Entity\MessageScope;
use App\Entity\Room;
use App\Message\DismissedMessagesHelper;
use App\Repository\ImportDateTypeRepositoryInterface;
use App\Repository\MessageRepositoryInterface;
use App\Repository\RoomRepositoryInterface;
use App\Repository\RoomTagRepositoryInterface;
use App\Rooms\RoomQueryBuilder;
use App\Security\Voter\RoomVoter;
use App\Sorting\RoomNameStrategy;
use App\Sorting\Sorter;
use SchulIT\CommonBundle\Helper\DateHelper;
use SchulIT\CommonBundle\Utils\RefererHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomController extends AbstractControllerWithMessages {

    private $sorter;

    public function __construct(MessageRepositoryInterface $messageRepository, DismissedMessagesHelper $dismissedMessagesHelper,
                                DateHelper $dateHelper, Sorter $sorter, RefererHelper $refererHelper) {
        parent::__construct($messageRepository, $dismissedMessagesHelper, $dateHelper, $refererHelper);

        $this->sorter = $sorter;
    }

    /**
     * @Route("/rooms", name="rooms")
     */
    public function index(Request $request, RoomQueryBuilder $queryBuilder, RoomRepositoryInterface $roomRepository,
                          RoomTagRepositoryInterface $roomTagRepository, ImportDateTypeRepositoryInterface $importDateTypeRepository) {
        $this->denyAccessUnlessGranted(RoomVoter::View);

        $query = $queryBuilder->buildFromRequest($request);

        if($query->hasConditions() || $query->hasName()) {
            $rooms = $roomRepository->findAllByQuery($query);
        } else {
            $rooms = $roomRepository->findAll();
        }

        $this->sorter->sort($rooms, RoomNameStrategy::class);

        return $this->renderWithMessages('rooms/index.html.twig', [
            'rooms' => $rooms,
            'query' => $query,
            'tags' => $roomTagRepository->findAll(),
            'last_import' => $importDateTypeRepository->findOneByEntityClass(Room::class)
        ]);
    }

    protected function getMessageScope(): MessageScope {
        return MessageScope::Lists();
    }
}