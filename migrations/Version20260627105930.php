<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260627105930 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment ADD is_recurring TINYINT NOT NULL, ADD recurring_uuid CHAR(36) DEFAULT NULL, ADD recurring_grade_names JSON DEFAULT NULL, CHANGE uuid uuid CHAR(36) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appointment DROP is_recurring, DROP recurring_uuid, DROP recurring_grade_names, CHANGE uuid uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\'');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
