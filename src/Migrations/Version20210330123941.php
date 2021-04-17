<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210330123941 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE timetable_week_week');
        $this->addSql('SET FOREIGN_KEY_CHECKS=0;');
        $this->addSql('ALTER TABLE student CHANGE unique_identifier unique_identifier VARCHAR(255) NOT NULL');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1;');
        $this->addSql('ALTER TABLE week ADD timetable_week_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE week ADD CONSTRAINT FK_5B5A69C0490F7151 FOREIGN KEY (timetable_week_id) REFERENCES timetable_week (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_5B5A69C0490F7151 ON week (timetable_week_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable_week_week (timetable_week_id INT UNSIGNED NOT NULL, week_id INT UNSIGNED NOT NULL, INDEX IDX_B6951BE6C86F3B2F (week_id), INDEX IDX_B6951BE6490F7151 (timetable_week_id), PRIMARY KEY(timetable_week_id, week_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE timetable_week_week ADD CONSTRAINT FK_B6951BE6490F7151 FOREIGN KEY (timetable_week_id) REFERENCES timetable_week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_week_week ADD CONSTRAINT FK_B6951BE6C86F3B2F FOREIGN KEY (week_id) REFERENCES week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student CHANGE unique_identifier unique_identifier VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE week DROP FOREIGN KEY FK_5B5A69C0490F7151');
        $this->addSql('DROP INDEX IDX_5B5A69C0490F7151 ON week');
        $this->addSql('ALTER TABLE week DROP timetable_week_id');
    }
}
