<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210704194230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function preUp(Schema $schema): void {
        $tables = [
            'exam',
            'exam_audit',
            'exam_supervision_audit',
            'exam_tuitions',
            'grade_teacher',
            'study_group',
            'study_group_audit',
            'substitution',
            'substitution_audit',
            'timetable_period',
            'timetable_period_audit',
            'timetable_lesson_audit',
            'timetable_supervision_audit',
            'tuition',
            'tuition_audit'
        ];

        foreach($tables as $table) {
            $sql = sprintf('DELETE FROM %s', $table);
            $this->connection->executeQuery($sql);

            $sql = sprintf('OPTIMIZE TABLE %s', $table);
            $this->connection->executeQuery($sql);
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grade_membership (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, grade_id INT UNSIGNED DEFAULT NULL, section_id INT UNSIGNED DEFAULT NULL, INDEX IDX_67830304CB944F1A (student_id), INDEX IDX_67830304FE19A1A8 (grade_id), INDEX IDX_67830304D823E37A (section_id), UNIQUE INDEX UNIQ_67830304D823E37AFE19A1A8CB944F1A (section_id, grade_id, student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_membership_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_b4fc90c9de2e8d4c43658222f1a9f663_idx (type), INDEX object_id_b4fc90c9de2e8d4c43658222f1a9f663_idx (object_id), INDEX discriminator_b4fc90c9de2e8d4c43658222f1a9f663_idx (discriminator), INDEX transaction_hash_b4fc90c9de2e8d4c43658222f1a9f663_idx (transaction_hash), INDEX blame_id_b4fc90c9de2e8d4c43658222f1a9f663_idx (blame_id), INDEX created_at_b4fc90c9de2e8d4c43658222f1a9f663_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_teacher_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_515250cc402e956ae1cc785b6a065b8b_idx (type), INDEX object_id_515250cc402e956ae1cc785b6a065b8b_idx (object_id), INDEX discriminator_515250cc402e956ae1cc785b6a065b8b_idx (discriminator), INDEX transaction_hash_515250cc402e956ae1cc785b6a065b8b_idx (transaction_hash), INDEX blame_id_515250cc402e956ae1cc785b6a065b8b_idx (blame_id), INDEX created_at_515250cc402e956ae1cc785b6a065b8b_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE section (id INT UNSIGNED AUTO_INCREMENT NOT NULL, number INT NOT NULL, year INT NOT NULL, display_name VARCHAR(255) NOT NULL, start DATE NOT NULL, end DATE NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_2D737AEF96901F54BB827337 (number, year), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_sections (student_id INT UNSIGNED NOT NULL, section_id INT UNSIGNED NOT NULL, INDEX IDX_981D0F03CB944F1A (student_id), INDEX IDX_981D0F03D823E37A (section_id), PRIMARY KEY(student_id, section_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE study_group_membership_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_8e7ce6d0f2d42842ce4685890f729292_idx (type), INDEX object_id_8e7ce6d0f2d42842ce4685890f729292_idx (object_id), INDEX discriminator_8e7ce6d0f2d42842ce4685890f729292_idx (discriminator), INDEX transaction_hash_8e7ce6d0f2d42842ce4685890f729292_idx (transaction_hash), INDEX blame_id_8e7ce6d0f2d42842ce4685890f729292_idx (blame_id), INDEX created_at_8e7ce6d0f2d42842ce4685890f729292_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teacher_sections (teacher_id INT UNSIGNED NOT NULL, section_id INT UNSIGNED NOT NULL, INDEX IDX_2A80F01341807E1D (teacher_id), INDEX IDX_2A80F013D823E37A (section_id), PRIMARY KEY(teacher_id, section_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grade_membership ADD CONSTRAINT FK_67830304CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grade_membership ADD CONSTRAINT FK_67830304FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE grade_membership ADD CONSTRAINT FK_67830304D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_sections ADD CONSTRAINT FK_981D0F03CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_sections ADD CONSTRAINT FK_981D0F03D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_sections ADD CONSTRAINT FK_2A80F01341807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_sections ADD CONSTRAINT FK_2A80F013D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_595AAE349F75D7B0 ON grade');
        $this->addSql('ALTER TABLE grade_teacher ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, ADD section_id INT UNSIGNED DEFAULT NULL, CHANGE teacher_id teacher_id INT UNSIGNED DEFAULT NULL, CHANGE grade_id grade_id INT UNSIGNED DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE grade_teacher ADD CONSTRAINT FK_4ABB3437D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_4ABB3437D823E37A ON grade_teacher (section_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4ABB3437D823E37AFE19A1A841807E1D ON grade_teacher (section_id, grade_id, teacher_id)');
        $this->addSql('ALTER TABLE student DROP FOREIGN KEY FK_B723AF33FE19A1A8');
        $this->addSql('DROP INDEX IDX_B723AF33FE19A1A8 ON student');
        $this->addSql('ALTER TABLE student DROP grade_id, CHANGE unique_identifier unique_identifier VARCHAR(255) NOT NULL');
        $this->addSql('DROP INDEX UNIQ_32BA14259F75D7B0 ON study_group');
        $this->addSql('ALTER TABLE study_group ADD section_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE study_group ADD CONSTRAINT FK_32BA1425D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_32BA1425D823E37A ON study_group (section_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32BA1425D823E37A9F75D7B0 ON study_group (section_id, external_id)');
        $this->addSql('ALTER TABLE study_group_membership ADD id INT UNSIGNED AUTO_INCREMENT NOT NULL, CHANGE study_group_id study_group_id INT UNSIGNED DEFAULT NULL, CHANGE student_id student_id INT UNSIGNED DEFAULT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE timetable_period ADD section_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_period ADD CONSTRAINT FK_1BE4AD1AD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_1BE4AD1AD823E37A ON timetable_period (section_id)');
        $this->addSql('DROP INDEX UNIQ_A1B25E5B9F75D7B0 ON tuition');
        $this->addSql('ALTER TABLE tuition ADD section_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition ADD CONSTRAINT FK_A1B25E5BD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_A1B25E5BD823E37A ON tuition (section_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_membership DROP FOREIGN KEY FK_67830304D823E37A');
        $this->addSql('ALTER TABLE grade_teacher DROP FOREIGN KEY FK_4ABB3437D823E37A');
        $this->addSql('ALTER TABLE student_sections DROP FOREIGN KEY FK_981D0F03D823E37A');
        $this->addSql('ALTER TABLE study_group DROP FOREIGN KEY FK_32BA1425D823E37A');
        $this->addSql('ALTER TABLE teacher_sections DROP FOREIGN KEY FK_2A80F013D823E37A');
        $this->addSql('ALTER TABLE timetable_period DROP FOREIGN KEY FK_1BE4AD1AD823E37A');
        $this->addSql('ALTER TABLE tuition DROP FOREIGN KEY FK_A1B25E5BD823E37A');
        $this->addSql('DROP TABLE grade_membership');
        $this->addSql('DROP TABLE grade_membership_audit');
        $this->addSql('DROP TABLE grade_teacher_audit');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP TABLE student_sections');
        $this->addSql('DROP TABLE study_group_membership_audit');
        $this->addSql('DROP TABLE teacher_sections');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_595AAE349F75D7B0 ON grade (external_id)');
        $this->addSql('ALTER TABLE grade_teacher MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('DROP INDEX IDX_4ABB3437D823E37A ON grade_teacher');
        $this->addSql('DROP INDEX UNIQ_4ABB3437D823E37AFE19A1A841807E1D ON grade_teacher');
        $this->addSql('ALTER TABLE grade_teacher DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE grade_teacher DROP id, DROP section_id, CHANGE teacher_id teacher_id INT UNSIGNED NOT NULL, CHANGE grade_id grade_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE grade_teacher ADD PRIMARY KEY (teacher_id, grade_id)');
        $this->addSql('ALTER TABLE student ADD grade_id INT UNSIGNED DEFAULT NULL, CHANGE unique_identifier unique_identifier VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE student ADD CONSTRAINT FK_B723AF33FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_B723AF33FE19A1A8 ON student (grade_id)');
        $this->addSql('DROP INDEX IDX_32BA1425D823E37A ON study_group');
        $this->addSql('DROP INDEX UNIQ_32BA1425D823E37A9F75D7B0 ON study_group');
        $this->addSql('ALTER TABLE study_group DROP section_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32BA14259F75D7B0 ON study_group (external_id)');
        $this->addSql('ALTER TABLE study_group_membership MODIFY id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE study_group_membership DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE study_group_membership DROP id, CHANGE study_group_id study_group_id INT UNSIGNED NOT NULL, CHANGE student_id student_id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE study_group_membership ADD PRIMARY KEY (study_group_id, student_id)');
        $this->addSql('DROP INDEX IDX_1BE4AD1AD823E37A ON timetable_period');
        $this->addSql('ALTER TABLE timetable_period DROP section_id');
        $this->addSql('DROP INDEX IDX_A1B25E5BD823E37A ON tuition');
        $this->addSql('ALTER TABLE tuition DROP section_id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A1B25E5B9F75D7B0 ON tuition (external_id)');
    }
}
