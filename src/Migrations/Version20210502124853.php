<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210502124853 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE substitution_replacement_grades (substitution_id INT UNSIGNED NOT NULL, grade_id INT UNSIGNED NOT NULL, INDEX IDX_E20A96E0D7F487C9 (substitution_id), INDEX IDX_E20A96E0FE19A1A8 (grade_id), PRIMARY KEY(substitution_id, grade_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE substitution_replacement_grades ADD CONSTRAINT FK_E20A96E0D7F487C9 FOREIGN KEY (substitution_id) REFERENCES substitution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE substitution_replacement_grades ADD CONSTRAINT FK_E20A96E0FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE substitution_replacement_grades');
    }
}
