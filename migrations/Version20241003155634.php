<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241003155634 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chat_tag (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, color VARCHAR(255) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_tag_usertypes (chat_tag_id INT UNSIGNED NOT NULL, user_type_entity_id INT UNSIGNED NOT NULL, INDEX IDX_F5E2D71ADD0E5430 (chat_tag_id), INDEX IDX_F5E2D71A5E66E314 (user_type_entity_id), PRIMARY KEY(chat_tag_id, user_type_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE chat_user_tag (chat_id INT UNSIGNED NOT NULL, tag_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, INDEX IDX_DCBD01381A9A7125 (chat_id), INDEX IDX_DCBD0138BAD26311 (tag_id), INDEX IDX_DCBD0138A76ED395 (user_id), PRIMARY KEY(chat_id, tag_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chat_tag_usertypes ADD CONSTRAINT FK_F5E2D71ADD0E5430 FOREIGN KEY (chat_tag_id) REFERENCES chat_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_tag_usertypes ADD CONSTRAINT FK_F5E2D71A5E66E314 FOREIGN KEY (user_type_entity_id) REFERENCES user_type_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_user_tag ADD CONSTRAINT FK_DCBD01381A9A7125 FOREIGN KEY (chat_id) REFERENCES chat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_user_tag ADD CONSTRAINT FK_DCBD0138BAD26311 FOREIGN KEY (tag_id) REFERENCES chat_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chat_user_tag ADD CONSTRAINT FK_DCBD0138A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat_tag_usertypes DROP FOREIGN KEY FK_F5E2D71ADD0E5430');
        $this->addSql('ALTER TABLE chat_tag_usertypes DROP FOREIGN KEY FK_F5E2D71A5E66E314');
        $this->addSql('ALTER TABLE chat_user_tag DROP FOREIGN KEY FK_DCBD01381A9A7125');
        $this->addSql('ALTER TABLE chat_user_tag DROP FOREIGN KEY FK_DCBD0138BAD26311');
        $this->addSql('ALTER TABLE chat_user_tag DROP FOREIGN KEY FK_DCBD0138A76ED395');
        $this->addSql('DROP TABLE chat_tag');
        $this->addSql('DROP TABLE chat_tag_usertypes');
        $this->addSql('DROP TABLE chat_user_tag');
    }
}
