<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240105165938 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grade_limited_membership (id INT UNSIGNED AUTO_INCREMENT NOT NULL, student_id INT UNSIGNED DEFAULT NULL, grade_id INT UNSIGNED DEFAULT NULL, section_id INT UNSIGNED DEFAULT NULL, until DATE NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_34340F83CB944F1A (student_id), INDEX IDX_34340F83FE19A1A8 (grade_id), INDEX IDX_34340F83D823E37A (section_id), UNIQUE INDEX UNIQ_34340F83D823E37AFE19A1A8CB944F1A (section_id, grade_id, student_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE grade_limited_membership_audit (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:json)\', blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_2141e271252e6b2902429c1694ebca2c_idx (type), INDEX object_id_2141e271252e6b2902429c1694ebca2c_idx (object_id), INDEX discriminator_2141e271252e6b2902429c1694ebca2c_idx (discriminator), INDEX transaction_hash_2141e271252e6b2902429c1694ebca2c_idx (transaction_hash), INDEX blame_id_2141e271252e6b2902429c1694ebca2c_idx (blame_id), INDEX created_at_2141e271252e6b2902429c1694ebca2c_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grade_limited_membership ADD CONSTRAINT FK_34340F83CB944F1A FOREIGN KEY (student_id) REFERENCES student (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grade_limited_membership ADD CONSTRAINT FK_34340F83FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
        $this->addSql('ALTER TABLE grade_limited_membership ADD CONSTRAINT FK_34340F83D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_limited_membership DROP FOREIGN KEY FK_34340F83CB944F1A');
        $this->addSql('ALTER TABLE grade_limited_membership DROP FOREIGN KEY FK_34340F83FE19A1A8');
        $this->addSql('ALTER TABLE grade_limited_membership DROP FOREIGN KEY FK_34340F83D823E37A');
        $this->addSql('DROP TABLE grade_limited_membership');
        $this->addSql('DROP TABLE grade_limited_membership_audit');
    }
}
