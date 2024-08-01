<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240731145625 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE lesson_attendance_flag RENAME TO attendance_flag');
        $this->addSql('ALTER TABLE lesson_attendance RENAME TO attendance');
        $this->addSql('ALTER TABLE lesson_attendance_lesson_attendance_flag RENAME TO attendance_attendance_flag');
        $this->addSql('ALTER TABLE lesson_attendance_audit RENAME TO attendance_audit');
        $this->addSql('ALTER TABLE lesson_attendance_flag_audit RENAME TO attendance_flag_audit');
        $this->addSql('ALTER TABLE lesson_attendance_flag_subject RENAME TO attendance_flag_subject');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE attendance_flag RENAME TO lesson_attendance_flag');
        $this->addSql('ALTER TABLE attendance RENAME TO lesson_attendance');
        $this->addSql('ALTER TABLE attendance_attendance_flag RENAME TO lesson_attendance_lesson_attendance_flag');
        $this->addSql('ALTER TABLE attendance_audit RENAME TO lesson_attendance_audit');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME TO lesson_attendance_flag_audit');
        $this->addSql('ALTER TABLE attendance_flag_subject RENAME TO lesson_attendance_flag_subject');
    }
}
