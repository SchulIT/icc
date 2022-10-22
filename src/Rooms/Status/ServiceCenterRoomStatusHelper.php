<?php

namespace App\Rooms\Status;

use GuzzleHttp\ClientInterface;
use JMS\Serializer\SerializerInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;

class ServiceCenterRoomStatusHelper implements StatusHelperInterface {
    public function __construct(private bool $isEnabled, private CacheItemPoolInterface $cache, private ClientInterface $client, private LoggerInterface $logger)
    {
    }

    public function retrieveFromRemote(): void {
        if($this->isEnabled !== true) {
            return;
        }

        $response = $this->client->request('GET','/api/status');

        if($response->getStatusCode() !== 200) {
            $this->logger->error(sprintf('Failed to retrieve room status from ServiceCenter - status code: %d', $response->getStatusCode()));
            throw new RuntimeException(sprintf('Failed to retrieve room status from ServiceCenter - status code: %d', $response->getStatusCode()));
        }

        $status = json_decode($response->getBody());

        if(json_last_error() !== JSON_ERROR_NONE) {
            $this->logger->error(sprintf('Failed to decode room status JSON from ServiceCenter - error: %d (%s)', json_last_error(), json_last_error_msg()));
            throw new RuntimeException(sprintf('Failed to decode room status JSON from ServiceCenter - error: %d (%s)', json_last_error(), json_last_error_msg()));
        }

        $roomStatuses = [ ];

        foreach($status->rooms as $roomStatus) {
            $roomStatuses[] = (new RoomStatus())
                ->setLink($roomStatus->link)
                ->setName($roomStatus->name)
                ->addBadge(
                    (new RoomStatusBadge())
                        ->setLabel('servicecenter.problem')
                        ->setIcon('fas fa-exclamation-circle')
                        ->setCounter($roomStatus->numProblems)
                )
                ->addBadge(
                    (new RoomStatusBadge())
                        ->setLabel('servicecenter.maintenance')
                        ->setIcon('fas fa-wrench')
                        ->setCounter($roomStatus->numMaintanance)
                );
        }

        $item = $this->cache->getItem('rooms.status.service_center');
        $item->set(serialize($roomStatuses));
        $this->cache->save($item);
    }

    public function getStatus(string $room): ?RoomStatus {
        if($this->isEnabled !== true) {
            return null;
        }

        $item = $this->cache->getItem('rooms.status.service_center');

        if(!$item->isHit()) {
            return null;
        }

        /** @var RoomStatus[] $statuses */
        $statuses = unserialize($item->get());

        foreach($statuses as $roomStatus) {
            if(str_starts_with($roomStatus->getName(), $room)) {
                return $roomStatus;
            }
        }

        return null;
    }
}