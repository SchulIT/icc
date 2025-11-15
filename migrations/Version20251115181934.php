<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251115181934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE parents_day_teacher_room (parents_day_id INT UNSIGNED NOT NULL, teacher_id INT UNSIGNED NOT NULL, room_id INT UNSIGNED NOT NULL, INDEX IDX_EF2103302C60A5FA (parents_day_id), INDEX IDX_EF21033041807E1D (teacher_id), INDEX IDX_EF21033054177093 (room_id), PRIMARY KEY (parents_day_id, teacher_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE parents_day_teacher_room ADD CONSTRAINT FK_EF2103302C60A5FA FOREIGN KEY (parents_day_id) REFERENCES parents_day (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_teacher_room ADD CONSTRAINT FK_EF21033041807E1D FOREIGN KEY (teacher_id) REFERENCES teacher (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE parents_day_teacher_room ADD CONSTRAINT FK_EF21033054177093 FOREIGN KEY (room_id) REFERENCES room (id) ON DELETE CASCADE');
        }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE parents_day_teacher_room DROP FOREIGN KEY FK_EF2103302C60A5FA');
        $this->addSql('ALTER TABLE parents_day_teacher_room DROP FOREIGN KEY FK_EF21033041807E1D');
        $this->addSql('ALTER TABLE parents_day_teacher_room DROP FOREIGN KEY FK_EF21033054177093');
        $this->addSql('DROP TABLE parents_day_teacher_room');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
