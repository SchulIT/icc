<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512145113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tuition_grade_category DROP FOREIGN KEY FK_C2AD35D3F011E71');
        $this->addSql('DROP INDEX IDX_C2AD35D3F011E71 ON tuition_grade_category');
        $this->addSql('ALTER TABLE tuition_grade_category CHANGE grade_type_id catalog_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition_grade_category ADD CONSTRAINT FK_C2AD35DCC3C66FC FOREIGN KEY (catalog_id) REFERENCES tuition_grade_catalog (id)');
        $this->addSql('CREATE INDEX IDX_C2AD35DCC3C66FC ON tuition_grade_category (catalog_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tuition_grade_category DROP FOREIGN KEY FK_C2AD35DCC3C66FC');
        $this->addSql('DROP INDEX IDX_C2AD35DCC3C66FC ON tuition_grade_category');
        $this->addSql('ALTER TABLE tuition_grade_category CHANGE catalog_id grade_type_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE tuition_grade_category ADD CONSTRAINT FK_C2AD35D3F011E71 FOREIGN KEY (grade_type_id) REFERENCES tuition_grade_catalog (id)');
        $this->addSql('CREATE INDEX IDX_C2AD35D3F011E71 ON tuition_grade_category (grade_type_id)');
    }
}
