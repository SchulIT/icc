<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231231135309 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function postUp(Schema $schema): void {
        $result = $this->connection->executeQuery('SELECT teacher_absence_lesson.id, timetable_lesson.tuition_id, timetable_lesson.date, timetable_lesson.lesson_start, timetable_lesson.lesson_end FROM teacher_absence_lesson LEFT JOIN timetable_lesson ON teacher_absence_lesson.lesson_id = timetable_lesson.id WHERE teacher_absence_lesson.lesson_id IS NOT NULL');

        while($row = $result->fetchAssociative()) {
            $stmt = $this->connection->prepare('UPDATE teacher_absence_lesson SET teacher_absence_lesson.tuition_id = :tuition, teacher_absence_lesson.lesson_start = :start, teacher_absence_lesson.lesson_end = :end, teacher_absence_lesson.date = :date WHERE id = :id');
            $stmt->bindValue('tuition', $row['tuition_id']);
            $stmt->bindValue('start', $row['lesson_start']);
            $stmt->bindValue('end', $row['lesson_end']);
            $stmt->bindValue('date', $row['date']);
            $stmt->bindValue('id', $row['id']);

            $stmt->executeQuery();
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD tuition_id INT UNSIGNED DEFAULT NULL, ADD date DATE NOT NULL, ADD lesson_start INT NOT NULL, ADD lesson_end INT NOT NULL');
        $this->addSql('ALTER TABLE teacher_absence_lesson ADD CONSTRAINT FK_25D18E2B7FFA6BA FOREIGN KEY (tuition_id) REFERENCES tuition (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_25D18E2B7FFA6BA ON teacher_absence_lesson (tuition_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP FOREIGN KEY FK_25D18E2B7FFA6BA');
        $this->addSql('DROP INDEX IDX_25D18E2B7FFA6BA ON teacher_absence_lesson');
        $this->addSql('ALTER TABLE teacher_absence_lesson DROP tuition_id, DROP date, DROP lesson_start, DROP lesson_end');
    }
}
