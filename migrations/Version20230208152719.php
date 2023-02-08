<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230208152719 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE grade_responsibility (id INT UNSIGNED AUTO_INCREMENT NOT NULL, grade_id INT UNSIGNED DEFAULT NULL, section_id INT UNSIGNED DEFAULT NULL, task VARCHAR(255) NOT NULL, person VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_E467E045FE19A1A8 (grade_id), INDEX IDX_E467E045D823E37A (section_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE grade_responsibility ADD CONSTRAINT FK_E467E045FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE grade_responsibility ADD CONSTRAINT FK_E467E045D823E37A FOREIGN KEY (section_id) REFERENCES section (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE grade_responsibility DROP FOREIGN KEY FK_E467E045FE19A1A8');
        $this->addSql('ALTER TABLE grade_responsibility DROP FOREIGN KEY FK_E467E045D823E37A');
        $this->addSql('DROP TABLE grade_responsibility');
    }
}
