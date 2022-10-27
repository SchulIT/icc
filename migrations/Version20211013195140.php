<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211013195140 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        $this->connection->executeQuery('UPDATE sick_note SET reason = \'sick\'');
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sick_note_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sick_note_id INT UNSIGNED DEFAULT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, size INT NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_99CB808AD0F9AB9D (sick_note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sick_note_attachment_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_5805813099f68cd4f1fdc27203033c44_idx (type), INDEX object_id_5805813099f68cd4f1fdc27203033c44_idx (object_id), INDEX discriminator_5805813099f68cd4f1fdc27203033c44_idx (discriminator), INDEX transaction_hash_5805813099f68cd4f1fdc27203033c44_idx (transaction_hash), INDEX blame_id_5805813099f68cd4f1fdc27203033c44_idx (blame_id), INDEX created_at_5805813099f68cd4f1fdc27203033c44_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sick_note_attachment ADD CONSTRAINT FK_99CB808AD0F9AB9D FOREIGN KEY (sick_note_id) REFERENCES sick_note (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sick_note ADD reason VARCHAR(255) NOT NULL COMMENT \'(DC2Type:sick_reason)\', ADD email VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(255) DEFAULT NULL, ADD ordered_by VARCHAR(255) DEFAULT NULL, ADD message LONGTEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sick_note_attachment');
        $this->addSql('DROP TABLE sick_note_attachment_audit');
        $this->addSql('ALTER TABLE sick_note DROP reason, DROP email, DROP phone, DROP ordered_by, DROP message');
    }
}
