<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210730134202 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_comment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, teacher_id INT UNSIGNED DEFAULT NULL, text LONGTEXT NOT NULL, date DATE NOT NULL, created_at DATETIME NOT NULL, created_by VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_7547AFA41807E1D (teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_comment_student (book_comment_id INT UNSIGNED NOT NULL, student_id INT UNSIGNED NOT NULL, INDEX IDX_C0E75F5867C437A0 (book_comment_id), INDEX IDX_C0E75F58CB944F1A (student_id), PRIMARY KEY(book_comment_id, student_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_comment_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_5b949501a5c831b074e6d1ce834ccd1b_idx (type), INDEX object_id_5b949501a5c831b074e6d1ce834ccd1b_idx (object_id), INDEX discriminator_5b949501a5c831b074e6d1ce834ccd1b_idx (discriminator), INDEX transaction_hash_5b949501a5c831b074e6d1ce834ccd1b_idx (transaction_hash), INDEX blame_id_5b949501a5c831b074e6d1ce834ccd1b_idx (blame_id), INDEX created_at_5b949501a5c831b074e6d1ce834ccd1b_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE excuse_note (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, excused_by_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, comment LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_FFC74522CB944F1A (student_id), INDEX IDX_FFC7452258793222 (excused_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE excuse_note_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_9466a3631413cf34a5287c53cbc7d7dc_idx (type), INDEX object_id_9466a3631413cf34a5287c53cbc7d7dc_idx (object_id), INDEX discriminator_9466a3631413cf34a5287c53cbc7d7dc_idx (discriminator), INDEX transaction_hash_9466a3631413cf34a5287c53cbc7d7dc_idx (transaction_hash), INDEX blame_id_9466a3631413cf34a5287c53cbc7d7dc_idx (blame_id), INDEX created_at_9466a3631413cf34a5287c53cbc7d7dc_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tuition_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_F87474F37FFA6BA (tuition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_1f3b98518731e004601dd5a93bac5ed4_idx (type), INDEX object_id_1f3b98518731e004601dd5a93bac5ed4_idx (object_id), INDEX discriminator_1f3b98518731e004601dd5a93bac5ed4_idx (discriminator), INDEX transaction_hash_1f3b98518731e004601dd5a93bac5ed4_idx (transaction_hash), INDEX blame_id_1f3b98518731e004601dd5a93bac5ed4_idx (blame_id), INDEX created_at_1f3b98518731e004601dd5a93bac5ed4_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attendance (id INT UNSIGNED AUTO_INCREMENT NOT NULL, entry_id INT UNSIGNED DEFAULT NULL, student_id INT UNSIGNED DEFAULT NULL, type INT NOT NULL, late_minutes INT NOT NULL, absent_lessons INT NOT NULL, comment LONGTEXT DEFAULT NULL, excuse_status INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_60EA4440BA364942 (entry_id), INDEX IDX_60EA4440CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_attendance_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_50a0fde927bab3ff7f5e2e03693947fc_idx (type), INDEX object_id_50a0fde927bab3ff7f5e2e03693947fc_idx (object_id), INDEX discriminator_50a0fde927bab3ff7f5e2e03693947fc_idx (discriminator), INDEX transaction_hash_50a0fde927bab3ff7f5e2e03693947fc_idx (transaction_hash), INDEX blame_id_50a0fde927bab3ff7f5e2e03693947fc_idx (blame_id), INDEX created_at_50a0fde927bab3ff7f5e2e03693947fc_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_entry (id INT UNSIGNED AUTO_INCREMENT NOT NULL, lesson_id INT UNSIGNED DEFAULT NULL, tuition_id INT UNSIGNED DEFAULT NULL, subject_id INT UNSIGNED DEFAULT NULL, teacher_id INT UNSIGNED DEFAULT NULL, replacement_teacher_id INT UNSIGNED DEFAULT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, replacement_subject VARCHAR(255) DEFAULT NULL, topic VARCHAR(255) DEFAULT NULL, comment LONGTEXT DEFAULT NULL, is_cancelled TINYINT(1) NOT NULL, cancel_reason VARCHAR(255) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_61C749F7CDF80196 (lesson_id), INDEX IDX_61C749F77FFA6BA (tuition_id), INDEX IDX_61C749F723EDC87 (subject_id), INDEX IDX_61C749F741807E1D (teacher_id), INDEX IDX_61C749F780771AA7 (replacement_teacher_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE lesson_entry_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (type), INDEX object_id_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (object_id), INDEX discriminator_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (discriminator), INDEX transaction_hash_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (transaction_hash), INDEX blame_id_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (blame_id), INDEX created_at_67baa065c6cae7dd7c5a59ac5a14a0b7_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_comment ADD CONSTRAINT FK_7547AFA41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE book_comment_student ADD CONSTRAINT FK_C0E75F5867C437A0 FOREIGN KEY (book_comment_id) REFERENCES book_comment (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE book_comment_student ADD CONSTRAINT FK_C0E75F58CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE excuse_note ADD CONSTRAINT FK_FFC74522CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE excuse_note ADD CONSTRAINT FK_FFC7452258793222 FOREIGN KEY (excused_by_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F37FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id)');
        $this->addSql('ALTER TABLE lesson_attendance ADD CONSTRAINT FK_60EA4440BA364942 FOREIGN KEY (entry_id) REFERENCES lesson_entry (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_attendance ADD CONSTRAINT FK_60EA4440CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F7CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id)');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F77FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id)');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F723EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F741807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F780771AA7 FOREIGN KEY (replacement_teacher_id) REFERENCES teacher (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_comment_student DROP FOREIGN KEY FK_C0E75F5867C437A0');
        $this->addSql('ALTER TABLE lesson_entry DROP FOREIGN KEY FK_61C749F7CDF80196');
        $this->addSql('ALTER TABLE lesson_attendance DROP FOREIGN KEY FK_60EA4440BA364942');
        $this->addSql('DROP TABLE book_comment');
        $this->addSql('DROP TABLE book_comment_student');
        $this->addSql('DROP TABLE book_comment_audit');
        $this->addSql('DROP TABLE excuse_note');
        $this->addSql('DROP TABLE excuse_note_audit');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_audit');
        $this->addSql('DROP TABLE lesson_attendance');
        $this->addSql('DROP TABLE lesson_attendance_audit');
        $this->addSql('DROP TABLE lesson_entry');
        $this->addSql('DROP TABLE lesson_entry_audit');
    }
}
