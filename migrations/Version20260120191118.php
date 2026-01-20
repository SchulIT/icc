<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260120191118 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL, diffs JSON DEFAULT NULL, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL, created_at DATETIME NOT NULL, INDEX type_e06395edc291d0719bee26fd39a32e8a_idx (type), INDEX object_id_e06395edc291d0719bee26fd39a32e8a_idx (object_id), INDEX discriminator_e06395edc291d0719bee26fd39a32e8a_idx (discriminator), INDEX transaction_hash_e06395edc291d0719bee26fd39a32e8a_idx (transaction_hash), INDEX blame_id_e06395edc291d0719bee26fd39a32e8a_idx (blame_id), INDEX created_at_e06395edc291d0719bee26fd39a32e8a_idx (created_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_audit');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
