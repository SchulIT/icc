<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231112162359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_integrity_check_run (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, last_run DATETIME DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_94AC4303CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_integrity_check_violation (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, lesson_id INT UNSIGNED DEFAULT NULL, reference_id VARCHAR(255) NOT NULL, date DATE NOT NULL, lesson_number INT NOT NULL, message LONGTEXT NOT NULL, is_suppressed TINYINT(1) NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_357D54561645DEA9 (reference_id), INDEX IDX_357D5456CB944F1A (student_id), INDEX IDX_357D5456CDF80196 (lesson_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_integrity_check_run ADD CONSTRAINT FK_94AC4303CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_integrity_check_violation ADD CONSTRAINT FK_357D5456CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_integrity_check_violation ADD CONSTRAINT FK_357D5456CDF80196 FOREIGN KEY (lesson_id) REFERENCES timetable_lesson (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_integrity_check_run DROP FOREIGN KEY FK_94AC4303CB944F1A');
        $this->addSql('ALTER TABLE book_integrity_check_violation DROP FOREIGN KEY FK_357D5456CB944F1A');
        $this->addSql('ALTER TABLE book_integrity_check_violation DROP FOREIGN KEY FK_357D5456CDF80196');
        $this->addSql('DROP TABLE book_integrity_check_run');
        $this->addSql('DROP TABLE book_integrity_check_violation');
    }
}
