<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250127151750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_notification_setting (type VARCHAR(255) NOT NULL, target VARCHAR(255) NOT NULL, user_id INT UNSIGNED NOT NULL, is_enabled TINYINT(1) NOT NULL, INDEX IDX_344BE150A76ED395 (user_id), PRIMARY KEY(user_id, type, target)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_notification_setting ADD CONSTRAINT FK_344BE150A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user DROP is_email_notifications_enabled');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_notification_setting DROP FOREIGN KEY FK_344BE150A76ED395');
        $this->addSql('DROP TABLE user_notification_setting');
        $this->addSql('ALTER TABLE user ADD is_email_notifications_enabled TINYINT(1) NOT NULL');
    }
}
