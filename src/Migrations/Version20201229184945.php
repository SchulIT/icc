<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229184945 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE teacher_tag_visibilities (teacher_tag_id INT UNSIGNED NOT NULL, user_type_entity_id INT UNSIGNED NOT NULL, INDEX IDX_349960CAAF59B1E4 (teacher_tag_id), INDEX IDX_349960CA5E66E314 (user_type_entity_id), PRIMARY KEY(teacher_tag_id, user_type_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE teacher_tag_visibilities ADD CONSTRAINT FK_349960CAAF59B1E4 FOREIGN KEY (teacher_tag_id) REFERENCES teacher_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE teacher_tag_visibilities ADD CONSTRAINT FK_349960CA5E66E314 FOREIGN KEY (user_type_entity_id) REFERENCES user_type_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE teacher_tag_visibilities');
    }
}
