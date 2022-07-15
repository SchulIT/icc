<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220715133925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sick_note_attachment DROP FOREIGN KEY FK_99CB808AD0F9AB9D');
        $this->addSql('CREATE TABLE student_absence (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, type_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED DEFAULT NULL, approved_by_id INT UNSIGNED DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, approved_at DATETIME DEFAULT NULL, is_approved TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', from_date DATE NOT NULL, from_lesson INT NOT NULL, until_date DATE NOT NULL, until_lesson INT NOT NULL, INDEX IDX_9B6C5531CB944F1A (student_id), INDEX IDX_9B6C5531C54C8C93 (type_id), INDEX IDX_9B6C5531B03A8386 (created_by_id), INDEX IDX_9B6C55312D234F6A (approved_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (type), INDEX object_id_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (object_id), INDEX discriminator_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (discriminator), INDEX transaction_hash_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (transaction_hash), INDEX blame_id_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (blame_id), INDEX created_at_5c1e3d6b65a33cbfa2296e30f8c898ee_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, absence_id INT UNSIGNED DEFAULT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, size INT NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_64702A042DFF238F (absence_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_attachment_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_d09aa3a70fa4729b232d5d809ef1a236_idx (type), INDEX object_id_d09aa3a70fa4729b232d5d809ef1a236_idx (object_id), INDEX discriminator_d09aa3a70fa4729b232d5d809ef1a236_idx (discriminator), INDEX transaction_hash_d09aa3a70fa4729b232d5d809ef1a236_idx (transaction_hash), INDEX blame_id_d09aa3a70fa4729b232d5d809ef1a236_idx (blame_id), INDEX created_at_d09aa3a70fa4729b232d5d809ef1a236_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, absence_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED DEFAULT NULL, message LONGTEXT NOT NULL, created_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_239F9DD12DFF238F (absence_id), INDEX IDX_239F9DD1B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE student_absence_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, must_approve TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C5531CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C5531C54C8C93 FOREIGN KEY (type_id) REFERENCES student_absence_type (id)');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C5531B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE student_absence ADD CONSTRAINT FK_9B6C55312D234F6A FOREIGN KEY (approved_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE student_absence_attachment ADD CONSTRAINT FK_64702A042DFF238F FOREIGN KEY (absence_id) REFERENCES student_absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_message ADD CONSTRAINT FK_239F9DD12DFF238F FOREIGN KEY (absence_id) REFERENCES student_absence (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_message ADD CONSTRAINT FK_239F9DD1B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE sick_note');
        $this->addSql('DROP TABLE sick_note_attachment');
        $this->addSql('DROP TABLE sick_note_attachment_audit');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_absence_attachment DROP FOREIGN KEY FK_64702A042DFF238F');
        $this->addSql('ALTER TABLE student_absence_message DROP FOREIGN KEY FK_239F9DD12DFF238F');
        $this->addSql('ALTER TABLE student_absence DROP FOREIGN KEY FK_9B6C5531C54C8C93');
        $this->addSql('CREATE TABLE sick_note (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, created_by_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', from_date DATE NOT NULL, from_lesson INT NOT NULL, until_date DATE NOT NULL, until_lesson INT NOT NULL, reason VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:sick_reason)\', email VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, phone VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ordered_by VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, message LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_6C5484DDB03A8386 (created_by_id), INDEX IDX_6C5484DDCB944F1A (student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sick_note_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, sick_note_id INT UNSIGNED DEFAULT NULL, filename VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, path VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, size INT NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', INDEX IDX_99CB808AD0F9AB9D (sick_note_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE sick_note_attachment_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, object_id VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, discriminator VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, transaction_hash VARCHAR(40) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, diffs LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_fqdn VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, blame_user_firewall VARCHAR(100) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ip VARCHAR(45) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX blame_id_5805813099f68cd4f1fdc27203033c44_idx (blame_id), INDEX object_id_5805813099f68cd4f1fdc27203033c44_idx (object_id), INDEX created_at_5805813099f68cd4f1fdc27203033c44_idx (created_at), INDEX discriminator_5805813099f68cd4f1fdc27203033c44_idx (discriminator), INDEX transaction_hash_5805813099f68cd4f1fdc27203033c44_idx (transaction_hash), INDEX type_5805813099f68cd4f1fdc27203033c44_idx (type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE sick_note ADD CONSTRAINT FK_6C5484DDB03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE sick_note ADD CONSTRAINT FK_6C5484DDCB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sick_note_attachment ADD CONSTRAINT FK_99CB808AD0F9AB9D FOREIGN KEY (sick_note_id) REFERENCES sick_note (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE student_absence');
        $this->addSql('DROP TABLE student_absence_audit');
        $this->addSql('DROP TABLE student_absence_attachment');
        $this->addSql('DROP TABLE student_absence_attachment_audit');
        $this->addSql('DROP TABLE student_absence_message');
        $this->addSql('DROP TABLE student_absence_type');
    }
}
