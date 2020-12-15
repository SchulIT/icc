<?php

namespace App\Command;

use App\Repository\SickNoteRepositoryInterface;
use App\Settings\SickNoteSettings;
use SchulIT\CommonBundle\Helper\DateHelper;
use Shapecode\Bundle\CronBundle\Annotation\CronJob;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @CronJob("@daily")
 */
class RemoveExpiredSickNotesCommand extends Command {

    private $settings;
    private $repository;
    private $dateHelper;

    public function __construct(SickNoteSettings $settings, SickNoteRepositoryInterface $repository, DateHelper $dateHelper, string $name = null) {
        parent::__construct($name);

        $this->settings = $settings;
        $this->repository = $repository;
        $this->dateHelper = $dateHelper;
    }

    public function configure() {
        $this->setName('app:sick_notes:cleanup')
            ->setDescription('Sends notifications for messages which did not push any notification yet.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $style = new SymfonyStyle($input, $output);

        $days = $this->settings->getRetentionDays();

        if($days === 0) {
            $style->success('Retention days is set to 0 - do not remove any.');
            return 0;
        }

        $threshold = $this->dateHelper->getToday()
            ->modify(sprintf('-%d days', $days));

        $style->text(sprintf('Removing sick notes before %s', $threshold->format('r')));

        $count = $this->repository->removeExpired($threshold);

        $style->success(sprintf('%d sick note(s) removed.', $count));

        return 0;
    }
}