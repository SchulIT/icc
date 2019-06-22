<?php

namespace App\Rooms;

use App\Repository\RoomRepositoryInterface;
use App\Repository\RoomTagRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

class RoomQueryBuilder {

    private $roomTagRepository;
    private $roomRepository;

    public function __construct(RoomTagRepositoryInterface $roomTagRepository, RoomRepositoryInterface $roomRepository) {
        $this->roomTagRepository = $roomTagRepository;
        $this->roomRepository = $roomRepository;
    }

    /**
     * @param Request $request
     * @return RoomQuery
     */
    public function buildFromRequest(Request $request) {
        $query = new RoomQuery();

        /**
         * Name
         */
        if($request->request->get('name', false) !== false) {
            $value = $request->request->get('name-value', null);

            if(!empty($value)) {
                $query->setName($value);
            }
        }

        /**
         * Seats
         */
        if($request->request->get('seats', false) !== false) {
            $value = $request->request->get('seats-value', 0);

            if(!empty($value)) {
                $query->addSeats($value);
            }
        }

        /**
         * Tags
         */
        foreach($this->roomTagRepository->findAll() as $tag) {
            $paramName = sprintf('tag-%s', $tag->getId());

            if($request->request->get($paramName, null) !== null) {
                if($tag->hasValue()) {
                    $valueParam = sprintf('tag-%s-value', $tag->getId());
                    $value = $request->request->get($valueParam, 0);

                    if(!empty($value) || $value == 0)  {
                        $query->addTagWithValue($tag, $value);
                    }
                } else {
                    $query->addTag($tag);
                }
            }
        }

        return $query;
    }
}