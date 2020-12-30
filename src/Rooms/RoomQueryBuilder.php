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

    public function serialize(RoomQuery $query): array {
        $data = [ ];

        if($query->hasSeats()) {
            $data['seats'] = $query->getSeatsValueOrDefault();
        }

        foreach($this->roomTagRepository->findAll() as $tag) {
            $paramName = sprintf('tag-%s', $tag->getUuid());

            if($query->hasTag($tag)) {
                $data[$paramName] = '✓';

                if($tag->hasValue()) {
                    $valueParam = sprintf('tag-%s-value', $tag->getUuid());
                    $data[$valueParam] = $query->getValueOrDefault($tag);
                }
            }
        }

        return $data;
    }

    /**
     * @param Request $request
     * @return RoomQuery
     */
    public function buildFromRequest(Request $request) {
        $query = new RoomQuery();

        /**
         * Seats
         */
        if($request->query->get('seats', false) !== false) {
            $value = $request->query->get('seats-value', 0);

            if(!empty($value)) {
                $query->addSeats($value);
            }
        }

        /**
         * Tags
         */
        foreach($this->roomTagRepository->findAll() as $tag) {
            $paramName = sprintf('tag-%s', $tag->getUuid());

            if($request->query->get($paramName, null) !== null) {
                if($tag->hasValue()) {
                    $valueParam = sprintf('tag-%s-value', $tag->getUuid());
                    $value = $request->query->get($valueParam, 0);

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