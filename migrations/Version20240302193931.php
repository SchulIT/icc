<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302193931 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat (id INT UNSIGNED AUTO_INCREMENT NOT NULL, topic VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_user (chat_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_2B0F4B081A9A7125 (chat_id), INDEX IDX_2B0F4B08A76ED395 (user_id), PRIMARY KEY(chat_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_message (id INT UNSIGNED AUTO_INCREMENT NOT NULL, chat_id INT UNSIGNED NOT NULL, created_by_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, content LONGTEXT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_FAB3FC161A9A7125 (chat_id), INDEX IDX_FAB3FC16B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_message_seen_by (chat_message_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_7894FA47948B568F (chat_message_id), INDEX IDX_7894FA47A76ED395 (user_id), PRIMARY KEY(chat_message_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_message_attachment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, message_id INT UNSIGNED NOT NULL, filename VARCHAR(255) NOT NULL, path VARCHAR(255) NOT NULL, size INT NOT NULL, updated_at DATETIME NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4965BF86537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B081A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_user ADD CONSTRAINT FK_2B0F4B08A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC161A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message ADD CONSTRAINT FK_FAB3FC16B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE chat_message_seen_by ADD CONSTRAINT FK_7894FA47948B568F FOREIGN KEY (chat_message_id) REFERENCES chat_message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message_seen_by ADD CONSTRAINT FK_7894FA47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_message_attachment ADD CONSTRAINT FK_4965BF86537A1329 FOREIGN KEY (message_id) REFERENCES chat_message (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_user DROP FOREIGN KEY FK_2B0F4B081A9A7125');
        $this->addSql('ALTER TABLE chat_user DROP FOREIGN KEY FK_2B0F4B08A76ED395');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_FAB3FC161A9A7125');
        $this->addSql('ALTER TABLE chat_message DROP FOREIGN KEY FK_FAB3FC16B03A8386');
        $this->addSql('ALTER TABLE chat_message_seen_by DROP FOREIGN KEY FK_7894FA47948B568F');
        $this->addSql('ALTER TABLE chat_message_seen_by DROP FOREIGN KEY FK_7894FA47A76ED395');
        $this->addSql('ALTER TABLE chat_message_attachment DROP FOREIGN KEY FK_4965BF86537A1329');
        $this->addSql('DROP TABLE chat');
        $this->addSql('DROP TABLE chat_user');
        $this->addSql('DROP TABLE chat_message');
        $this->addSql('DROP TABLE chat_message_seen_by');
        $this->addSql('DROP TABLE chat_message_attachment');
    }
}
