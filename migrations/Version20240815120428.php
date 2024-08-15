<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240815120428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_limited_membership_audit CHANGE diffs diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade DROP FOREIGN KEY FK_5BFA54E6CC3C66FC');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade ADD color VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade ADD CONSTRAINT FK_5BFA54E6CC3C66FC FOREIGN KEY (catalog_id) REFERENCES tuition_grade_catalog (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade DROP FOREIGN KEY FK_5BFA54E6CC3C66FC');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade DROP color');
        $this->addSql('ALTER TABLE tuition_grade_catalog_grade ADD CONSTRAINT FK_5BFA54E6CC3C66FC FOREIGN KEY (catalog_id) REFERENCES tuition_grade_catalog (id)');
        $this->addSql('ALTER TABLE grade_limited_membership_audit CHANGE diffs diffs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\'');
    }
}
