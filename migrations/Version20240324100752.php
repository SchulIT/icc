<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240324100752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book_student_information (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, content LONGTEXT NOT NULL, `from` DATE NOT NULL, until DATE NOT NULL, include_in_grade_book_export TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_FD6121C5CB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_student_information_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_ae30d41bcab0dfeb45f83f430e05ec3b_idx (type), INDEX object_id_ae30d41bcab0dfeb45f83f430e05ec3b_idx (object_id), INDEX discriminator_ae30d41bcab0dfeb45f83f430e05ec3b_idx (discriminator), INDEX transaction_hash_ae30d41bcab0dfeb45f83f430e05ec3b_idx (transaction_hash), INDEX blame_id_ae30d41bcab0dfeb45f83f430e05ec3b_idx (blame_id), INDEX created_at_ae30d41bcab0dfeb45f83f430e05ec3b_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book_student_information ADD CONSTRAINT FK_FD6121C5CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_student_information DROP FOREIGN KEY FK_FD6121C5CB944F1A');
        $this->addSql('DROP TABLE book_student_information');
        $this->addSql('DROP TABLE book_student_information_audit');
    }
}
