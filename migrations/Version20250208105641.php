<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208105641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_limited_membership_audit CHANGE diffs diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_week RENAME INDEX uniq_ce6683ce8a90aba9 TO UNIQ_CE6683CE4E645A7E');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_limited_membership_audit CHANGE diffs diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE timetable_week RENAME INDEX uniq_ce6683ce4e645a7e TO UNIQ_CE6683CE8A90ABA9');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
