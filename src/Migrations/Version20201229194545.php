<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201229194545 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    private $rooms = [ ];

    public function preUp(Schema $schema): void {
        $stmt = $this->connection->executeQuery('SELECT * FROM room');
        $this->rooms = $stmt->fetchAllAssociative();
    }

    public function postUp(Schema $schema): void {
        $stmt = $this->connection->prepare('INSERT INTO resource_type (name) VALUES (?)');
        $stmt->bindValue(1, "Raum", "string");
        $stmt->execute();

        $typeId = $this->connection->lastInsertId();

        foreach($this->rooms as $room) {
            $stmt = $this->connection->prepare('INSERT INTO resource (id, type_id, name, description, is_reservation_enabled, uuid, class) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->bindValue(1, $room['id'], 'integer');
            $stmt->bindValue(2, $typeId, 'integer');
            $stmt->bindValue(3, $room['name'], 'string');
            $stmt->bindValue(4, $room['description'], 'text');
            $stmt->bindValue(5, $room['is_reservation_enabled'], 'boolean');
            $stmt->bindValue(6, $room['uuid'], 'string');
            $stmt->bindValue(7, 'room', 'string');

            $stmt->execute();
        }
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('SET FOREIGN_KEY_CHECKS=0;');
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE resource (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type_id INT UNSIGNED DEFAULT NULL, name VARCHAR(16) NOT NULL, `description` LONGTEXT DEFAULT NULL, is_reservation_enabled TINYINT(1) NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', class VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_BC91F4165E237E06 (name), INDEX IDX_BC91F416C54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE resource_type (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) DEFAULT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE resource ADD CONSTRAINT FK_BC91F416C54C8C93 FOREIGN KEY (type_id) REFERENCES resource_type (id) ON DELETE SET NULL');
        $this->addSql('DROP INDEX UNIQ_729F519B5E237E06 ON room');
        $this->addSql('ALTER TABLE room DROP name, DROP description, DROP uuid, DROP is_reservation_enabled, CHANGE id id INT UNSIGNED NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519BBF396750 FOREIGN KEY (id) REFERENCES resource (id) ON DELETE CASCADE');
        $this->addSql('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519BBF396750');
        $this->addSql('ALTER TABLE resource DROP FOREIGN KEY FK_BC91F416C54C8C93');
        $this->addSql('DROP TABLE resource');
        $this->addSql('DROP TABLE resource_type');
        $this->addSql('ALTER TABLE room ADD name VARCHAR(16) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD uuid CHAR(36) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:uuid)\', ADD is_reservation_enabled TINYINT(1) NOT NULL, CHANGE id id INT UNSIGNED AUTO_INCREMENT NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B5E237E06 ON room (name)');
    }
}
