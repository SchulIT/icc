<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210730153954 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_group_grades DROP FOREIGN KEY FK_C647F0BAFE19A1A8');
        $this->addSql('ALTER TABLE study_group_grades ADD CONSTRAINT FK_C647F0BAFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE study_group_grades DROP FOREIGN KEY FK_C647F0BAFE19A1A8');
        $this->addSql('ALTER TABLE study_group_grades ADD CONSTRAINT FK_C647F0BAFE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
    }
}
