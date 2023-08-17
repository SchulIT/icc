<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230817151226 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        parent::postUp($schema);

        $this->addSql("UPDATE student_absence_type SET additional_recipients = '[]'");
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_absence_type ADD additional_recipients LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE student_absence_type DROP additional_recipients');
    }
}
