<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201012141739 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    private $examRooms = [ ];
    private $substitutionRooms = [ ];
    private $substitutionReplacementRooms = [ ];

    public function preUp(Schema $schema): void {
        $this->write('Getting current rooms...');

        $sql = $this->connection->prepare('SELECT id, external_id FROM room');
        $result = $sql->executeQuery();

        $rooms = [ ];

        foreach($result->fetchAllAssociative() as $row) {
            $rooms[$row['external_id']] = intval($row['id']);
        }

        $this->write('Create exam-room-map...');

        $sql = $this->connection->prepare('SELECT id, rooms FROM exam');
        $result = $sql->executeQuery();

        foreach($result->fetchAllAssociative() as $row) {
            $examId = intval($row['id']);
            $examRooms = json_decode($row['rooms']);

            if(count($examRooms) > 0 && array_key_exists($examRooms[0], $rooms)) {
                $this->examRooms[$examId] = $rooms[$examRooms[0]];
            }
        }

        $this->write('Create substitution-room-map...');

        $sql = $this->connection->prepare('SELECT id, room, replacement_room FROM substitution');
        $result = $sql->executeQuery();

        foreach($result->fetchAllAssociative() as $row) {
            $id = intval($row['id']);
            $room = $row['room'];
            $replacementRoom = $row['replacement_room'];

            if($room !== null && array_key_exists($room, $rooms)) {
                $this->substitutionRooms[$id] = $rooms[$room];
            }

            if($replacementRoom !== null && array_key_exists($replacementRoom, $rooms)) {
                $this->substitutionReplacementRooms[$id] = $rooms[$replacementRoom];
            }
        }

        $this->write('Migrating data successfully saved.');
    }

    public function postUp(Schema $schema): void {
        $this->write('Migrate data...');

        foreach($this->examRooms as $examId => $roomId) {
            $this->connection->executeStatement('UPDATE exam SET room_id = :room WHERE id = :id', [
                'id' => $examId,
                'room' => $roomId
            ]);
        }

        foreach($this->substitutionRooms as $id => $roomId) {
            $this->connection->executeStatement('UPDATE substitution SET room_id = :room WHERE id = :id', [
                'id' => $id,
                'room' => $roomId
            ]);
        }

        foreach($this->substitutionReplacementRooms as $id => $roomId) {
            $this->connection->executeStatement('UPDATE substitution SET replacement_room_id = :room WHERE id = :id', [
                'id' => $id,
                'room' => $roomId
            ]);
        }

        $this->write('Data successfully migrated.');
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam ADD room_id INT UNSIGNED DEFAULT NULL, DROP rooms');
        $this->addSql('ALTER TABLE exam ADD CONSTRAINT FK_38BBA6C654177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_38BBA6C654177093 ON exam (room_id)');
        $this->addSql('ALTER TABLE substitution ADD room_id INT UNSIGNED DEFAULT NULL, ADD replacement_room_id INT UNSIGNED DEFAULT NULL, DROP room, DROP replacement_room');
        $this->addSql('ALTER TABLE substitution ADD CONSTRAINT FK_C7C90AE054177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE substitution ADD CONSTRAINT FK_C7C90AE08753B596 FOREIGN KEY (replacement_room_id) REFERENCES room (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_C7C90AE054177093 ON substitution (room_id)');
        $this->addSql('CREATE INDEX IDX_C7C90AE08753B596 ON substitution (replacement_room_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE exam DROP FOREIGN KEY FK_38BBA6C654177093');
        $this->addSql('DROP INDEX IDX_38BBA6C654177093 ON exam');
        $this->addSql('ALTER TABLE exam DROP room_id');
        $this->addSql('ALTER TABLE substitution DROP FOREIGN KEY FK_C7C90AE054177093');
        $this->addSql('ALTER TABLE substitution DROP FOREIGN KEY FK_C7C90AE08753B596');
        $this->addSql('DROP INDEX IDX_C7C90AE054177093 ON substitution');
        $this->addSql('DROP INDEX IDX_C7C90AE08753B596 ON substitution');
        $this->addSql('ALTER TABLE substitution ADD room VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, ADD replacement_room VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, DROP room_id, DROP replacement_room_id');
    }
}
