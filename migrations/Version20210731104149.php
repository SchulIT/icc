<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210731104149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    private array $documentGradesMap = [ ];

    public function preUp(Schema $schema): void {
        $result = $this->connection->executeQuery('SELECT document_id, grade_id FROM document_studygroups sg LEFT JOIN study_group_grades sgg ON sg.study_group_id = sgg.study_group_id');
        while(($row = $result->fetchAssociative()) !== false) {
            $documentId = intval($row['document_id']);
            $gradeId = intval($row['grade_id']);
            if (!isset($this->documentGradesMap[$documentId])) {
                $this->documentGradesMap[$documentId] = [];
            }

            $this->documentGradesMap[$documentId][] = $gradeId;
        }
    }

    public function postUp(Schema $schema): void {
        foreach($this->documentGradesMap as $documentId => $gradeIds) {
            foreach($gradeIds as $gradeId) {
                $stmt = $this->connection->prepare('INSERT INTO document_grades (document_id, grade_id) VALUES(?, ?)');
                $stmt->bindValue(1, $documentId, 'integer');
                $stmt->bindValue(2, $gradeId, 'integer');
                $stmt->executeQuery();
            }
        }
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_grades (document_id INT UNSIGNED NOT NULL, grade_id INT UNSIGNED NOT NULL, INDEX IDX_8481D076C33F7837 (document_id), INDEX IDX_8481D076FE19A1A8 (grade_id), PRIMARY KEY(document_id, grade_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE document_grades ADD CONSTRAINT FK_8481D076C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_grades ADD CONSTRAINT FK_8481D076FE19A1A8 FOREIGN KEY (grade_id) REFERENCES grade (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE document_studygroups');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE document_studygroups (document_id INT UNSIGNED NOT NULL, study_group_id INT UNSIGNED NOT NULL, INDEX IDX_A786B266C33F7837 (document_id), INDEX IDX_A786B2665DDDCCCE (study_group_id), PRIMARY KEY(document_id, study_group_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE document_studygroups ADD CONSTRAINT FK_A786B2665DDDCCCE FOREIGN KEY (study_group_id) REFERENCES study_group (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE document_studygroups ADD CONSTRAINT FK_A786B266C33F7837 FOREIGN KEY (document_id) REFERENCES document (id) ON DELETE CASCADE');
        $this->addSql('DROP TABLE document_grades');
    }
}
