<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210101165639 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE display_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_11f990cdc0e3bc1a524ed32de1aae0fb_idx (type), INDEX object_id_11f990cdc0e3bc1a524ed32de1aae0fb_idx (object_id), INDEX discriminator_11f990cdc0e3bc1a524ed32de1aae0fb_idx (discriminator), INDEX transaction_hash_11f990cdc0e3bc1a524ed32de1aae0fb_idx (transaction_hash), INDEX blame_id_11f990cdc0e3bc1a524ed32de1aae0fb_idx (blame_id), INDEX created_at_11f990cdc0e3bc1a524ed32de1aae0fb_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_6c9cd0347db459f067f101fbda96e3ae_idx (type), INDEX object_id_6c9cd0347db459f067f101fbda96e3ae_idx (object_id), INDEX discriminator_6c9cd0347db459f067f101fbda96e3ae_idx (discriminator), INDEX transaction_hash_6c9cd0347db459f067f101fbda96e3ae_idx (transaction_hash), INDEX blame_id_6c9cd0347db459f067f101fbda96e3ae_idx (blame_id), INDEX created_at_6c9cd0347db459f067f101fbda96e3ae_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_type_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_964270a12a79cf2336c43ffe3857923e_idx (type), INDEX object_id_964270a12a79cf2336c43ffe3857923e_idx (object_id), INDEX discriminator_964270a12a79cf2336c43ffe3857923e_idx (discriminator), INDEX transaction_hash_964270a12a79cf2336c43ffe3857923e_idx (transaction_hash), INDEX blame_id_964270a12a79cf2336c43ffe3857923e_idx (blame_id), INDEX created_at_964270a12a79cf2336c43ffe3857923e_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE display_audit');
        $this->addSql('DROP TABLE resource_audit');
        $this->addSql('DROP TABLE resource_type_audit');
    }
}
