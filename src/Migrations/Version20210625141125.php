<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210625141125 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE message_poll_usertypes (message_id INT UNSIGNED NOT NULL, user_type_entity_id INT UNSIGNED NOT NULL, INDEX IDX_EBD4C85E537A1329 (message_id), INDEX IDX_EBD4C85E5E66E314 (user_type_entity_id), PRIMARY KEY(message_id, user_type_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_poll_studygroups (message_id INT UNSIGNED NOT NULL, study_group_id INT UNSIGNED NOT NULL, INDEX IDX_43EBFC79537A1329 (message_id), INDEX IDX_43EBFC795DDDCCCE (study_group_id), PRIMARY KEY(message_id, study_group_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_poll_choice (id INT UNSIGNED AUTO_INCREMENT NOT NULL, message_id INT UNSIGNED DEFAULT NULL, label VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, mininum INT NOT NULL, maximum INT NOT NULL, uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:uuid)\', INDEX IDX_4DD14730537A1329 (message_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_poll_vote (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED DEFAULT NULL, message_id INT UNSIGNED DEFAULT NULL, assigned_choice_id INT UNSIGNED DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_87EF8888A76ED395 (user_id), INDEX IDX_87EF8888537A1329 (message_id), INDEX IDX_87EF8888E027D373 (assigned_choice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message_poll_vote_ranked_choice (id INT UNSIGNED AUTO_INCREMENT NOT NULL, vote_id INT UNSIGNED DEFAULT NULL, choice_id INT UNSIGNED DEFAULT NULL, rank INT NOT NULL, INDEX IDX_2BE71E5372DCDAFC (vote_id), INDEX IDX_2BE71E53998666D1 (choice_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE message_poll_usertypes ADD CONSTRAINT FK_EBD4C85E537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_usertypes ADD CONSTRAINT FK_EBD4C85E5E66E314 FOREIGN KEY (user_type_entity_id) REFERENCES user_type_entity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_studygroups ADD CONSTRAINT FK_43EBFC79537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_studygroups ADD CONSTRAINT FK_43EBFC795DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_choice ADD CONSTRAINT FK_4DD14730537A1329 FOREIGN KEY (message_id) REFERENCES message (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888537A1329 FOREIGN KEY (message_id) REFERENCES message (id)');
        $this->addSql('ALTER TABLE message_poll_vote ADD CONSTRAINT FK_87EF8888E027D373 FOREIGN KEY (assigned_choice_id) REFERENCES message_poll_choice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_vote_ranked_choice ADD CONSTRAINT FK_2BE71E5372DCDAFC FOREIGN KEY (vote_id) REFERENCES message_poll_vote (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message_poll_vote_ranked_choice ADD CONSTRAINT FK_2BE71E53998666D1 FOREIGN KEY (choice_id) REFERENCES message_poll_choice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD is_poll_enabled TINYINT(1) NOT NULL, ADD allow_poll_revote TINYINT(1) NOT NULL, ADD poll_num_choices INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE message_poll_vote DROP FOREIGN KEY FK_87EF8888E027D373');
        $this->addSql('ALTER TABLE message_poll_vote_ranked_choice DROP FOREIGN KEY FK_2BE71E53998666D1');
        $this->addSql('ALTER TABLE message_poll_vote_ranked_choice DROP FOREIGN KEY FK_2BE71E5372DCDAFC');
        $this->addSql('DROP TABLE message_poll_usertypes');
        $this->addSql('DROP TABLE message_poll_studygroups');
        $this->addSql('DROP TABLE message_poll_choice');
        $this->addSql('DROP TABLE message_poll_vote');
        $this->addSql('DROP TABLE message_poll_vote_ranked_choice');
        $this->addSql('ALTER TABLE message DROP is_poll_enabled, DROP allow_poll_revote, DROP poll_num_choices');
    }
}
