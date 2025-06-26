<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Entity\StudentInformationType;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626114809 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('RENAME TABLE book_student_information TO student_information');
        $this->addSql('RENAME TABLE book_student_information_audit TO student_information_audit');
        $this->addSql('ALTER TABLE student_information ADD COLUMN `type` VARCHAR(255) NOT NULL');
        $this->addSql('UPDATE student_information SET `type` = ?', [ StudentInformationType::Lessons->value ]);
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE student_information DROP COLUMN `type`');
        $this->addSql('RENAME TABLE student_information TO book_student_information');
        $this->addSql('RENAME TABLE student_information_audit TO book_student_information_audit');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
