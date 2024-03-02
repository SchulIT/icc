<?php

namespace App\Command;

use App\Entity\Chat;
use App\Entity\ChatMessageAttachment;
use App\Entity\WikiArticle;
use App\Filesystem\ChatFilesystem;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\DirectoryAttributes;
use League\Flysystem\FileAttributes;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:filesystem:wiki:cleanup', description: 'Räumt den Ordner files/wiki/ auf und synchronisiert ihn mit der Datenbank.')]
class CleanupWikiFilesystemCommand extends Command {
    public function __construct(private readonly FilesystemOperator $wikiFilesystem,
                                private readonly EntityManagerInterface $em,
                                string $name = null) {
        parent::__construct($name);
    }

    public function configure(): void {
        $this->addOption('dry-run', 'd', InputOption::VALUE_OPTIONAL, 'Nur prüfen und nichts löschen.', false);
    }

    public function execute(InputInterface $input, OutputInterface $output): int {
        $dryRun = $input->getOption('dry-run') !== false;
        $style = new SymfonyStyle($input, $output);

        if($dryRun === true) {
            $style->info('Diese Operation wird als `dry-run` ausgeführt. Es wird nichts gelöscht.');
        }

        $style->section('Prüfe auf verwaise Wiki-Dateien');

        foreach($this->wikiFilesystem->listContents('/') as $item) {
            if(!$item instanceof FileAttributes) {
                continue;
            }

            $style->write(sprintf('Prüfe Datei %s: ', $item->path()));

            $articles = $this->em->createQueryBuilder()
                ->select('a')
                ->from(WikiArticle::class, 'a')
                ->where('a.content LIKE :query')
                ->setParameter('query', '%' . $item->path() . '%')
                ->getQuery()
                ->getResult();

            if(count($articles) === 0) {
                if($dryRun) {
                    $style->writeln('nicht in Datenbank vorhanden. Unternehme nichts (dry-run).');
                } else {
                    $style->writeln('nicht in Datenbank vorhanden. Lösche Datei.');
                    $this->wikiFilesystem->delete($item->path());
                }
            } else {
                $style->writeln('in Datenbank vorhanden. Unternehme nichts.');
            }
        }

        return Command::SUCCESS;
    }
}