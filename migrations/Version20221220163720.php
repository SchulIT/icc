<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221220163720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    private function removeSettings(): void {
        // Remove all settings (CAUTION: THIS IS IRREVERSIBLE)
        $settingsToRemove = [
            'notifications.email.user_types',
            'substitutions.absences.visibility',
            'notifications.web_push.user_types',
            'exams.visibility'
        ];

        foreach($settingsToRemove as $settingName) {
            $this->addSql('DELETE FROM setting WHERE `key` = ?', [ $settingName ]);
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE display CHANGE target_user_type target_user_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE grade_teacher CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE ics_access_token CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE message CHANGE scope scope VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE student CHANGE gender gender VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE study_group CHANGE type type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE teacher CHANGE gender gender VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE user_type user_type VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user_type_entity CHANGE user_type user_type VARCHAR(255) NOT NULL');

        $this->removeSettings();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE display CHANGE target_user_type target_user_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:display_target_user_type)\'');
        $this->addSql('ALTER TABLE grade_teacher CHANGE type type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:grade_teacher_type)\'');
        $this->addSql('ALTER TABLE ics_access_token CHANGE type type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:ics_access_token_type)\'');
        $this->addSql('ALTER TABLE message CHANGE scope scope VARCHAR(255) NOT NULL COMMENT \'(DC2Type:message_scope)\'');
        $this->addSql('ALTER TABLE student CHANGE gender gender VARCHAR(255) NOT NULL COMMENT \'(DC2Type:gender)\'');
        $this->addSql('ALTER TABLE study_group CHANGE type type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:study_group_type)\'');
        $this->addSql('ALTER TABLE teacher CHANGE gender gender VARCHAR(255) NOT NULL COMMENT \'(DC2Type:gender)\'');
        $this->addSql('ALTER TABLE user CHANGE user_type user_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:user_type)\'');
        $this->addSql('ALTER TABLE user_type_entity CHANGE user_type user_type VARCHAR(255) NOT NULL COMMENT \'(DC2Type:user_type)\'');

        $this->removeSettings();
    }
}
