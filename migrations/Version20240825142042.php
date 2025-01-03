<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240825142042 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_integrity_check_violation ADD event_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE book_integrity_check_violation ADD CONSTRAINT FK_357D545671F7E88B FOREIGN KEY (event_id) REFERENCES book_event (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_357D545671F7E88B ON book_integrity_check_violation (event_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_integrity_check_violation DROP FOREIGN KEY FK_357D545671F7E88B');
        $this->addSql('DROP INDEX IDX_357D545671F7E88B ON book_integrity_check_violation');
        $this->addSql('ALTER TABLE book_integrity_check_violation DROP event_id');
    }
}
