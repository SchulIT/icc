<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260617160846 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE report_remark (id INT UNSIGNED AUTO_INCREMENT NOT NULL, uuid CHAR(36) NOT NULL, student_id INT UNSIGNED NOT NULL, remark LONGTEXT NOT NULL, created_by_id INT UNSIGNED DEFAULT NULL, section_id INT UNSIGNED DEFAULT NULL, INDEX IDX_C8390DA6CB944F1A (student_id), INDEX IDX_C8390DA6B03A8386 (created_by_id), INDEX IDX_C8390DA6D823E37A (section_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE report_remark_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_35fd8b445221d68b61569be9e94af9e4_idx (type), INDEX object_id_35fd8b445221d68b61569be9e94af9e4_idx (object_id), INDEX discriminator_35fd8b445221d68b61569be9e94af9e4_idx (discriminator), INDEX transaction_hash_35fd8b445221d68b61569be9e94af9e4_idx (transaction_hash), INDEX blame_id_35fd8b445221d68b61569be9e94af9e4_idx (blame_id), INDEX created_at_35fd8b445221d68b61569be9e94af9e4_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE report_remark ADD CONSTRAINT FK_C8390DA6CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_remark ADD CONSTRAINT FK_C8390DA6B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE report_remark ADD CONSTRAINT FK_C8390DA6D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report_remark DROP FOREIGN KEY FK_C8390DA6CB944F1A');
        $this->addSql('ALTER TABLE report_remark DROP FOREIGN KEY FK_C8390DA6B03A8386');
        $this->addSql('ALTER TABLE report_remark DROP FOREIGN KEY FK_C8390DA6D823E37A');
        $this->addSql('DROP TABLE report_remark');
        $this->addSql('DROP TABLE report_remark_audit');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
