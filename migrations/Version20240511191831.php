<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240511191831 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_poll_vote ADD student_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_87EF8888CB944F1A ON message_poll_vote (student_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_poll_vote DROP FOREIGN KEY FK_87EF8888CB944F1A');
        $this->addSql('DROP INDEX IDX_87EF8888CB944F1A ON message_poll_vote');
        $this->addSql('ALTER TABLE message_poll_vote DROP student_id');
    }
}
