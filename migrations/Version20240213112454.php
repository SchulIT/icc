<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240213112454 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parents_day (id INT UNSIGNED AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, date DATE NOT NULL, booking_allowed_from DATE NOT NULL, booking_allowed_until DATE NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents_day_appointment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parents_day_id INT UNSIGNED DEFAULT NULL, cancelled_by_id INT UNSIGNED DEFAULT NULL, is_blocked TINYINT(1) NOT NULL, start TIME NOT NULL, end TIME NOT NULL, is_cancelled TINYINT(1) NOT NULL, cancel_reason LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_9A8E78F52C60A5FA (parents_day_id), INDEX IDX_9A8E78F5187B2D12 (cancelled_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents_day_appointment_student (parents_day_appointment_id INT UNSIGNED NOT NULL, student_id INT UNSIGNED NOT NULL, INDEX IDX_146B803538A82948 (parents_day_appointment_id), INDEX IDX_146B8035CB944F1A (student_id), PRIMARY KEY(parents_day_appointment_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents_day_appointment_teacher (parents_day_appointment_id INT UNSIGNED NOT NULL, teacher_id INT UNSIGNED NOT NULL, INDEX IDX_13BE89D338A82948 (parents_day_appointment_id), INDEX IDX_13BE89D341807E1D (teacher_id), PRIMARY KEY(parents_day_appointment_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parents_day_parental_information (id INT UNSIGNED AUTO_INCREMENT NOT NULL, parents_day_id INT UNSIGNED DEFAULT NULL, teacher_id INT UNSIGNED DEFAULT NULL, student_id INT UNSIGNED DEFAULT NULL, is_appointment_cancelled TINYINT(1) NOT NULL, is_appointment_not_necessary TINYINT(1) NOT NULL, is_appointment_requested TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F7035D412C60A5FA (parents_day_id), INDEX IDX_F7035D4141807E1D (teacher_id), INDEX IDX_F7035D41CB944F1A (student_id), UNIQUE INDEX UNIQ_F7035D412C60A5FA41807E1DCB944F1A (parents_day_id, teacher_id, student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE parents_day_appointment ADD CONSTRAINT FK_9A8E78F52C60A5FA FOREIGN KEY (parents_day_id) REFERENCES parents_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_appointment ADD CONSTRAINT FK_9A8E78F5187B2D12 FOREIGN KEY (cancelled_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE parents_day_appointment_student ADD CONSTRAINT FK_146B803538A82948 FOREIGN KEY (parents_day_appointment_id) REFERENCES parents_day_appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_appointment_student ADD CONSTRAINT FK_146B8035CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_appointment_teacher ADD CONSTRAINT FK_13BE89D338A82948 FOREIGN KEY (parents_day_appointment_id) REFERENCES parents_day_appointment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_appointment_teacher ADD CONSTRAINT FK_13BE89D341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_parental_information ADD CONSTRAINT FK_F7035D412C60A5FA FOREIGN KEY (parents_day_id) REFERENCES parents_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_parental_information ADD CONSTRAINT FK_F7035D4141807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_parental_information ADD CONSTRAINT FK_F7035D41CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parents_day_appointment DROP FOREIGN KEY FK_9A8E78F52C60A5FA');
        $this->addSql('ALTER TABLE parents_day_appointment DROP FOREIGN KEY FK_9A8E78F5187B2D12');
        $this->addSql('ALTER TABLE parents_day_appointment_student DROP FOREIGN KEY FK_146B803538A82948');
        $this->addSql('ALTER TABLE parents_day_appointment_student DROP FOREIGN KEY FK_146B8035CB944F1A');
        $this->addSql('ALTER TABLE parents_day_appointment_teacher DROP FOREIGN KEY FK_13BE89D338A82948');
        $this->addSql('ALTER TABLE parents_day_appointment_teacher DROP FOREIGN KEY FK_13BE89D341807E1D');
        $this->addSql('ALTER TABLE parents_day_parental_information DROP FOREIGN KEY FK_F7035D412C60A5FA');
        $this->addSql('ALTER TABLE parents_day_parental_information DROP FOREIGN KEY FK_F7035D4141807E1D');
        $this->addSql('ALTER TABLE parents_day_parental_information DROP FOREIGN KEY FK_F7035D41CB944F1A');
        $this->addSql('DROP TABLE parents_day');
        $this->addSql('DROP TABLE parents_day_appointment');
        $this->addSql('DROP TABLE parents_day_appointment_student');
        $this->addSql('DROP TABLE parents_day_appointment_teacher');
        $this->addSql('DROP TABLE parents_day_parental_information');
    }
}
