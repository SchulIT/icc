<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220815161113 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE student_absence_type_allowed_usertypes (student_absence_type_id INT UNSIGNED NOT NULL, user_type_entity_id INT UNSIGNED NOT NULL, INDEX IDX_70073A88506E213D (student_absence_type_id), INDEX IDX_70073A885E66E314 (user_type_entity_id), PRIMARY KEY(student_absence_type_id, user_type_entity_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE student_absence_type_allowed_usertypes ADD CONSTRAINT FK_70073A88506E213D FOREIGN KEY (student_absence_type_id) REFERENCES student_absence_type (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE student_absence_type_allowed_usertypes ADD CONSTRAINT FK_70073A885E66E314 FOREIGN KEY (user_type_entity_id) REFERENCES user_type_entity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE student_absence_type_allowed_usertypes');
    }
}
