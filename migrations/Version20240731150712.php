<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240731150712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance RENAME INDEX idx_60ea4440ba364942 TO IDX_6DE30D91BA364942');
        $this->addSql('ALTER TABLE attendance RENAME INDEX idx_60ea4440cb944f1a TO IDX_6DE30D91CB944F1A');
        $this->addSql('ALTER TABLE attendance_attendance_flag DROP FOREIGN KEY FK_8560474852E3D9B6');
        $this->addSql('ALTER TABLE attendance_attendance_flag DROP FOREIGN KEY FK_85604748C211C9F7');
        $this->addSql('DROP INDEX IDX_85604748C211C9F7 ON attendance_attendance_flag');
        $this->addSql('DROP INDEX IDX_8560474852E3D9B6 ON attendance_attendance_flag');
        $this->addSql('DROP INDEX `primary` ON attendance_attendance_flag');
        $this->addSql('ALTER TABLE attendance_attendance_flag RENAME COLUMN lesson_attendance_id TO attendance_id');
        $this->addSql('ALTER TABLE attendance_attendance_flag RENAME COLUMN lesson_attendance_flag_id TO attendance_flag_id');
        //$this->addSql('ALTER TABLE attendance_attendance_flag ADD attendance_id INT UNSIGNED NOT NULL, ADD attendance_flag_id INT UNSIGNED NOT NULL, DROP lesson_attendance_id, DROP lesson_attendance_flag_id');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD CONSTRAINT FK_465D1EC4163DDA15 FOREIGN KEY (attendance_id) REFERENCES attendance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD CONSTRAINT FK_465D1EC496580AE7 FOREIGN KEY (attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_465D1EC4163DDA15 ON attendance_attendance_flag (attendance_id)');
        $this->addSql('CREATE INDEX IDX_465D1EC496580AE7 ON attendance_attendance_flag (attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD PRIMARY KEY (attendance_id, attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX type_50a0fde927bab3ff7f5e2e03693947fc_idx TO type_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX object_id_50a0fde927bab3ff7f5e2e03693947fc_idx TO object_id_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX discriminator_50a0fde927bab3ff7f5e2e03693947fc_idx TO discriminator_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX transaction_hash_50a0fde927bab3ff7f5e2e03693947fc_idx TO transaction_hash_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX blame_id_50a0fde927bab3ff7f5e2e03693947fc_idx TO blame_id_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX created_at_50a0fde927bab3ff7f5e2e03693947fc_idx TO created_at_408d14eca91b36e8e100d78eba0ab918_idx');
        $this->addSql('ALTER TABLE attendance_flag_subject DROP FOREIGN KEY FK_CA3908EB52E3D9B6');
        $this->addSql('DROP INDEX IDX_CA3908EB52E3D9B6 ON attendance_flag_subject');
        $this->addSql('DROP INDEX `primary` ON attendance_flag_subject');
        $this->addSql('ALTER TABLE attendance_flag_subject CHANGE lesson_attendance_flag_id attendance_flag_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE attendance_flag_subject ADD CONSTRAINT FK_9D36FB5C96580AE7 FOREIGN KEY (attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_9D36FB5C96580AE7 ON attendance_flag_subject (attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_flag_subject ADD PRIMARY KEY (attendance_flag_id, subject_id)');
        $this->addSql('ALTER TABLE attendance_flag_subject RENAME INDEX idx_ca3908eb23edc87 TO IDX_9D36FB5C23EDC87');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX type_fe6c4b71dba248a26ce76e42f779e825_idx TO type_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX object_id_fe6c4b71dba248a26ce76e42f779e825_idx TO object_id_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX discriminator_fe6c4b71dba248a26ce76e42f779e825_idx TO discriminator_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX transaction_hash_fe6c4b71dba248a26ce76e42f779e825_idx TO transaction_hash_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX blame_id_fe6c4b71dba248a26ce76e42f779e825_idx TO blame_id_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX created_at_fe6c4b71dba248a26ce76e42f779e825_idx TO created_at_c664b74ddd6bec51571ca3d2b7f74f27_idx');
        $this->addSql('ALTER TABLE student_absence_type_flags DROP FOREIGN KEY FK_182635F252E3D9B6');
        $this->addSql('DROP INDEX IDX_182635F252E3D9B6 ON student_absence_type_flags');
        $this->addSql('DROP INDEX `primary` ON student_absence_type_flags');
        $this->addSql('ALTER TABLE student_absence_type_flags CHANGE lesson_attendance_flag_id attendance_flag_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD CONSTRAINT FK_182635F296580AE7 FOREIGN KEY (attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_182635F296580AE7 ON student_absence_type_flags (attendance_flag_id)');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD PRIMARY KEY (student_absence_type_id, attendance_flag_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attendance RENAME INDEX idx_6de30d91ba364942 TO IDX_60EA4440BA364942');
        $this->addSql('ALTER TABLE attendance RENAME INDEX idx_6de30d91cb944f1a TO IDX_60EA4440CB944F1A');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX blame_id_c664b74ddd6bec51571ca3d2b7f74f27_idx TO blame_id_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX object_id_c664b74ddd6bec51571ca3d2b7f74f27_idx TO object_id_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX created_at_c664b74ddd6bec51571ca3d2b7f74f27_idx TO created_at_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX discriminator_c664b74ddd6bec51571ca3d2b7f74f27_idx TO discriminator_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX transaction_hash_c664b74ddd6bec51571ca3d2b7f74f27_idx TO transaction_hash_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_audit RENAME INDEX type_c664b74ddd6bec51571ca3d2b7f74f27_idx TO type_fe6c4b71dba248a26ce76e42f779e825_idx');
        $this->addSql('ALTER TABLE attendance_flag_subject DROP FOREIGN KEY FK_9D36FB5C96580AE7');
        $this->addSql('DROP INDEX IDX_9D36FB5C96580AE7 ON attendance_flag_subject');
        $this->addSql('DROP INDEX `PRIMARY` ON attendance_flag_subject');
        $this->addSql('ALTER TABLE attendance_flag_subject CHANGE attendance_flag_id lesson_attendance_flag_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE attendance_flag_subject ADD CONSTRAINT FK_CA3908EB52E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_CA3908EB52E3D9B6 ON attendance_flag_subject (lesson_attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_flag_subject ADD PRIMARY KEY (lesson_attendance_flag_id, subject_id)');
        $this->addSql('ALTER TABLE attendance_flag_subject RENAME INDEX idx_9d36fb5c23edc87 TO IDX_CA3908EB23EDC87');
        $this->addSql('ALTER TABLE student_absence_type_flags DROP FOREIGN KEY FK_182635F296580AE7');
        $this->addSql('DROP INDEX IDX_182635F296580AE7 ON student_absence_type_flags');
        $this->addSql('DROP INDEX `PRIMARY` ON student_absence_type_flags');
        $this->addSql('ALTER TABLE student_absence_type_flags CHANGE attendance_flag_id lesson_attendance_flag_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD CONSTRAINT FK_182635F252E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_182635F252E3D9B6 ON student_absence_type_flags (lesson_attendance_flag_id)');
        $this->addSql('ALTER TABLE student_absence_type_flags ADD PRIMARY KEY (student_absence_type_id, lesson_attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX object_id_408d14eca91b36e8e100d78eba0ab918_idx TO object_id_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX created_at_408d14eca91b36e8e100d78eba0ab918_idx TO created_at_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX discriminator_408d14eca91b36e8e100d78eba0ab918_idx TO discriminator_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX transaction_hash_408d14eca91b36e8e100d78eba0ab918_idx TO transaction_hash_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX type_408d14eca91b36e8e100d78eba0ab918_idx TO type_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_audit RENAME INDEX blame_id_408d14eca91b36e8e100d78eba0ab918_idx TO blame_id_50a0fde927bab3ff7f5e2e03693947fc_idx');
        $this->addSql('ALTER TABLE attendance_attendance_flag DROP FOREIGN KEY FK_465D1EC4163DDA15');
        $this->addSql('ALTER TABLE attendance_attendance_flag DROP FOREIGN KEY FK_465D1EC496580AE7');
        $this->addSql('DROP INDEX IDX_465D1EC4163DDA15 ON attendance_attendance_flag');
        $this->addSql('DROP INDEX IDX_465D1EC496580AE7 ON attendance_attendance_flag');
        $this->addSql('DROP INDEX `PRIMARY` ON attendance_attendance_flag');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD lesson_attendance_id INT UNSIGNED NOT NULL, ADD lesson_attendance_flag_id INT UNSIGNED NOT NULL, DROP attendance_id, DROP attendance_flag_id');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD CONSTRAINT FK_8560474852E3D9B6 FOREIGN KEY (lesson_attendance_flag_id) REFERENCES attendance_flag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD CONSTRAINT FK_85604748C211C9F7 FOREIGN KEY (lesson_attendance_id) REFERENCES attendance (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_85604748C211C9F7 ON attendance_attendance_flag (lesson_attendance_id)');
        $this->addSql('CREATE INDEX IDX_8560474852E3D9B6 ON attendance_attendance_flag (lesson_attendance_flag_id)');
        $this->addSql('ALTER TABLE attendance_attendance_flag ADD PRIMARY KEY (lesson_attendance_id, lesson_attendance_flag_id)');
    }
}
