<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Migrations\TimetableSettingsDependentMigrationInterface;
use App\Migrations\TimetableTimeHelperDependentMigrationInterface;
use App\Settings\TimetableSettings;
use App\Timetable\TimetableTimeHelper;
use DateTime;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201125192634 extends AbstractMigration implements TimetableTimeHelperDependentMigrationInterface, TimetableSettingsDependentMigrationInterface
{
    private ?TimetableTimeHelper $timetableTimeHelper = null;

    private ?TimetableSettings $timetableSettings = null;

    /** @var array[] */
    private array $sickNotes = [ ];

    /**
     * @inheritdoc
     */
    public function setTimetableTimeHelper(TimetableTimeHelper $timetableTimeHelper): void {
        $this->timetableTimeHelper = $timetableTimeHelper;
    }

    public function setTimetableSettings(TimetableSettings $settings): void {
        $this->timetableSettings = $settings;
    }

    public function preUp(Schema $schema): void {
        $stmt = $this->connection->executeQuery('SELECT * FROM sick_note');
        $this->sickNotes = $stmt->fetchAllAssociative();
    }

    public function postUp(Schema $schema): void {
        foreach($this->sickNotes as $note) {
            $createdAt = new DateTime($note['created_at']);
            $from = $this->timetableTimeHelper->getLessonDateForDateTime($createdAt);

            $until = new DateTime($note['until']);
            $until->setTime(0, 0, 0);

            $stmt = $this->connection->prepare('UPDATE sick_note SET from_date = ?, from_lesson = ?, until_date = ?, until_lesson = ? WHERE id = ?');
            $stmt->bindValue(1, $from->getDate(), "date");
            $stmt->bindValue(2, $from->getLesson(), "integer");
            $stmt->bindValue(3, $until, "date");
            $stmt->bindValue(4, $this->timetableSettings->getMaxLessons(), "integer");
            $stmt->bindValue(5, $note['id'], "string");
            $stmt->execute();
        }
    }

    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sick_note ADD from_date DATE NOT NULL, ADD from_lesson INT NOT NULL, ADD until_date DATE NOT NULL, ADD until_lesson INT NOT NULL, DROP until');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sick_note ADD until DATETIME NOT NULL, DROP from_date, DROP from_lesson, DROP until_date, DROP until_lesson');
    }


}
