<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221022135542 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_poll_vote DROP FOREIGN KEY FK_87EF8888537A1329');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence DROP FOREIGN KEY FK_9B6C5531B03A8386');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C5531B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_message DROP FOREIGN KEY FK_239F9DD1B03A8386');
        $this->addSql('ALTER TABLE student_absence_message ADD CONSTRAINT FK_239F9DD1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_poll_vote DROP FOREIGN KEY FK_87EF8888537A1329');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE student_absence DROP FOREIGN KEY FK_9B6C5531B03A8386');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C5531B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_absence_message DROP FOREIGN KEY FK_239F9DD1B03A8386');
        $this->addSql('ALTER TABLE student_absence_message ADD CONSTRAINT FK_239F9DD1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
    }
}
