<?php

namespace App\Messenger;

use App\Repository\UserRepositoryInterface;
use DateTime;
use Exception;
use GuzzleHttp\ClientInterface;
use Psr\Log\LoggerInterface;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateOrRemoveUserHandler {
    public function __construct(private readonly bool $isDebug,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly ClientInterface $client,
                                private readonly DateHelper $dateHelper,
                                private readonly LoggerInterface $logger) {

    }

    public function __invoke(UpdateOrRemoveUserMessage $message): void {
        try {
            $user = $this->userRepository->findOneById($message->getUserId());

            if($user === null) {
                $this->logger->notice(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d ignoriert. Grund: Benutzer existiert nicht mehr.', $message->getUserId()));
                return;
            }

            $response = $this->client->request('GET', sprintf('/api/user/%s', $user->getIdpId()), ['verify' => $this->isDebug !== true]);

            if($response->getStatusCode() === 404) { // User was removed
                $this->logger->info(sprintf('Lösche %s, da er/sie nicht mehr im Single-Sign-On existiert.', $user->getUsername()));
                $this->userRepository->remove($user);

                return;
            }

            if($response->getStatusCode() !== 200) {
                $this->logger->error(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d war nicht erfolgreich: HTTP Status Code %d', $message->getUserId(), $response->getStatusCode()));
                return;
            }

            $userData = json_decode($response->getBody());

            if(json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d war nicht erfolgreich: JSON Fehler. %s', $message->getUserId(), json_last_error_msg()));
                return;
            }

            if($userData->enabled_until !== null) {
                $enabledUntil = new DateTime($userData->enabled_until);
                if($enabledUntil < $this->dateHelper->getToday()) {
                    $this->logger->info(sprintf('Lösche %s, da er/sie im Single-Sign-On existiert nicht mehr aktiv ist.', $user->getUsername()));
                    $this->userRepository->remove($user);
                    return;
                }
            }

            $this->logger->debug(sprintf('Aktualisiere %s.', $user->getUsername()));

            $user->setUsername($userData->username);
            $user->setFirstname($userData->firstname);
            $user->setLastname($userData->lastname);
            $user->setEmail($userData->email);

            $this->userRepository->persist($user);
        } catch (Exception $e) {
            $this->logger->notice(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d ignoriert. Grund: %s.', $message->getUserId(), $e->getMessage()), [
                'exception' => $e
            ]);
        }
    }
}