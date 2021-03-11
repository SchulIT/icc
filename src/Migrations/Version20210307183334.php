<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210307183334 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE substitution_rooms (substitution_id INT UNSIGNED NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_35C8D460D7F487C9 (substitution_id), INDEX IDX_35C8D46054177093 (room_id), PRIMARY KEY(substitution_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE substitution_replacement_rooms (substitution_id INT UNSIGNED NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_987D303ED7F487C9 (substitution_id), INDEX IDX_987D303E54177093 (room_id), PRIMARY KEY(substitution_id, room_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE substitution_rooms ADD CONSTRAINT FK_35C8D460D7F487C9 FOREIGN KEY (substitution_id) REFERENCES substitution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE substitution_rooms ADD CONSTRAINT FK_35C8D46054177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE substitution_replacement_rooms ADD CONSTRAINT FK_987D303ED7F487C9 FOREIGN KEY (substitution_id) REFERENCES substitution (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE substitution_replacement_rooms ADD CONSTRAINT FK_987D303E54177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE substitution DROP FOREIGN KEY FK_C7C90AE054177093');
        $this->addSql('ALTER TABLE substitution DROP FOREIGN KEY FK_C7C90AE08753B596');
        $this->addSql('DROP INDEX IDX_C7C90AE08753B596 ON substitution');
        $this->addSql('DROP INDEX IDX_C7C90AE054177093 ON substitution');
        $this->addSql('ALTER TABLE substitution DROP room_id, DROP replacement_room_id');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE substitution_rooms');
        $this->addSql('DROP TABLE substitution_replacement_rooms');
        $this->addSql('ALTER TABLE substitution ADD room_id INT UNSIGNED DEFAULT NULL, ADD replacement_room_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE substitution ADD CONSTRAINT FK_C7C90AE054177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE substitution ADD CONSTRAINT FK_C7C90AE08753B596 FOREIGN KEY (replacement_room_id) REFERENCES room (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_C7C90AE08753B596 ON substitution (replacement_room_id)');
        $this->addSql('CREATE INDEX IDX_C7C90AE054177093 ON substitution (room_id)');
    }
}
