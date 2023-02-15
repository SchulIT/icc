<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230215092546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE learning_management_system (id INT UNSIGNED AUTO_INCREMENT NOT NULL, external_id VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_6F4915539F75D7B0 (external_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_learning_management_system_information (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, lms_id INT UNSIGNED DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, is_consented TINYINT(1) NOT NULL, is_consent_obtained TINYINT(1) NOT NULL, is_audio_consented TINYINT(1) NOT NULL, is_video_consented TINYINT(1) NOT NULL, INDEX IDX_AAFEBB22CB944F1A (student_id), INDEX IDX_AAFEBB224FDCCE2E (lms_id), UNIQUE INDEX UNIQ_AAFEBB22CB944F1A4FDCCE2E (student_id, lms_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_learning_management_system_information ADD CONSTRAINT FK_AAFEBB22CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_learning_management_system_information ADD CONSTRAINT FK_AAFEBB224FDCCE2E FOREIGN KEY (lms_id) REFERENCES learning_management_system (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE timetable_week_week');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable_week_week (timetable_week_id INT UNSIGNED NOT NULL, week_id INT UNSIGNED NOT NULL, INDEX IDX_B6951BE6490F7151 (timetable_week_id), INDEX IDX_B6951BE6C86F3B2F (week_id), PRIMARY KEY(timetable_week_id, week_id)) DEFAULT CHARACTER SET utf8mb3 COLLATE `utf8mb3_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE student_learning_management_system_information DROP FOREIGN KEY FK_AAFEBB22CB944F1A');
        $this->addSql('ALTER TABLE student_learning_management_system_information DROP FOREIGN KEY FK_AAFEBB224FDCCE2E');
        $this->addSql('DROP TABLE learning_management_system');
        $this->addSql('DROP TABLE student_learning_management_system_information');
    }
}
