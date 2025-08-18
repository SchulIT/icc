<?php

namespace App\Command;

use App\Repository\StudentAbsenceAttachmentRepositoryInterface;
use App\Repository\StudentAbsenceRepositoryInterface;
use App\Settings\StudentAbsenceSettings;
use League\Flysystem\Filesystem;
use SchulIT\CommonBundle\Helper\DateHelper;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('@monthly')]
#[AsCommand('app:absences:cleanup', 'Löscht abgelaufene Abwesenheitsmeldungen (Lernende).')]
readonly class RemoveExpiredStudentAbsencesCommand {

    public function __construct(private StudentAbsenceSettings $settings, private StudentAbsenceRepositoryInterface $repository,
                                private StudentAbsenceAttachmentRepositoryInterface $attachmentRepository, private Filesystem $filesystem,
                                private DateHelper $dateHelper) { }

    public function __invoke(SymfonyStyle $style, OutputInterface $output): int {
        $days = $this->settings->getRetentionDays();

        if($days === 0) {
            $style->success('Laut Einstellung sollen alle Abwesenheitsmeldungen behalten werden - lösche nichts.');
            return 0;
        }

        $threshold = $this->dateHelper->getToday()
            ->modify(sprintf('-%d days', $days));

        $style->text(sprintf('Lösche Abwesenheitsmeldungen vor %s', $threshold->format('r')));

        $count = $this->repository->removeExpired($threshold);

        $style->success(sprintf('%d Abwesenheitsmeldung(en) gelöscht', $count));

        // Remove unused attachments
        $style->section('Anhänge prüfen');

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

        $style->success(sprintf('%d verwaise Anhänge aus der Datenbank entfernt', $count));

        $count = 0;
        foreach($this->filesystem->listContents('/') as $content) {
            if(in_array($content['path'], $exists) !== true) {
                $this->filesystem->delete($content['path']);
                $count++;
            }
        }

        $style->success(sprintf('%d verwaiste Anhänge vom Datensystem gelöscht', $count));

        return Command::SUCCESS;
    }
}