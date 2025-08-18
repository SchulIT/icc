<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210223204347 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        for($i = 1; $i <= 53; $i++) {
            $stmt = $this->connection->prepare('INSERT INTO week (number) VALUES(?)');
            $stmt->executeQuery([$i]);
        }
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE timetable_supervision_week (timetable_supervision_id INT UNSIGNED NOT NULL, week_id INT UNSIGNED NOT NULL, INDEX IDX_72D8EF6A2EA503EF (timetable_supervision_id), INDEX IDX_72D8EF6AC86F3B2F (week_id), PRIMARY KEY(timetable_supervision_id, week_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE timetable_week_week (timetable_week_id INT UNSIGNED NOT NULL, week_id INT UNSIGNED NOT NULL, INDEX IDX_B6951BE6490F7151 (timetable_week_id), INDEX IDX_B6951BE6C86F3B2F (week_id), PRIMARY KEY(timetable_week_id, week_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE week (id INT UNSIGNED AUTO_INCREMENT NOT NULL, number INT NOT NULL, UNIQUE INDEX UNIQ_5B5A69C096901F54 (number), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE timetable_supervision_week ADD CONSTRAINT FK_72D8EF6A2EA503EF FOREIGN KEY (timetable_supervision_id) REFERENCES timetable_supervision (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_supervision_week ADD CONSTRAINT FK_72D8EF6AC86F3B2F FOREIGN KEY (week_id) REFERENCES week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_week_week ADD CONSTRAINT FK_B6951BE6490F7151 FOREIGN KEY (timetable_week_id) REFERENCES timetable_week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_week_week ADD CONSTRAINT FK_B6951BE6C86F3B2F FOREIGN KEY (week_id) REFERENCES week (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE timetable_supervision DROP FOREIGN KEY FK_396D5F68C86F3B2F');
        $this->addSql('DROP INDEX IDX_396D5F68C86F3B2F ON timetable_supervision');
        $this->addSql('ALTER TABLE timetable_supervision DROP week_id');
        $this->addSql('ALTER TABLE timetable_week DROP week_mod');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE timetable_supervision_week DROP FOREIGN KEY FK_72D8EF6AC86F3B2F');
        $this->addSql('ALTER TABLE timetable_week_week DROP FOREIGN KEY FK_B6951BE6C86F3B2F');
        $this->addSql('DROP TABLE timetable_supervision_week');
        $this->addSql('DROP TABLE timetable_week_week');
        $this->addSql('DROP TABLE week');
        $this->addSql('ALTER TABLE timetable_supervision ADD week_id INT UNSIGNED DEFAULT NULL');
        $this->addSql('ALTER TABLE timetable_supervision ADD CONSTRAINT FK_396D5F68C86F3B2F FOREIGN KEY (week_id) REFERENCES timetable_week (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_396D5F68C86F3B2F ON timetable_supervision (week_id)');
        $this->addSql('ALTER TABLE timetable_week ADD week_mod INT NOT NULL');
    }
}
