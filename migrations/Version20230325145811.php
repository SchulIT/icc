<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230325145811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tuition_grade (id INT UNSIGNED AUTO_INCREMENT NOT NULL, tuition_id INT UNSIGNED DEFAULT NULL, category_id INT UNSIGNED DEFAULT NULL, student_id INT UNSIGNED DEFAULT NULL, encrypted_grade LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_5DC97A707FFA6BA (tuition_id), INDEX IDX_5DC97A7012469DE2 (category_id), INDEX IDX_5DC97A70CB944F1A (student_id), UNIQUE INDEX UNIQ_5DC97A707FFA6BA12469DE2CB944F1A (tuition_id, category_id, student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_cc950746ea43abd25c7669d7b6c1c350_idx (type), INDEX object_id_cc950746ea43abd25c7669d7b6c1c350_idx (object_id), INDEX discriminator_cc950746ea43abd25c7669d7b6c1c350_idx (discriminator), INDEX transaction_hash_cc950746ea43abd25c7669d7b6c1c350_idx (transaction_hash), INDEX blame_id_cc950746ea43abd25c7669d7b6c1c350_idx (blame_id), INDEX created_at_cc950746ea43abd25c7669d7b6c1c350_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_category (id INT UNSIGNED AUTO_INCREMENT NOT NULL, grade_type_id INT UNSIGNED DEFAULT NULL, display_name VARCHAR(255) NOT NULL, position INT NOT NULL, is_exportable TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_C2AD35D3F011E71 (grade_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_category_tuition (tuition_grade_category_id INT UNSIGNED NOT NULL, tuition_id INT UNSIGNED NOT NULL, INDEX IDX_E2358BB8738B27D5 (tuition_grade_category_id), INDEX IDX_E2358BB87FFA6BA (tuition_id), PRIMARY KEY(tuition_grade_category_id, tuition_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_category_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_4381ff0b996daa49c8d3688ea7f7f347_idx (type), INDEX object_id_4381ff0b996daa49c8d3688ea7f7f347_idx (object_id), INDEX discriminator_4381ff0b996daa49c8d3688ea7f7f347_idx (discriminator), INDEX transaction_hash_4381ff0b996daa49c8d3688ea7f7f347_idx (transaction_hash), INDEX blame_id_4381ff0b996daa49c8d3688ea7f7f347_idx (blame_id), INDEX created_at_4381ff0b996daa49c8d3688ea7f7f347_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, display_name VARCHAR(255) NOT NULL, `values` LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tuition_grade_type_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (type), INDEX object_id_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (object_id), INDEX discriminator_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (discriminator), INDEX transaction_hash_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (transaction_hash), INDEX blame_id_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (blame_id), INDEX created_at_5c1e1aa41771bcdbe9667f7e8a10dd77_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tuition_grade ADD CONSTRAINT FK_5DC97A707FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id)');
        $this->addSql('ALTER TABLE tuition_grade ADD CONSTRAINT FK_5DC97A7012469DE2 FOREIGN KEY (category_id) REFERENCES tuition_grade_category (id)');
        $this->addSql('ALTER TABLE tuition_grade ADD CONSTRAINT FK_5DC97A70CB944F1A FOREIGN KEY (student_id) REFERENCES student (id)');
        $this->addSql('ALTER TABLE tuition_grade_category ADD CONSTRAINT FK_C2AD35D3F011E71 FOREIGN KEY (grade_type_id) REFERENCES tuition_grade_type (id)');
        $this->addSql('ALTER TABLE tuition_grade_category_tuition ADD CONSTRAINT FK_E2358BB8738B27D5 FOREIGN KEY (tuition_grade_category_id) REFERENCES tuition_grade_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tuition_grade_category_tuition ADD CONSTRAINT FK_E2358BB87FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tuition_grade DROP FOREIGN KEY FK_5DC97A707FFA6BA');
        $this->addSql('ALTER TABLE tuition_grade DROP FOREIGN KEY FK_5DC97A7012469DE2');
        $this->addSql('ALTER TABLE tuition_grade DROP FOREIGN KEY FK_5DC97A70CB944F1A');
        $this->addSql('ALTER TABLE tuition_grade_category DROP FOREIGN KEY FK_C2AD35D3F011E71');
        $this->addSql('ALTER TABLE tuition_grade_category_tuition DROP FOREIGN KEY FK_E2358BB8738B27D5');
        $this->addSql('ALTER TABLE tuition_grade_category_tuition DROP FOREIGN KEY FK_E2358BB87FFA6BA');
        $this->addSql('DROP TABLE tuition_grade');
        $this->addSql('DROP TABLE tuition_grade_audit');
        $this->addSql('DROP TABLE tuition_grade_category');
        $this->addSql('DROP TABLE tuition_grade_category_tuition');
        $this->addSql('DROP TABLE tuition_grade_category_audit');
        $this->addSql('DROP TABLE tuition_grade_type');
        $this->addSql('DROP TABLE tuition_grade_type_audit');
    }
}
