<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715133718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lesson_entry DROP FOREIGN KEY FK_61C749F7CDF80196');
        $this->addSql('ALTER TABLE timetable_lesson DROP FOREIGN KEY FK_2628C727EC8B7ADE');
        $this->addSql('ALTER TABLE timetable_period_visibilities DROP FOREIGN KEY FK_1954F2D6CB3E0748');
        $this->addSql('ALTER TABLE timetable_supervision DROP FOREIGN KEY FK_396D5F68EC8B7ADE');
        $this->addSql('DROP TABLE lesson');
        $this->addSql('DROP TABLE lesson_audit');
        $this->addSql('DROP TABLE timetable_period');
        $this->addSql('DROP TABLE timetable_period_audit');
        $this->addSql('DROP TABLE timetable_period_visibilities');
        $this->addSql('DROP TABLE timetable_supervision_week');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F7CDF80196 FOREIGN KEY (lesson_id) REFERENCES timetable_lesson (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_lesson DROP FOREIGN KEY FK_2628C727C86F3B2F');
        $this->addSql('DROP INDEX IDX_2628C727C86F3B2F ON timetable_lesson');
        $this->addSql('DROP INDEX UNIQ_2628C7279F75D7B0EC8B7ADE ON timetable_lesson');
        $this->addSql('DROP INDEX IDX_2628C727EC8B7ADE ON timetable_lesson');
        $this->addSql('ALTER TABLE timetable_lesson ADD date DATE NOT NULL, ADD lesson_start INT NOT NULL, ADD lesson_end INT NOT NULL, DROP period_id, DROP week_id, DROP day, DROP lesson, DROP is_double_lesson');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2628C7279F75D7B0 ON timetable_lesson (external_id)');
        $this->addSql('DROP INDEX IDX_396D5F68EC8B7ADE ON timetable_supervision');
        $this->addSql('ALTER TABLE timetable_supervision ADD date DATETIME NOT NULL, DROP period_id, DROP day');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE lesson (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tuition_id INT UNSIGNED DEFAULT NULL, date DATE NOT NULL, lesson_start INT NOT NULL, lesson_end INT NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_F87474F37FFA6BA (tuition_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE lesson_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX transaction_hash_1f3b98518731e004601dd5a93bac5ed4_idx (transaction_hash), INDEX type_1f3b98518731e004601dd5a93bac5ed4_idx (type), INDEX blame_id_1f3b98518731e004601dd5a93bac5ed4_idx (blame_id), INDEX object_id_1f3b98518731e004601dd5a93bac5ed4_idx (object_id), INDEX created_at_1f3b98518731e004601dd5a93bac5ed4_idx (created_at), INDEX discriminator_1f3b98518731e004601dd5a93bac5ed4_idx (discriminator), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE timetable_period (id INT UNSIGNED AUTO_INCREMENT NOT NULL, section_id INT UNSIGNED DEFAULT NULL, external_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, start DATETIME NOT NULL, end DATETIME NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', UNIQUE INDEX UNIQ_1BE4AD1A9F75D7B0 (external_id), INDEX IDX_1BE4AD1AD823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE timetable_period_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX transaction_hash_6180335df9e81b227d1786f59e915bed_idx (transaction_hash), INDEX type_6180335df9e81b227d1786f59e915bed_idx (type), INDEX blame_id_6180335df9e81b227d1786f59e915bed_idx (blame_id), INDEX object_id_6180335df9e81b227d1786f59e915bed_idx (object_id), INDEX created_at_6180335df9e81b227d1786f59e915bed_idx (created_at), INDEX discriminator_6180335df9e81b227d1786f59e915bed_idx (discriminator), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE timetable_period_visibilities (timetable_period_id INT UNSIGNED NOT NULL, user_type_entity_id INT UNSIGNED NOT NULL, INDEX IDX_1954F2D6CB3E0748 (timetable_period_id), INDEX IDX_1954F2D65E66E314 (user_type_entity_id), PRIMARY KEY(timetable_period_id, user_type_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE timetable_supervision_week (timetable_supervision_id INT UNSIGNED NOT NULL, week_id INT UNSIGNED NOT NULL, INDEX IDX_72D8EF6A2EA503EF (timetable_supervision_id), INDEX IDX_72D8EF6AC86F3B2F (week_id), PRIMARY KEY(timetable_supervision_id, week_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE lesson ADD CONSTRAINT FK_F87474F37FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id)');
        $this->addSql('ALTER TABLE timetable_period ADD CONSTRAINT FK_1BE4AD1AD823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_period_visibilities ADD CONSTRAINT FK_1954F2D65E66E314 FOREIGN KEY (user_type_entity_id) REFERENCES user_type_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_period_visibilities ADD CONSTRAINT FK_1954F2D6CB3E0748 FOREIGN KEY (timetable_period_id) REFERENCES timetable_period (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_supervision_week ADD CONSTRAINT FK_72D8EF6A2EA503EF FOREIGN KEY (timetable_supervision_id) REFERENCES timetable_supervision (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_supervision_week ADD CONSTRAINT FK_72D8EF6AC86F3B2F FOREIGN KEY (week_id) REFERENCES week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE lesson_entry DROP FOREIGN KEY FK_61C749F7CDF80196');
        $this->addSql('ALTER TABLE lesson_entry ADD CONSTRAINT FK_61C749F7CDF80196 FOREIGN KEY (lesson_id) REFERENCES lesson (id) ON DELETE CASCADE');
        $this->addSql('DROP INDEX UNIQ_2628C7279F75D7B0 ON timetable_lesson');
        $this->addSql('ALTER TABLE timetable_lesson ADD period_id INT UNSIGNED DEFAULT NULL, ADD week_id INT UNSIGNED DEFAULT NULL, ADD day INT NOT NULL, ADD lesson INT NOT NULL, ADD is_double_lesson TINYINT(1) NOT NULL, DROP date, DROP lesson_start, DROP lesson_end');
        $this->addSql('ALTER TABLE timetable_lesson ADD CONSTRAINT FK_2628C727C86F3B2F FOREIGN KEY (week_id) REFERENCES timetable_week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_lesson ADD CONSTRAINT FK_2628C727EC8B7ADE FOREIGN KEY (period_id) REFERENCES timetable_period (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_2628C727C86F3B2F ON timetable_lesson (week_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2628C7279F75D7B0EC8B7ADE ON timetable_lesson (external_id, period_id)');
        $this->addSql('CREATE INDEX IDX_2628C727EC8B7ADE ON timetable_lesson (period_id)');
        $this->addSql('ALTER TABLE timetable_supervision ADD period_id INT UNSIGNED DEFAULT NULL, ADD day INT NOT NULL, DROP date');
        $this->addSql('ALTER TABLE timetable_supervision ADD CONSTRAINT FK_396D5F68EC8B7ADE FOREIGN KEY (period_id) REFERENCES timetable_period (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_396D5F68EC8B7ADE ON timetable_supervision (period_id)');
    }
}
