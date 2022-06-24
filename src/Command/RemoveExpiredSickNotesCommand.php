<?php

namespace App\Command;

use App\Repository\SickNoteAttachmentRepositoryInterface;
use App\Repository\SickNoteRepositoryInterface;
use App\Settings\SickNoteSettings;
use League\Flysystem\Filesystem;
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

    private SickNoteSettings $settings;
    private SickNoteRepositoryInterface $repository;
    private SickNoteAttachmentRepositoryInterface $attachmentRepository;
    private DateHelper $dateHelper;
    private Filesystem $filesystem;

    public function __construct(SickNoteSettings $settings, SickNoteRepositoryInterface $repository,
                                SickNoteAttachmentRepositoryInterface $attachmentRepository, Filesystem $filesystem,
                                DateHelper $dateHelper, string $name = null) {
        parent::__construct($name);

        $this->settings = $settings;
        $this->repository = $repository;
        $this->attachmentRepository = $attachmentRepository;
        $this->dateHelper = $dateHelper;
        $this->filesystem = $filesystem;
    }

    public function configure() {
        $this->setName('app:sick_notes:cleanup')
            ->setDescription('Sends notifications for messages which did not push any notification yet.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
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

        // Remove unused attachments
        $style->section('Check attachments');

        $exists = [ ];
        $count = 0;
        foreach($this->attachmentRepository->findAll() as $attachment) {
            if($this->filesystem->fileExists($attachment->getPath())) {
                $exists[] = $attachment->getPath();
            } else {
                $this->attachmentRepository->remove($attachment);
                $count++;
            }
        }

        $style->success(sprintf('%d orphaned attachment(s) removed from database.', $count));

        $count = 0;
        foreach($this->filesystem->listContents('/') as $content) {
            if(in_array($content['path'], $exists) !== true) {
                $this->filesystem->delete($content['path']);
                $count++;
            }
        }

        $style->success(sprintf('%d orphaned attachment(s) removed from filesystem.', $count));

        return 0;
    }
}