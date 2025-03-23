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
readonly class UpdateOrRemoveUserHandler {

    public function __construct(private bool                    $isDebug,
                                private UserRepositoryInterface $userRepository,
                                private ClientInterface         $client,
                                private DateHelper              $dateHelper,
                                private LoggerInterface         $logger) {

    }

    public function __invoke(UpdateOrRemoveUserMessage $message): array {
        try {
            $user = $this->userRepository->findOneById($message->getUserId());

            if($user === null) {
                $this->logger->notice(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d ignoriert. Grund: Benutzer existiert nicht mehr.', $message->getUserId()));
                return [
                    'userId' => $message->getUserId(),
                    'action' => 'ignore',
                    'reason' => 'non_existent_user'
                ];
            }

            $response = $this->client->request('GET', sprintf('/api/user/%s', $user->getIdpId()), ['verify' => $this->isDebug !== true]);

            if($response->getStatusCode() === 404) { // User was removed
                $this->logger->info(sprintf('Lösche %s, da er/sie nicht mehr im Single-Sign-On existiert.', $user->getUsername()));
                $this->userRepository->remove($user);

                return [
                    'userId' => $message->getUserId(),
                    'username' => $user->getUsername(),
                    'action' => 'remove',
                    'reason' => 'removed_in_sso'
                ];
            }

            if($response->getStatusCode() !== 200) {
                $this->logger->error(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d war nicht erfolgreich: HTTP Status Code %d', $message->getUserId(), $response->getStatusCode()));
                return [
                    'userId' => $message->getUserId(),
                    'username' => $user->getUsername(),
                    'action' => 'ignore',
                    'reason' => 'invalid_http_status_code',
                    'status_code' => $response->getStatusCode()
                ];
            }

            $json = $response->getBody()->getContents();
            $userData = json_decode($json);

            if(json_last_error() !== JSON_ERROR_NONE) {
                $this->logger->error(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d war nicht erfolgreich: JSON Fehler. %s', $message->getUserId(), json_last_error_msg()));
                return [
                    'userId' => $message->getUserId(),
                    'username' => $user->getUsername(),
                    'action' => 'ignore',
                    'reason' => 'json_error',
                    'json' => $json,
                    'error' => json_last_error_msg()
                ];
            }

            if($userData->enabled_until !== null) {
                $enabledUntil = new DateTime($userData->enabled_until);
                if($enabledUntil < $this->dateHelper->getToday()) {
                    $this->logger->info(sprintf('Lösche %s, da er/sie im Single-Sign-On existiert nicht mehr aktiv ist.', $user->getUsername()));
                    $this->userRepository->remove($user);
                    return [
                        'userId' => $message->getUserId(),
                        'username' => $user->getUsername(),
                        'action' => 'remove',
                        'reason' => 'not_active_in_sso'
                    ];
                }
            }

            $this->logger->debug(sprintf('Aktualisiere %s.', $user->getUsername()));

            $user->setUsername($userData->username);
            $user->setFirstname($userData->firstname);
            $user->setLastname($userData->lastname);
            $user->setEmail($userData->email);

            $this->userRepository->persist($user);

            return [
                'userId' => $message->getUserId(),
                'username' => $user->getUsername(),
                'action' => 'update'
            ];
        } catch (Exception $e) {
            $this->logger->notice(sprintf('UpdateOrRemoveUserMessage für Benutzer mit ID %d ignoriert. Grund: %s.', $message->getUserId(), $e->getMessage()), [
                'exception' => $e
            ]);

            return [
                'userId' => $message->getUserId(),
                'action' => 'unknown',
                'reason' => 'exception',
                'exception' => $e->getMessage()
            ];
        }
    }
}