<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260707190320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE subject_chair (chair_type VARCHAR(255) NOT NULL, id INT UNSIGNED AUTO_INCREMENT NOT NULL, subject_id INT UNSIGNED DEFAULT NULL, teacher_id INT UNSIGNED DEFAULT NULL, INDEX IDX_B490855D23EDC87 (subject_id), INDEX IDX_B490855D41807E1D (teacher_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE subject_chair_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_403f03e2eee6e17c34a317fb8377d948_idx (type), INDEX object_id_403f03e2eee6e17c34a317fb8377d948_idx (object_id), INDEX discriminator_403f03e2eee6e17c34a317fb8377d948_idx (discriminator), INDEX transaction_hash_403f03e2eee6e17c34a317fb8377d948_idx (transaction_hash), INDEX blame_id_403f03e2eee6e17c34a317fb8377d948_idx (blame_id), INDEX created_at_403f03e2eee6e17c34a317fb8377d948_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE subject_chair ADD CONSTRAINT FK_B490855D23EDC87 FOREIGN KEY (subject_id) REFERENCES subject (id)');
        $this->addSql('ALTER TABLE subject_chair ADD CONSTRAINT FK_B490855D41807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subject_chair DROP FOREIGN KEY FK_B490855D23EDC87');
        $this->addSql('ALTER TABLE subject_chair DROP FOREIGN KEY FK_B490855D41807E1D');
        $this->addSql('DROP TABLE subject_chair');
        $this->addSql('DROP TABLE subject_chair_audit');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
