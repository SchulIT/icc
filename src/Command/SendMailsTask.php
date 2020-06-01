<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Spooled emails are sent using this command.
 */
class SendMailsTask extends Command {

    private $messageLimit;
    private $transport;
    private $spool;

    public function __construct(int $messageLimit, \Swift_Transport $transport, \Swift_Spool $spool, string $name = null) {
        parent::__construct($name);

        $this->messageLimit = $messageLimit;
        $this->transport = $transport;
        $this->spool = $spool;
    }

    public function configure() {
        $this->setName('tasks:mails:send')
            ->setDescription('Sends next batch of mails');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        try {
            if ($this->spool instanceof \Swift_ConfigurableSpool) {
                $this->spool->setMessageLimit($this->messageLimit);
            }

            if ($this->spool instanceof \Swift_FileSpool) {
                $this->spool->recover();
            }

            $sent = $this->spool->flushQueue($this->transport);

            $style->success(sprintf('%d email(s) sent', $sent));
        } catch (\Throwable $e) {
            $this->getApplication()->renderThrowable($e, $output);
            return 1;
        }

        return 0;
    }
}