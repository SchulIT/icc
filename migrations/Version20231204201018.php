<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231204201018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lesson_attendance_lesson_attendance_flag (lesson_attendance_id INT UNSIGNED NOT NULL, lesson_attendance_flag_id INT UNSIGNED NOT NULL, INDEX IDX_85604748C211C9F7 (lesson_attendance_id), INDEX IDX_8560474852E3D9B6 (lesson_attendance_flag_id), PRIMARY KEY(lesson_attendance_id, lesson_attendance_flag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attendance_flag (id INT UNSIGNED AUTO_INCREMENT NOT NULL, icon VARCHAR(255) NOT NULL, stack_icon VARCHAR(255) DEFAULT NULL, description VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attendance_flag_subject (lesson_attendance_flag_id INT UNSIGNED NOT NULL, subject_id INT UNSIGNED NOT NULL, INDEX IDX_CA3908EB52E3D9B6 (lesson_attendance_flag_id), INDEX IDX_CA3908EB23EDC87 (subject_id), PRIMARY KEY(lesson_attendance_flag_id, subject_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attendance_flag_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_fe6c4b71dba248a26ce76e42f779e825_idx (type), INDEX object_id_fe6c4b71dba248a26ce76e42f779e825_idx (object_id), INDEX discriminator_fe6c4b71dba248a26ce76e42f779e825_idx (discriminator), INDEX transaction_hash_fe6c4b71dba248a26ce76e42f779e825_idx (transaction_hash), INDEX blame_id_fe6c4b71dba248a26ce76e42f779e825_idx (blame_id), INDEX created_at_fe6c4b71dba248a26ce76e42f779e825_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_type_flags (student_absence_type_id INT UNSIGNED NOT NULL, lesson_attendance_flag_id INT UNSIGNED NOT NULL, INDEX IDX_182635F2506E213D (student_absence_type_id), INDEX IDX_182635F252E3D9B6 (lesson_attendance_flag_id), PRIMARY KEY(student_absence_type_id, lesson_attendance_flag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE lesson_attendance_lesson_attendance_flag ADD CONSTRAINT FK_85604748C211C9F7 FOREIGN KEY (lesson_attendance_id) REFERENCES lesson_attendance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_attendance_lesson_attendance_flag ADD CONSTRAINT FK_8560474852E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES lesson_attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_attendance_flag_subject ADD CONSTRAINT FK_CA3908EB52E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES lesson_attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_attendance_flag_subject ADD CONSTRAINT FK_CA3908EB23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD CONSTRAINT FK_182635F2506E213D FOREIGN KEY (student_absence_type_id) REFERENCES student_absence_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD CONSTRAINT FK_182635F252E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES lesson_attendance_flag (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_attendance_lesson_attendance_flag DROP FOREIGN KEY FK_85604748C211C9F7');
        $this->addSql('ALTER TABLE lesson_attendance_lesson_attendance_flag DROP FOREIGN KEY FK_8560474852E3D9B6');
        $this->addSql('ALTER TABLE lesson_attendance_flag_subject DROP FOREIGN KEY FK_CA3908EB52E3D9B6');
        $this->addSql('ALTER TABLE lesson_attendance_flag_subject DROP FOREIGN KEY FK_CA3908EB23EDC87');
        $this->addSql('ALTER TABLE student_absence_type_flags DROP FOREIGN KEY FK_182635F2506E213D');
        $this->addSql('ALTER TABLE student_absence_type_flags DROP FOREIGN KEY FK_182635F252E3D9B6');
        $this->addSql('DROP TABLE lesson_attendance_lesson_attendance_flag');
        $this->addSql('DROP TABLE lesson_attendance_flag');
        $this->addSql('DROP TABLE lesson_attendance_flag_subject');
        $this->addSql('DROP TABLE lesson_attendance_flag_audit');
        $this->addSql('DROP TABLE student_absence_type_flags');
    }
}
