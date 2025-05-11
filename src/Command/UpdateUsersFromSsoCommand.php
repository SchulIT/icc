<?php

namespace App\Command;

use App\Messenger\UpdateOrRemoveUserMessage;
use App\Repository\UserRepositoryInterface;
use Shapecode\Bundle\CronBundle\Attribute\AsCronJob;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('app:users:update', 'Aktualisiert alle Benutzer aus dem Single-Sign-On und löscht sie bei Bedarf aus dem System (falls aktiviert)')]
#[AsCronJob('@daily')]
class UpdateUsersFromSsoCommand extends Command {
    public function __construct(private readonly string|null $ssoUrl,
                                private readonly string|null $ssoToken,
                                #[Autowire(env: 'SSO_USER_UPDATE')] private readonly bool $enabled,
                                private readonly UserRepositoryInterface $userRepository,
                                private readonly MessageBusInterface $messageBus,
                                string $name = null) {
        parent::__construct($name);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $style = new SymfonyStyle($input, $output);

        if($this->enabled === false) {
            $style->info('Der Parameter SSO_USER_UPDATE muss auf true gesetzt werden, um die Funktion zu aktivieren. Bitte im Handbuch nachlesen.');
            return Command::SUCCESS;
        }

        if($this->ssoUrl === null) {
            $style->info('Kein Abgleich möglich, da die Umgebungsvariable SSO_URL nicht gesetzt ist.');
            return Command::SUCCESS;
        }

        if($this->ssoToken === null) {
            $style->info('Kein Abgleich möglich, da die Umgebungsvariable SSO_APITOKEN nicht gesetzt ist.');
            return Command::SUCCESS;
        }

        $count = 0;
        foreach($this->userRepository->findAll() as $user) {
            $this->messageBus->dispatch(new UpdateOrRemoveUserMessage($user->getId()));
            $count++;
        }

        $style->success(sprintf('%d Benutzer werden in nächster Zeit synchronisiert.', $count));
        return Command::SUCCESS;
    }
}