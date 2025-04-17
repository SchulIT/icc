<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417174255 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE return_item (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED NOT NULL, type_id INT UNSIGNED NOT NULL, created_by_id INT UNSIGNED DEFAULT NULL, returned_by_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, is_returned TINYINT(1) NOT NULL, returned_at DATETIME DEFAULT NULL, return_comment LONGTEXT DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_7EED95F7CB944F1A (student_id), INDEX IDX_7EED95F7C54C8C93 (type_id), INDEX IDX_7EED95F7B03A8386 (created_by_id), INDEX IDX_7EED95F771AD87D9 (returned_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE return_item_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_decf422e146d3879723d64796e829761_idx (type), INDEX object_id_decf422e146d3879723d64796e829761_idx (object_id), INDEX discriminator_decf422e146d3879723d64796e829761_idx (discriminator), INDEX transaction_hash_decf422e146d3879723d64796e829761_idx (transaction_hash), INDEX blame_id_decf422e146d3879723d64796e829761_idx (blame_id), INDEX created_at_decf422e146d3879723d64796e829761_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE return_item_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, display_name VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, notification_note LONGTEXT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE return_item_type_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL, diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_e15e4d35f943787e9a7a145289baa3dd_idx (type), INDEX object_id_e15e4d35f943787e9a7a145289baa3dd_idx (object_id), INDEX discriminator_e15e4d35f943787e9a7a145289baa3dd_idx (discriminator), INDEX transaction_hash_e15e4d35f943787e9a7a145289baa3dd_idx (transaction_hash), INDEX blame_id_e15e4d35f943787e9a7a145289baa3dd_idx (blame_id), INDEX created_at_e15e4d35f943787e9a7a145289baa3dd_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE return_item ADD CONSTRAINT FK_7EED95F7CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE return_item ADD CONSTRAINT FK_7EED95F7C54C8C93 FOREIGN KEY (type_id) REFERENCES return_item_type (id) ON DELETE RESTRICT');
        $this->addSql('ALTER TABLE return_item ADD CONSTRAINT FK_7EED95F7B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE return_item ADD CONSTRAINT FK_7EED95F771AD87D9 FOREIGN KEY (returned_by_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE return_item DROP FOREIGN KEY FK_7EED95F7CB944F1A');
        $this->addSql('ALTER TABLE return_item DROP FOREIGN KEY FK_7EED95F7C54C8C93');
        $this->addSql('ALTER TABLE return_item DROP FOREIGN KEY FK_7EED95F7B03A8386');
        $this->addSql('ALTER TABLE return_item DROP FOREIGN KEY FK_7EED95F771AD87D9');
        $this->addSql('DROP TABLE return_item');
        $this->addSql('DROP TABLE return_item_audit');
        $this->addSql('DROP TABLE return_item_type');
        $this->addSql('DROP TABLE return_item_type_audit');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
