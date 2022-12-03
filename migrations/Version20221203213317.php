<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203213317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document ADD created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE updated_at updated_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE message_poll_choice CHANGE mininum minimum INT NOT NULL');
        $this->addSql('DROP INDEX uniq_ce6683ce4e645a7e ON timetable_week');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE6683CE8A90ABA9 ON timetable_week (`key`)');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC06514BFC18');
        $this->addSql('DROP INDEX idx_22cddc06514bfc18 ON wiki');
        $this->addSql('CREATE INDEX IDX_22CDDC063D8E604F ON wiki (parent)');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC06514BFC18 FOREIGN KEY (parent) REFERENCES wiki (id) ON DELETE CASCADE');
        $this->addSql('UPDATE document SET created_at = updated_at');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE document DROP created_at');
        $this->addSql('ALTER TABLE message CHANGE updated_at updated_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE message_poll_choice CHANGE minimum mininum INT NOT NULL');
        $this->addSql('DROP INDEX uniq_ce6683ce8a90aba9 ON timetable_week');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CE6683CE4E645A7E ON timetable_week (`key`)');
        $this->addSql('ALTER TABLE wiki DROP FOREIGN KEY FK_22CDDC063D8E604F');
        $this->addSql('DROP INDEX idx_22cddc063d8e604f ON wiki');
        $this->addSql('CREATE INDEX IDX_22CDDC06514BFC18 ON wiki (parent)');
        $this->addSql('ALTER TABLE wiki ADD CONSTRAINT FK_22CDDC063D8E604F FOREIGN KEY (parent) REFERENCES wiki (id) ON DELETE CASCADE');
    }
}
